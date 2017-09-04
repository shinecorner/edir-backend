<?php namespace App\Traits;

use Illuminate\Filesystem\Filesystem;

trait ImageHelper
{
    /**
     * @param $size
     * @param bool $local
     *
     * @return null|string
     */
	public function image($size)
	{
	    $publicFile = null;

		if (isset($this->image)) {
			$filename = basename($this->image);

			if(config('filesystems.disks.s3.serve')) {
                $publicFile = config('edir.aws_url') . str_replace($filename, $size . '/' . $filename, $this->image);
            } else {
                $publicFile = '/storage/' . str_replace($filename, $size . '/' . $filename, $this->image);
            }

            if ($size == 'original') {
                return '/storage/' . $this->image;
            }

            return $publicFile;
		}

		return '/img/no-image-placeholder.png';
	}

    /**
     * @param $type
     * @param bool $local
     *
     * @return string
     */
    public function imageJson($type, $local = false)
    {
        $images = null;

        switch ($type) {
			case 'image':
				$images = collect($this->image);
				break;
			case 'gallery':
				$images = $this->gallery_images;
				break;
		}

        $images = $images->map(function ($image) use ($local) {

            $filesystem = new Filesystem();
            $imagePath = storage_path('app/public') . DIRECTORY_SEPARATOR . (is_object($image) ? $image->image : $image);

            return [
                'name' => basename($image),
                'type' => $filesystem->mimeType($imagePath),
                'size' => $filesystem->size($imagePath),
                'file' => is_object($image) ? $image->image('100x100') : $this->image('100x100')
                // only needed for download
                /*'data' => [
                    'url' => $image->image('100x100'),
                ]*/
            ];
        });

        return $images->toJson();
	}

    /**
     * @return mixed
     */
    public function galleryJson($type = null)
    {
        $images = $this->gallery_images->map(function ($image) use ($type) {

            $filesystem = new Filesystem();
            $imagePath = storage_path('app/public') . DIRECTORY_SEPARATOR . $image->image;

            return [
                'name' => basename($image),
                'type' => $filesystem->mimeType($imagePath),
                'size' => $filesystem->size($imagePath),
                'file' => $image->image('100x100'),
                // only needed for download
                /*'data' => [
                    'url' => $image->image('100x100'),
                ]*/
            ];

        });

        return $images->toJson();
	}
}
