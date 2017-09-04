<?php namespace App\Services;

use App\Jobs\UploadFileJob;
use App\Models\Image as ImageDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\LaravelImageOptimizer\ImageOptimizerFacade;

class ImageResize
{

    private $filename;
    private $storageDir;
    private $resizeDir;
    private $uploadedFile;
    private $originalFile;

    /**
     * @param $data
     * @param $request
     * @param $imageType
     *
     * @return false|null|string
     */
    public function storeAndSyncImage($data, $request, $imageType)
    {
        if ($request->get('fileuploader-list-image') && $data->getOriginal('image') && !is_null($request->image)) {

            $files = json_decode($request->get('fileuploader-list-image'));

            $filesToCheck = collect($files)->map(function ($image) {
                return str_replace(['/storage/', '/100x100'], [''], $image);
            });

            $image = collect($data->getOriginal('image'));

            $diff = $image->diff($filesToCheck);

            if ($diff->count() > 0) {
                $diff->each(function ($file) use ($imageType, $data) {
                    self::deleteImageFiles($file, $imageType);
                });
            }
		} else if($request->get('fileuploader-list-image') == "[]" && is_null($request->image)) {
			$file = $data->getOriginal('image');

			if($file) {
				self::deleteImageFiles($file, $imageType);
			}

			$data->update(['image' => null]);
		}

        if ($request->image) {
            return self::make($request->file('image')[0], $imageType);
        }

        return $data->getOriginal('image');
    }

    /**
     * @param $uploadedFile
     * @param $imageType
     * @param $baseStorageDir
     */
    public function deleteImageFiles($uploadedFile, $imageType)
    {
        $this->filename = basename($uploadedFile);
        $this->storageDir = str_replace($this->filename, '', $uploadedFile);
        $this->resizeDir = storage_path('app/public/') . $this->storageDir;

        self::remove(collect($this->imageSizes($imageType))->keys(), $uploadedFile);

        return;
    }

    /**
     * @param $sizes
     * @param $uploadedFile
     */
    private function remove($sizes, $uploadedFile)
    {
    	$disks = ['public', 's3'];

    	foreach ($disks as $disk) {
			foreach ($sizes as $thumbType) {
				$path = dirname($uploadedFile) . '/' . $thumbType;
				$file = $path . '/' . basename($uploadedFile);

				Storage::disk($disk)->delete($file);

				//remove dir
				$files_in_directory = Storage::disk($disk)->allFiles($path);
				if (count($files_in_directory) == 0) {
					Storage::disk($disk)->deleteDirectory($path);
				}
			}

//        $original_file = $this->resizeDir . '/' . basename($uploadedFile);
			Storage::disk($disk)->delete($uploadedFile);

			//remove dir
			$files_in_directory = Storage::disk($disk)->allFiles(dirname($uploadedFile));
			if (count($files_in_directory) == 0) {
				Storage::disk($disk)->deleteDirectory(dirname($uploadedFile));
			}
		}
    }

