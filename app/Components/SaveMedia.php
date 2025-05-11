<?php

namespace App\Components;


use App\Models\Attachment;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\get;
use function Termwind\render;

class SaveMedia
{


    public $file = [];

    public $thread_id;

    public $post_id;

    public function save(bool $withFlash = true)
    {
        $files = [];
        if (empty($this->file)) {
            if ($withFlash) session()->flash('error', 'Файл не валиден');
            return false;
        }
        foreach ($this->file as $file) {
            if ($file->isValid()) {
                $filePath = $file->store('files', 'public');
                $fileType = $file->getMimeType();

                $data = ['file_path' => $filePath, 'file_type' => $fileType, 'ip' => request()->ip()];

                if ($this->thread_id) {
                    $data['thread_id'] = $this->thread_id;
                }
                if ($this->post_id) {
                    $data['post_id'] = $this->post_id;
                }
                Attachment::create($data);
                $files[] = $filePath;
            }
        }
        if (!empty($files)) {
            if ($withFlash) session()->flash('success', 'Your attachments have been saved');
        } else {
            if ($withFlash) session()->flash('error', 'Your attachments have not been saved');
        }
        return $files;
    }


    public function getMedia()
    {
        if ($this->thread_id) {
            $thread = Thread::findOrFail($this->thread_id);
            return $thread->attachments;
        }
        if ($this->post_id) {
            $post = Post::findOrFail($this->post_id);
            return $post->attachments;
        }

        return collect();
    }

    public function deleteMedia()
    {
        if (empty($this->file)) {
            return false;
        }

        $mediaFiles = $this->getMedia();
        if ($mediaFiles->isNotEmpty()) {
            $paths = $mediaFiles->pluck('file_path')->toArray();
            Storage::disk('public')->delete($paths);

            foreach ($mediaFiles as $file) {
                $file->delete();
            }

            return true;
        }
        return false;
    }

    public function updateMedia()
    {
        if (empty($this->file)) {
            return false;
        }
        $mediaFiles = $this->getMedia();

        if ($mediaFiles->isNotEmpty())
        {
            $paths = $mediaFiles->pluck('file_path')->toArray();
            Storage::disk('public')->delete($paths);

            foreach ($this->file as $singleFile) {
                if ($singleFile->isValid()) {
                    $file_path = $singleFile->store('files', 'public');
                    $fileType = $singleFile->getMimeType();
                    foreach ($mediaFiles as $file) {
                        $file->update(['file_path' => $file_path, 'file_type' => $fileType]);
                    }
                }
            }
            return true;
        }
        return false;
    }


}

