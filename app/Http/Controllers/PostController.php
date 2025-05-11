<?php

namespace App\Http\Controllers;

use App\Components\SaveMedia;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($thread_id)
    {
        $thread = Thread::with('attachments', 'user')->findOrFail($thread_id);

        $posts = Post::with([
            'attachments',
            'replies.attachments',
            'replies.user',
            'parentPost',
            'user',
        ])
            ->where('thread_id', $thread_id)
            ->whereNull('parent_post_id')
            ->latest()
            ->paginate(15);

        return Inertia::render('Post', [
            'thread' => $thread,
            'posts' => $posts,
            'board' => $thread->board,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'thread_id' => 'required|exists:threads,id',
            'media.*' => 'nullable|file|mimes:jpeg,png,gif,svg,pdf,zip|max:10240',
            'parent_post_id' => 'nullable|exists:posts,id',
        ]);

        $validated['ip'] = $request->ip();

        if(auth('admin')->check())
        {
            $validated['user_id'] = auth('admin')->id();
        }

        $post = Post::create($validated);

        if ($request->hasFile('media')) {
            $service = app(SaveMedia::class);
            $service->file = $request->file('media');
            $service->post_id = $post->id;
            $service->save();
        }

        return redirect()->route('posts.index', $post->thread->id);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id);
        return Inertia::render('Posts/Show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::findOrFail($id);
        return Inertia::render('Posts/Edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateData = $request->validate([
            'content' => 'string|required',
            'thread_id' => 'required|exists:threads,id',
            'media.*' => 'nullable|file|mimes:jpeg,png,gif,svg,pdf,zip|max:10240',
        ]);

        $post = Post::findOrFail($id);

        if($post->ip != $request->ip() || !auth('admin')->check())
        {
            abort(403);
        }

        $post->update($validateData);

        if ($request->hasFile('media')) {
            if ($post->hasMedia('media')) {
                $mediaFiles = $post->getMedia('media')->pluck('file_name')->toArray();
                Storage::delete($mediaFiles);
                $post->media()->delete();
            }

            $service = app(SaveMedia::class);
            $service->file = $request->file('media');
            $service->post_id = $post->id;
            $service->save();
        }

        return Inertia::location(route('threads.show', $post->thread->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);


        if (!auth('admin')->check()) {
            abort(403);
        }

        $service = app(SaveMedia::class);
        $service->post_id = $post->id;

        if ($post->attachments->isNotEmpty()) {
            $service->deleteMedia();
        }

        $post->delete();

        return back()->with('success', 'Post has been deleted');

    }
}