    /**
     * @param $type
     *
     * @return array
     */
    private function imageSizes($type)
    {
        switch ($type) {

            case 'category-primary':
                return [
                	'100x100' => [100, 100],
                	'200x200' => [200, 200],
                    '360x360' => [360, 360], // edir-1
					'415x210' => [415, 210], //edir-9
					'415x450' => [415, 200], //edir-9
					'950x250' => [950, 250], //edir-1
					'1900x250' => [1900, 250], //edir-5, edir-7
                ];
                break;

            case 'category-secondary':
                return [
					'100x100' => [100, 100],
					'200x200' => [200, 200],
                ];
                break;

            case 'category-event':
                return [
					'100x100' => [100, 100],
					'200x200' => [200, 200],
					'265x220' => [265, 220], //edir_2 listing
					'365x265' => [365, 265], //edir_9 listing
					'370x192' => [370, 192], //edir-11
					'370x414' => [370, 414], //edir-11
					'440x250' => [440, 250], //edir-1
					'600x250' => [600, 250], //edir-3
					'950x250' => [950, 250], //edir-1
					'1900x250' => [1900, 250], //edir-5, edir-7
					'1900x1080' => [1900, 1080], //edir-14
                ];
                break;

            case 'category-deal':
                return [
					'100x100' => [100, 100],
					'200x200' => [200, 200],
					'265x220' => [265, 220], //edir_2 listing
					'365x265' => [365, 265], //edir_9 listing
					'370x192' => [370, 192], //edir-11
					'370x414' => [370, 414], //edir-11
					'440x250' => [440, 250], //edir-1
					'600x250' => [600, 250], //edir-3
					'950x250' => [950, 250], //edir-1
					'1900x250' => [1900, 250], //edir-5, edir-7
					'1900x1080' => [1900, 1080], //edir-14
                ];
                break;

            case 'blog':
                return [
					'100x100' => [100, 100],
					'200x200' => [200, 200],
                    '120x120' => [120, 120], // edir-1
                    '270x270' => [270, 270], // edir-1 listing
					'265x140' => [265, 140], //edir_2 sidebar
					'265x250' => [265, 250], //edir_2 listing
					'360x300' => [360, 300], //edir-16 front
					'370x420' => [370, 420], //edir_11 front
					'380x480' => [380, 480], //edir_9 front
					'550x344' => [550, 344], //edir_14 front
					'560x220' => [560, 220], //edir_2 prev/next
					'620x320' => [620, 320], //edir_5 front page
					'750x420' => [750, 420], //edir_5 front page
					'750x300' => [750, 300], //edir_7 front page
					'770x420' => [770, 420], //edir_11 front page
					'800x500' => [800, 500], //edir_14 front page
					'850x450' => [850, 450], //edir_9
					'870x470' => [870, 470], //edir_11
					'1140x640' => [1140, 640], //edir_2 single
					'1900x570' => [1900, 570], //edir_5 single
                ];
                break;

            case 'event':
                return [
					'100x100' => [100, 100],
					'200x200' => [200, 200],
                    '120x120' => [120, 120], // edir-1
                    '220x100' => [220, 100], // edir-1
					'265x140' => [265, 140], //edir_2 sidebar
					'265x220' => [265, 220], //edir_2 listing / category listing
					'320x320' => [320, 320], //edir_2 single
					'360x300' => [360, 300], //edir-4 listing, edir-2 listing
					'365x220' => [365, 220], //edir_2 listing
					'380x480' => [380, 480], //edir_9 front
					'460x220' => [460, 220], //edir_2 listing
					'560x220' => [560, 220], //edir_2 listing
					'750x480' => [750, 480], //edir_5 single
					'1900x500' => [1900, 500], //edir_7, edir_8 single
                ];
                break;

            case 'deal':
                return [
					'100x100' => [100, 100],
					'200x200' => [200, 200],
                    '120x120' => [120, 120], // edir-1
                    '220x100' => [220, 100], // edir-1
					'265x140' => [265, 140], //edir_2 sidebar
					'265x220' => [265, 220], //edir_2 listing / category listing
					'280x275' => [280, 275], //edir_16 home
					'280x560' => [280, 560], //edir_16 home
					'320x320' => [320, 320], //edir_2 single
					'360x300' => [360, 300], // edir-4 listing, edir-2 listing
					'365x220' => [365, 220], //edir_2 listing, edir-9 listing
					'380x480' => [380, 480], //edir_9 front
					'380x240' => [380, 240], //edir_11 front
					'460x220' => [460, 220], //edir_2 listing
					'560x220' => [560, 220], //edir_2 listing
					'570x275' => [570, 275], //edir_16 home
					'750x480' => [750, 480], //edir_5 single
					'1900x500' => [1900, 500], //edir_7, edir_8 single
                ];
                break;

            case 'company':
                return [
					'100x100' => [100, 100],
                    '200x200' => [200, 200], // edir-1
                    '120x120' => [120, 120], // edir-1
                    '220x100' => [220, 100], // edir-1
                    '270x270' => [270, 270], // edir-1
                    '265x140' => [265, 140], //edir_7 sidebar
                    '265x220' => [265, 220], //edir_2 rating front page
					'360x300' => [360, 300], // edir-4 listing, edir-2 listing
                    '420x200' => [420, 200], //edir_2 single
					'460x220' => [460, 220], //edir_9 listing
                    '620x320' => [620, 320], //edir_5 front page
                    '1900x500' => [1900, 500], //edir_7, edir_8 single
                ];
                break;

            case 'gallery':
                return [
					'100x100' => [100, 100],
					'200x200' => [200, 200],
					'400x350' => [400, 350], // edir-14
					'650x430' => [650, 430], // edir-1
					'550x360' => [550, 360], //edir_2 full width slider
					'750x360' => [750, 360], //edir_2 single image slider
                    // ar 16:9
                    '765x430' => [765, 430], //?
					'750x480' => [750, 480], //edir_5 single
					'870x480' => [870, 480], //edir_11 single
					'920x480' => [920, 480], //edir_4 single,
                ];
                break;
        }
    }

