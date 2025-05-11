<?php

namespace App\Http\Controllers;

use App\Models\BannedIp;
use App\Models\Board;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function boardIndex()
    {
        $boards = Board::latest()->get();
        return view('admin.boards.index', compact('boards'));
    }


    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
            ]);
        if (auth('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->intended(route('admin.boards.index'));
        } else {
            abort(403);
        }
    }

    public function logout()
    {
        auth('admin')->logout();
        return redirect()->route('admin.boards.index');
    }


    public function adminPosts(Request $request)
    {
        $query = Post::with('thread', 'board');

        if ($request->filled('ip')) {
            $query->where('ip', $request->get('ip'));
        }

        if ($request->filled('thread_id')) {
            $query->where('thread_id', $request->get('thread_id'));
        }

        $posts = $query->latest()->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    public function adminThreads(Request $request)
    {
        $query = Thread::with('board');

        if ($request->filled('ip')) {
            $query->where('ip', $request->get('ip'));
        }

        if ($request->filled('board')) {
            $query->whereHas('board', function($q) use ($request) {
                $q->where('name', $request->get('board'));
            });
        }

        $threads = $query->latest()->paginate(15);

        return view('admin.threads.index', compact('threads'));
    }

    public function banByThread(Request $request, Thread $thread)
    {
        if (trim($thread->ip) === trim($request->ip())) {
            return redirect()->back()->withErrors(['ip' => 'The IP cannot be banned.']);
        }

        if (auth('admin')->check() && $thread->ip) {
            BannedIp::firstOrCreate(
                ['ip' => $thread->ip],
                ['reason' => $request->get('reason') ?? 'no comment']
            );
            return redirect()->back();
        }
        return redirect()->back();
    }

    public function banByPost(Request $request, Post $post)
    {

        if (trim($post->ip) === trim($request->ip())) {
            return redirect()->back()->withErrors(['ip' => 'The IP cannot be banned.']);
        }

        if (auth('admin')->check() && $post->ip) {
            BannedIp::firstOrCreate([
                'ip' => $post->ip],
                ['reason' => $request->get('reason') ?? 'no comment',]);
        }
        return redirect()->back();
    }

    public function unbanIP(Request $request)
    {
        if (auth('admin')->check() && $request->filled('ip')) {
            $banned = BannedIp::where('ip', $request->get('ip'))->first();

            if ($banned) {
                $banned->delete();
            }
        }
        return redirect()->back();
    }

    public function showUsers()
    {
        $users = User::get();
        return view('admin.users.index', compact('users'));
    }

    public function addUser(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|in:admin,moderator',
        ]);

        if (auth('admin')->check() && auth('admin')->user()->isOwner()) {
            User::create($validatedData);
        }
        if (auth('admin')->check() && auth('admin')->user()->isAdmin()) {
            if ($validatedData['role'] != 'moderator') {
                abort(403);
            }
            User::create($validatedData);
        }
        if(!(auth('admin')->user()->isAdmin() && auth('admin')->user()->isOwner()))
        {
            abort(403);
        }
        return redirect()->route('admin.users.index');
    }

    public function userDelete(int $id)
    {
        $targetUser = User::findOrFail($id);

        $adminUser = auth('admin')->user();

        if (!$adminUser->isOwner()) {
            abort(403);
        }

        if ($adminUser->id === $targetUser->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account');
        }

        $targetUser->delete();

        return redirect()->route('admin.users.index');

    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|confirmed|min:6',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $adminUser = auth('admin')->user();

        $adminUser->name = $request->get('name');
        if($request->get('password')){
            $adminUser->password = $request->get('password');
        }
        if($request->get('email')){
            $adminUser->email = $request->get('email');
        }
        $adminUser->save();

        return redirect()->route('admin.profile.index');
    }
    

    


}
