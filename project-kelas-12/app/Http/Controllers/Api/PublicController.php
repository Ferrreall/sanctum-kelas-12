<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PublicController extends Controller
{
    public function films()
{
    try {

        $films = DB::table('films')
            ->join('genres', 'films.genre_id', '=', 'genres.id')
            ->select(
                'films.id',
                'films.judul',
                'films.slug',
                'films.poster',
                'films.tanggal_rilis',
                'films.durasi',
                'films.sutradara',
                'genres.nama_genre'
            )
            ->orderBy('films.id', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Data film berhasil diambil.',
            'data' => $films
        ], 200);

    } catch (Exception $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}

public function detailFilm($id)
{
    try {

        $film = DB::table('films')
            ->join('genres', 'films.genre_id', '=', 'genres.id')
            ->select(
                'films.*',
                'genres.nama_genre'
            )
            ->where('films.id', $id)
            ->first();

        if (!$film) {
            return response()->json([
                'status' => false,
                'message' => 'Film tidak ditemukan.'
            ], 404);
        }

        $actors = DB::table('aktor__films')
            ->join('aktors', 'aktor__films.aktor_id', '=', 'aktors.id')
            ->where('aktor__films.film_id', $id)
            ->select(
                'aktors.id',
                'aktors.nama_aktor'
            )
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Detail film berhasil diambil.',
            'film' => $film,
            'actors' => $actors
        ], 200);

    } catch (Exception $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}

public function genres()
{
    try {

        $genres = DB::table('genres')
            ->select(
                'id',
                'nama_genre',
                'slug'
            )
            ->orderBy('nama_genre', 'asc')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Data genre berhasil diambil.',
            'data' => $genres
        ], 200);

    } catch (Exception $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}

public function filmByGenre($id)
{
    try {

        $genre = DB::table('genres')
            ->where('id', $id)
            ->first();

        if (!$genre) {

            return response()->json([
                'status' => false,
                'message' => 'Genre tidak ditemukan.'
            ], 404);

        }

        $films = DB::table('films')
            ->join('genres', 'films.genre_id', '=', 'genres.id')
            ->where('genres.id', $id)
            ->select(
                'films.id',
                'films.judul',
                'films.slug',
                'films.poster',
                'films.tanggal_rilis',
                'films.durasi',
                'films.sutradara',
                'genres.nama_genre'
            )
            ->paginate(10);

        return response()->json([
            'status' => true,
            'genre' => $genre,
            'data' => $films
        ], 200);

    } catch (Exception $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}

public function actors()
{
    try {

        $actors = DB::table('aktors')
            ->select(
                'id',
                'nama_aktor',
                'jenis_kelamin',
                'tanggal_lahir',
                'umur',
                'foto'
            )
            ->orderBy('nama_aktor', 'asc')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Data aktor berhasil diambil.',
            'data' => $actors
        ], 200);

    } catch (Exception $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}

public function filmByActor($id)
{
    try {

        $actor = DB::table('aktors')
            ->where('id', $id)
            ->first();

        if (!$actor) {

            return response()->json([
                'status' => false,
                'message' => 'Aktor tidak ditemukan.'
            ], 404);

        }

        $films = DB::table('aktor__films')
            ->join('films', 'aktor__films.film_id', '=', 'films.id')
            ->join('genres', 'films.genre_id', '=', 'genres.id')
            ->join('aktors', 'aktor__films.aktor_id', '=', 'aktors.id')
            ->where('aktors.id', $id)
            ->select(
                'films.id',
                'films.judul',
                'films.slug',
                'films.poster',
                'films.tanggal_rilis',
                'films.durasi',
                'films.sutradara',
                'genres.nama_genre'
            )
            ->paginate(10);

        return response()->json([
            'status' => true,
            'actor' => $actor,
            'data' => $films
        ], 200);

    } catch (Exception $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}

public function search(Request $request)
{
    try {

        $keyword = $request->keyword;

        $films = DB::table('films')
            ->join('genres', 'films.genre_id', '=', 'genres.id')
            ->select(
                'films.id',
                'films.judul',
                'films.slug',
                'films.poster',
                'films.tanggal_rilis',
                'films.durasi',
                'films.sutradara',
                'genres.nama_genre'
            )
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('films.judul', 'like', '%' . $keyword . '%');
            })
            ->orderBy('films.judul', 'asc')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Data film berhasil ditemukan.',
            'data' => $films
        ], 200);

    } catch (Exception $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}
}
