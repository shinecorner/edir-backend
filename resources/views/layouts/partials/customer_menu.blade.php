<div class="mobile-menu-left-overlay"></div>
<nav class="side-menu">
    <ul class="side-menu-list">
        {{--<li class="grey">--}}
            {{--<a href="{!! route('dashboard') !!}" class="label-right">--}}
                {{--<i class="font-icon font-icon-home"></i>--}}
                {{--<span class="lbl">Dashboard</span>--}}
            {{--</a>--}}
        {{--</li>--}}

        <li class="grey">
            <a href="{!! route('customer.profile') !!}" {!! request()->is('profil/*') ? 'class="active"' : '' !!}>
                <i class="font-icon font-icon-user"></i>
                <span class="lbl">Mein Profil</span>
            </a>
        </li>

		<li class="grey">
			<a href="{!! route('customer.company') !!}" {!! request()->is('firmen*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-build"></i>
				<span class="lbl">Meine Firmen</span>
			</a>
		</li>

		<li class="grey">
			<a href="{!! route('customer.event') !!}" {!! request()->is('events*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-pin-2"></i>
				<span class="lbl">Events</span>
			</a>
		</li>

		<li class="grey">
			<a href="{!! route('customer.deal') !!}" {!! request()->is('deals*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-wallet"></i>
				<span class="lbl">Deals</span>
			</a>
		</li>

		<li class="grey">
			<a href="{!! route('customer.rating') !!}" {!! request()->is('ratings*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-star"></i>
				<span class="lbl">Ratings</span>
			</a>
		</li>

		<li class="grey">
			<a href="{!! route('messages') !!}" {!! request()->is('messages*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-help"></i>
				<span class="lbl">Support
					@if(auth()->user()->newThreadsCount())
						<span class="label label-danger">{{ auth()->user()->newThreadsCount() }}</span>
					@endif
				</span>
			</a>
		</li>
    </ul>
</nav>