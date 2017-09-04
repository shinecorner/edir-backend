<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Thread;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MessagesController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
	}
		/**
	 * Show all of the message threads to the user.
	 *
	 * @return mixed
	 */
	public function index()
	{
		$threads = Thread::withTrashed()->forUser(auth()->user()->id)->orderBy('deleted_at')->latest('updated_at')->get();
		$threads_open_count = Thread::forUser(auth()->user()->id)->count();
		$threads_closed_count = Thread::onlyTrashed()->forUser(auth()->user()->id)->count();

		return view('messenger.index', compact('threads', 'threads_open_count', 'threads_closed_count'));
	}

	/**
	 * Shows a message thread.
	 *
	 * @param $id
	 * @return mixed
	 */
	public function show($slug)
	{
		try {
			$conversation = Thread::withTrashed()->whereHas('participants', function($query) {
				$query->where('user_id', auth()->user()->id);
			})->whereSlug($slug)->firstOrFail();

			$conversation->markAsRead(auth()->user()->id);
		} catch (ModelNotFoundException $e) {
			alert()->warning(trans('messages.chat.notfound'));

			return redirect('messages');
		}

		$threads = Thread::withTrashed()->forUser(auth()->user()->id)->orderBy('deleted_at')->latest('updated_at')->get();
		$threads_open_count = Thread::forUser(auth()->user()->id)->count();
		$threads_closed_count = Thread::onlyTrashed()->forUser(auth()->user()->id)->count();

		return view('messenger.index', compact('conversation', 'threads', 'threads_open_count', 'threads_closed_count'));
	}


	/**
	 * Stores a new message thread.
	 *
	 * @return mixed
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'subject' => 'required|max:60',
			'message' => 'required|max:1000',
		]);

		$thread = Thread::updateOrCreate(
			[
				'id'	=> $request->id,
				'subject' => $request->subject,
			]
		);

		//add message only if new thread or user is alread participant
		if(! $thread->participants->count() or $thread->hasParticipant(auth()->user()->id)) {
			// Message
			Message::create(
				[
					'thread_id' => $thread->id,
					'user_id' => auth()->user()->id,
					'body' => $request->message,
				]
			);

			// Sender
			Participant::updateOrCreate(
				[
					'thread_id' => $thread->id,
					'user_id' => auth()->user()->id,
				], [
					'last_read' => new Carbon,
				]
			);

			//$thread->activateAllParticipants(); //probably not needed
			// Recipients
			$thread->addParticipant(User::where('role', 'admin')->orWhere('role', 'employee')->pluck('id')->toArray());

			//Admin/Staff can message user
			if($request->user_id && auth()->user()->can('manage-directory')) {
				$thread->addParticipant($request->user_id);
			}
		}

		return redirect('messages/' . $thread->slug);
	}


	/**
	 * Delete/Close a message thread.
	 *
	 * @param $slug
	 * @return mixed
	 */
	public function destroy($slug)
	{
		try {
			$conversation = Thread::whereHas('participants', function($query) {
				$query->where('user_id', auth()->user()->id);
			})->whereSlug($slug)->firstOrFail();
			$conversation->delete();
		} catch (ModelNotFoundException $e) {
			alert()->warning(trans('messages.chat.notfound'));

			return redirect('messages');
		}

		$threads = Thread::withTrashed()->forUser(auth()->user()->id)->orderBy('deleted_at')->latest('updated_at')->get();
		$threads_open_count = Thread::forUser(auth()->user()->id)->count();
		$threads_closed_count = Thread::onlyTrashed()->forUser(auth()->user()->id)->count();

		return view('messenger.index', compact('threads', 'threads_open_count', 'threads_closed_count'));
	}

	/**
	 * Undelete/Open a message thread.
	 *
	 * @param $slug
	 * @return mixed
	 */
	public function undelete($slug)
	{
		try {
			$conversation = Thread::withTrashed()->whereHas('participants', function($query) {
				$query->where('user_id', auth()->user()->id);
			})->whereSlug($slug)->firstOrFail();
			$conversation->restore();
		} catch (ModelNotFoundException $e) {
			alert()->warning(trans('messages.chat.notfound'));

			return redirect('messages');
		}

		$threads = Thread::withTrashed()->forUser(auth()->user()->id)->orderBy('deleted_at')->latest('updated_at')->get();
		$threads_open_count = Thread::forUser(auth()->user()->id)->count();
		$threads_closed_count = Thread::onlyTrashed()->forUser(auth()->user()->id)->count();

		return view('messenger.index', compact('threads', 'threads_open_count', 'threads_closed_count'));
	}
}