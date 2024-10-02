<?php

namespace App\Http\Controllers\Api;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::all();
        return response()->json([
            'message' => 'success',
            'data' => $notes
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'is_pinned' => 'required',
        ]);
        $note = Note::create($request->all());
        //if request has image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . "." . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $note->image = $imageName;
            $note->save();
        }
        //find the created note
        $note = Note::where('id', $note->id)->first();
        return response()->json([
            'message' => 'success',
            'data' => $note
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Validasi input dengan aturan lebih spesifik
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_pinned' => 'required|boolean',
            'image' => 'nullable|file|image|max:2048', // Validasi file gambar
        ]);

        // Cari Note berdasarkan id
        $note = Note::find($id);
        if (!$note) {
            return response()->json([
                'message' => 'Note not found'
            ], 404);
        }

        // Hanya update field yang diperlukan
        $note->title = $request->input('title');
        $note->content = $request->input('content');
        $note->is_pinned = $request->input('is_pinned');

        // Update jika ada file gambar
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Pastikan path target ada dan aman
            $image->move(public_path('images'), $imageName);

            // Simpan nama file gambar
            $note->image = $imageName;
        }

        // Simpan perubahan pada note
        $note->save();

        // Kembalikan respon sukses
        return response()->json([
            'message' => 'success',
            'data' => $note
        ], 200);
    }

    public function destroy($id)
    {
        $note = Note::find($id);
        if (!$note) {
            return response()->json([
                'message' => 'Note not found'
            ], 404);
        }
        $note->delete();
        return response()->json([
            'message' => 'Note deleted succesfully',
        ], 200);
    }
}
