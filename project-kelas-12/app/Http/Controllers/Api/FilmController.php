<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class FilmController extends Controller
{
    public function index()
    {
        try {
            $films = Film::with(['genre', 'aktors'])->get();

            if ($films->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No films found.'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'List of films retrieved successfully.',
                'data' => $films
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'durasi' => 'required|integer|min:1',
                'rating' => 'required|numeric|between:0,10',
                'deskripsi' => 'required|string',
                'tanggal_rilis' => 'required|date',
                'poster' => 'nullable|string',
                'genre_id' => 'required|exists:genres,id',
                'sutradara' => 'required|string|max:255',
                'aktor_id' => 'nullable|array',
                'aktor_id.*' => 'exists:aktors,id',
            ]);

            $film = new Film();
            $film->judul = $request->judul;
            $film->durasi = $request->durasi;
            $film->rating = $request->rating;
            $film->deskripsi = $request->deskripsi;
            $film->tanggal_rilis = $request->tanggal_rilis;
            $film->genre_id = $request->genre_id;
            $film->sutradara = $request->sutradara;
            $film->slug = Str::slug($request->judul) . '-' . Str::random(10);

            if ($request->hasFile('poster')) {
                $file = $request->file('poster');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/posters'), $filename);
                $film->poster = 'uploads/posters/' . $filename;
            }

            $film->save();

            if ($request->has('aktor_id')) {
                $film->aktors()->attach($request->aktor_id);
            }

            return response()->json([
                'status' => true,
                'message' => 'Film created successfully.',
                'data' => $film->load(['genre', 'aktors'])
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'durasi' => 'required|integer|min:1',
                'rating' => 'required|numeric|between:0,10',
                'deskripsi' => 'required|string',
                'tanggal_rilis' => 'required|date',
                'poster' => 'nullable|string',
                'genre_id' => 'required|exists:genres,id',
                'sutradara' => 'required|string|max:255',
                'aktor_id' => 'nullable|array',
                'aktor_id.*' => 'exists:aktors,id',
            ]);

            $film = Film::find($request->id);
            if (!$film) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film not found.'
                ], 404);
            }

            $film->judul = $request->judul;
            $film->durasi = $request->durasi;
            $film->rating = $request->rating;
            $film->deskripsi = $request->deskripsi;
            $film->tanggal_rilis = $request->tanggal_rilis;
            $film->genre_id = $request->genre_id;
            $film->sutradara = $request->sutradara;
            $film->slug = Str::slug($request->judul) . '-' . Str::random(10);

            if ($request->hasFile('poster')) {
                // Delete old poster if exists
                if ($film->poster && file_exists(public_path($film->poster))) {
                    unlink(public_path($film->poster));
                }
                $file = $request->file('poster');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/posters'), $filename);
                $film->poster = 'uploads/posters/' . $filename;
            }

            $film->save();

            if ($request->has('aktor_id')) {
                $film->aktors()->sync($request->aktor_id);
            }

            return response()->json([
                'status' => true,
                'message' => 'Film updated successfully.',
                'data' => $film->load(['genre', 'aktors'])
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $film = Film::with(['genre', 'aktors'])->find($id);
            if (!$film) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film not found.'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Film retrieved successfully.',
                'data' => $film
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $film = Film::find($id);
            if (!$film) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film not found.'
                ], 404);
            }

            // Delete poster if exists
            if ($film->poster && file_exists(public_path($film->poster))) {
                unlink(public_path($film->poster));
            }

            $film->aktors()->detach();

            $film->delete();

            return response()->json([
                'status' => true,
                'message' => 'Film deleted successfully.',
                'data' => $film
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