    /**
     * @param $imageType
     *
     * @return false|string
     */
    public function make($uploadedFile, $imageType)
    {
        if ($uploadedFile->isValid()) {
            $this->uploadedFile = $uploadedFile;
            $this->storageDir = sprintf('/%s/%s/%s/%s', $imageType, date('y'), date('m'), date('d'));
            $this->resizeDir = storage_path('app/public') . $this->storageDir;
            $this->originalFile = Storage::disk('public')->putFile($this->storageDir, $this->uploadedFile);

            //alias not working?
			ImageOptimizerFacade::optimize(storage_path('app/public/' . $this->originalFile));

			dispatch(new UploadFileJob($this->storageDir, storage_path('app/public/' . $this->originalFile), basename($this->originalFile), 'public'));
//            $this->awsOriginalFile = Storage::disk('s3')->putFile($this->storageDir, $this->uploadedFile, 'public');
//            dd(Storage::disk('s3')->url($this->awsOriginalFile));

            self::storeAndResize($this->imageSizes($imageType));

            return $this->originalFile;
        }

        return response('Invalid file', 500);
    }

    /**
     * @param $sizes
     */
    private function storeAndResize($sizes)
    {
        foreach ($sizes as $thumbType => $widthHeight) {

            $resizedFile = $this->resizeDir . '/' . $thumbType . '/' . basename($this->originalFile);
//            $publicFile = $this->storageDir . '/' . $thumbType . '/' . basename($this->originalFile);

            if (!is_dir($this->resizeDir . '/' . $thumbType)) {
                File::makeDirectory($this->resizeDir . '/' . $thumbType);
            }

            list($width, $height) = $widthHeight;

            Image::make(storage_path('app/public/' . $this->originalFile))
                ->fit($width, $height)
                ->save($resizedFile);

			dispatch(new UploadFileJob($this->storageDir . '/' . $thumbType, $resizedFile, basename($resizedFile), 'public'));
        }
    }

    /**
     * @param $data
     * @param $request
     * @param $imageType
     */
    public function storeAndSyncGallery($data, $request, $imageType)
    {
        // resync images, delete removed images..
        if ($request->get('fileuploader-list-image_gallery')) {

            $files = json_decode($request->get('fileuploader-list-image_gallery'));
            $filesToCheck = collect($files)->map(function ($image) {
                return str_replace(['/storage/', '/100x100'], [''], $image);
            });

            $galleryImages = $data->gallery_images()->pluck('image');

            $diff = $galleryImages->diff($filesToCheck);

            if ($diff->count() > 0) {
                $diff->each(function ($file) use ($imageType) {
                    self::deleteImageFiles($file, $imageType);
                    ImageDatabase::where('image', $file)->delete();
                });
            }
        }

        if ($request->image_gallery && $data->gallery_images()->count() < ImageDatabase::GALLERY_IMAGES_LIMIT) {
            collect($request->image_gallery)->each(function ($image) use ($data, $imageType) {
                $path = self::make($image, $imageType);
                $gallery_image = new ImageDatabase(['image' => $path]);
                $gallery_image->imageable()->associate($data);
                $gallery_image->save();
            });
        }
    }
}