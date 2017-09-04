<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Http\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class UploadFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file_path;
    protected $directory;
    protected $visibility;
    protected $filename;

    /**
     * UploadFileJob constructor.
     *
     * @param $directory
     * @param $file_path
     * @param $filename
     * @param string $visibility
     */
    public function __construct($directory, $file_path, $filename, $visibility = 'public')
    {
        $this->file_path = $file_path;
        $this->directory = $directory;
        $this->visibility = $visibility;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Storage::disk('s3')->putFileAs(
            $this->directory,
            new File($this->file_path),
            $this->filename,
            $this->visibility
        );
    }
}
