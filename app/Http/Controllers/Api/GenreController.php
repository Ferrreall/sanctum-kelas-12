<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class GenreController extends Controller
{
    public function index()
    {
        try {
            $genre = Genre::latest()->get();

            if ($genre->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No genres found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Genres retrieved successfully',
                'data' => $genre,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_genre' => 'required|string|unique:genres,nama_genre',
            ]);
            $genre             = new Genre();
            $genre->nama_genre = $request->nama_genre;
            // Str disini digunakan agar apa yang kita input jadi ada stripnya
            // Contoh Slice Of LIfe menjasdi slice-of-life
            // Ini akan memudahkan jika kita memanggil nya untuk kebutuhan api
            $genre->slug       = Str::slug($request->nama_genre) . Str::random(10);
            $genre->save();

            return response()->json([
                'status' => true,
                'message' => 'Genre created successfully',
                'data' => $genre,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $genre = Genre::find($id);
            if (!$genre) {
                return response()->json([
                    'status' => false,
                    'message' => 'Genre not found',
                ], 404);
            }

            $request->validate([
                'nama_genre' => 'required|string|unique:genres,nama_genre,' . $id,
            ]);
            $genre             = Genre::find($id);
            $genre->nama_genre = $request->nama_genre;
            $genre->slug       = Str::slug($request->nama_genre) . Str::random(10);
            $genre->save();

            return response()->json([
                'status' => true,
                'message' => 'Genre updated successfully',
                'data' => $genre,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $genre = Genre::find($id);
            if (!$genre) {
                return response()->json([
                    'status' => false,
                    'message' => 'Genre not found',
                ], 404);
            }
            $genre->delete();

            return response()->json([
                'status' => true,
                'message' => 'Genre deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
