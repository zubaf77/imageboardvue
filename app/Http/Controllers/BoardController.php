<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boards = Board::latest()->get();
        return Inertia::render('Boards', [
            'boards' => $boards,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Boards/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'slug' => 'required|unique:boards,slug',
            'name' => 'required',
        ]);

        if (auth('admin')->check() && (auth('admin')->user()->isAdmin() || auth('admin')->user()->isOwner())) {
            Board::create($validatedData);
            return redirect()->route('admin.boards.index');
        }

        return redirect()->route('home')->with('error', 'У вас нет прав для создания доски.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $board = Board::findOrFail($id);
        return Inertia::render('Boards/Show', ['board' => $board]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $board = Board::findOrFail($id);
        return Inertia::render('Boards/Edit', ['board' => $board]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'slug' => 'required',
            'name' => 'required',
        ]);

        $board = Board::findOrFail($id);

        if (auth('admin')->check() && auth('admin')->user()->isOwner()) {
            $board->update($validatedData);
            return redirect()->route('admin.boards.index')->with('success', 'Board updated successfully');
        }

        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(auth('admin')->check() && auth('admin')->user()->isOwner()) {
            $board = Board::findOrFail($id);
            $board->delete();
            return redirect()->route('admin.boards.index')->with('success', 'Board deleted successfully');
        }
        abort(403);
    }
}
