<?php

namespace App\Http\Controllers;

use App\Components\SaveMedia;
use App\Models\Board;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ThreadController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(string $slug)
    {
        $sort = request()->query('sort', 'latest');

        $board = Board::where('slug', $slug)->firstOrFail();


        $threads = $board->threads()
            ->withCount('posts')
            ->when($sort === 'popular', function ($query) {
                $query->orderByDesc('posts_count');
            })
            ->when($sort === 'latest', function ($query) {
                $query->latest();
            })->with('user')
            ->paginate(15)->filter();

        return Inertia::render('Threads', ['board' => $board, 'threads' => $threads, 'sort' => $sort]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Threads/Create');
    }

    public function show(string $id)
    {
        $thread = Thread::with(['user', 'attachments', 'board'])->findOrFail($id);

        return Inertia::render('Post', [
            'thread' => $thread,
            'board' => $thread->board,
            'posts' => $thread->posts()->with(['user'])->paginate(15),
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validateData = $request->validate([
        'title' => 'string|required|max:255',
        'media.*' => 'nullable|file|mimes:jpeg,png,gif,svg,pdf,zip|max:10240',
        'board_id' => 'required|exists:boards,id',
    ]);
    $validateData['ip'] = $request->ip();
    $validateData['board_id'] = $request->board_id;
    if(auth('admin')->check()) {
        $validateData['user_id'] = auth('admin')->id();
    }
    $thread = Thread::create($validateData);



        if ($request->hasFile('media')) {
            $service = app(SaveMedia::class);
            $service->file = $request->file('media');
            $service->thread_id = $thread->id;
            $service->save();
        }

        return Inertia::location(route('posts.index', $thread->id));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateData = $request->validate([
            'title' => 'string|required|max:255',
            'media.*' => 'nullable|file|mimes:jpeg,png,gif,svg,pdf,zip|max:10240',
            'board_id' => 'required|exists:boards,id',
        ]);


        $thread = Thread::findOrFail($id);

        if($thread->ip != $request->ip() || !auth('admin')->check())
        {
            abort(403);
        }

        $thread->update($validateData);


        if ($request->hasFile('media')) {

            if ($thread->hasMedia('media')) {

                $mediaFiles = $thread->getMedia('media')->pluck('file_name')->toArray();
                Storage::delete($mediaFiles);
                $thread->media()->delete();
            }

            $service = app(SaveMedia::class);
            $service->file = $request->file('media');
            $service->thread_id = $thread->id;
            $service->save();
        }

        return Inertia::location(route('threads.show', $thread));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $thread = Thread::findOrFail($id);

        if (!auth('admin')->check()) {
            abort(403);
        }

        $service = app(SaveMedia::class);
        $service->thread_id = $thread->id;

        if($thread->attachments->isNotEmpty())
        {
            $service->deleteMedia();
        }

        $thread->delete();


        return back()->with('success', 'Thread has been deleted');
    }

    

}
