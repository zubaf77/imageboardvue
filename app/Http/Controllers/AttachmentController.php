<?php

namespace App\Http\Controllers;

use App\Components\SaveMedia;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $board_id = null, $thread_id = null)
    {
        $validateData = $request->validate([
            'post_id' => 'required',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt,zip,rar|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $service = app(SaveMedia::class);
            $service->file = $request->file('file');
            $service->post_id = $request->post_id;

            if ($thread_id) {
                $service->thread_id = $thread_id;
            } elseif ($board_id) {
                $service->board_id = $board_id;
            }

            $service->save();
        }

        return response()->json(['success' => 'File uploaded successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, string $board_id = null, $thread_id = null)
    {
        $validateData = $request->validate([
            'post_id' => 'required',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt,zip,rar|max:10240',
        ]);

        $attachment = Attachment::findOrFail($id);

        if ($attachment->file) {
            Storage::delete($attachment->file);
        }

        if ($request->hasFile('file')) {
            $service = app(SaveMedia::class);
            $service->file = $request->file('file');
            $service->post_id = $request->post_id;

            if ($thread_id) {
                $service->thread_id = $thread_id;
            } elseif ($board_id) {
                $service->board_id = $board_id;
            }

            $service->save();
        }

        return response()->json(['success' => 'File updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attachment = Attachment::findOrFail($id);

        if ($attachment->file) {
            Storage::delete($attachment->file);
        }

        $attachment->delete();

        return response()->json(['success' => 'File deleted successfully']);
    }
}
