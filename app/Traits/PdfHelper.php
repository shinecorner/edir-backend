<?php namespace App\Traits;

use App\Jobs\UploadFileJob;
use App\Models\Company;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use App\Models\CompanyFile;

trait PdfHelper
{

	public function filesJson (Company $company)
	{
		$company_files = $company->files;
		$filesystem = new Filesystem();

		$files = $company_files->map(function ($file) use ($filesystem) {
			$filePath = storage_path('app/public') . DIRECTORY_SEPARATOR . $file->name;

			if($filesystem->isFile($filePath)) {
				return [
					'name' => basename($filePath),
					'type' => $file->mime, //$filesystem->mimeType($filePath),
					'size' => $file->size, //$filesystem->size($filePath),
					'file' => '/storage/'.$file->name,
				];
			}

			return [];
		});

		return $files->toJson();
	}


	/**
	 * @param $data
	 * @param $request_list
	 * @param $request_files
	 */
	public function storeAndSyncFiles($data, $request_list, $request_files)
	{
		// resync pdfs, delete removed pdfs..
		if ($request_list) {

			$files = json_decode($request_list);

			$filesToCheck = collect($files)->map(function ($pdf) {
				return str_replace ('/storage/', '', $pdf);
			});

			$current_files = $data->files()->pluck('name');

			$diff = $current_files->diff($filesToCheck);

			if ($diff->count() > 0) {
				$diff->each(function ($file) {
					$this->deletePdfFile($file);
					CompanyFile::where('name', $file)->delete();
				});
			}
		}

		if ($request_files && $data->files()->count() < CompanyFile::FILE_LIMIT) {
			$filesystem = new Filesystem();
			collect($request_files)->each(function ($file) use ($data, $filesystem) {
				if ($file->isValid()) {
					$uploadedFile = $file;
					$filename = $file->getClientOriginalName();
					$storageDir = sprintf('company/%s/%s/%s', date('y'), date('m'), date('d'));

					if(Storage::disk('public')->exists($storageDir.'/'.$filename)) {
						$filename = basename($file->getClientOriginalName(), '.pdf').'-'.str_random(5).'.'.$file->getClientOriginalExtension();
					}

					$originalFile = Storage::disk('public')->putFileAs($storageDir, $uploadedFile, $filename);

					dispatch(new UploadFileJob($storageDir, storage_path('app/public/' .$originalFile),  $filename, 'public'));

					$file = new CompanyFile(['name' => $originalFile,
											 'size' => $filesystem->size($file),
											 'mime' => $filesystem->mimeType($file)]);
					$file->company()->associate($data);
					$file->save();
				}
			});
		}
	}


	/**
	 * @param $uploadedFile
	 */
	public function deletePdfFile($uploadedFile)
	{
		$filename = basename($uploadedFile);
		$storageDir = str_replace($filename, '', $uploadedFile);
		$path = storage_path('app/public/') . $storageDir;

		Storage::disk('public')->delete($storageDir . basename($uploadedFile));
		Storage::disk('s3')->delete($storageDir . basename($uploadedFile));

		//remove dir
		$files_in_directory = Storage::disk('public')->allFiles($storageDir);
		if (count($files_in_directory) == 0) {
			Storage::disk('public')->deleteDirectory($storageDir);
		}

		$files_in_directory = Storage::disk('s3')->allFiles($storageDir); //not sure if this check is necessary
		if (count($files_in_directory) == 0) {
			Storage::disk('s3')->deleteDirectory($storageDir);
		}
		return;
	}


}
