<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aktor;
use Illuminate\Http\Request;
use Exception;

class AktorController extends Controller
{
    public function index()
    {
        try {
            $aktors = Aktor::all();

            if ($aktors->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No actors found.',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Actors data retrieved successfully.',
                'data' => $aktors
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
                'nama_aktor' => 'required|string',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'tanggal_lahir' => 'required|date',
                'umur' => 'required|integer',
                'foto' => 'nullable|string'
            ]);

            $aktors = new Aktor();
            $aktors->nama_aktor = $request->nama_aktor;
            $aktors->jenis_kelamin = $request->jenis_kelamin;
            $aktors->tanggal_lahir = $request->tanggal_lahir;
            $aktors->umur = $request->umur;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aktors'), $filename);
                $aktors->foto = 'uploads/aktors/' . $filename;
            }

            $aktors->save();

            return response()->json([
                'status' => true,
                'message' => 'Actors data saved successfully.',
                'data' => $aktors
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $aktors = Aktor::find($id);
            if (!$aktors) {
                return response()->json([
                    'status' => false,
                    'message' => 'Actor not found.'
                ], 404);
            }

            $request->validate([
                'nama_aktor' => 'required|string',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'tanggal_lahir' => 'required|date',
                'umur' => 'required|integer',
                'foto' => 'nullable|string'
            ]);

            $aktors->nama_aktor = $request->nama_aktor;
            $aktors->jenis_kelamin = $request->jenis_kelamin;
            $aktors->tanggal_lahir = $request->tanggal_lahir;
            $aktors->umur = $request->umur;

            if ($request->hasFile('foto')) {
                // Delete old photo if exists
                if ($aktors->foto && file_exists(public_path($aktors->foto))) {
                    unlink(public_path($aktors->foto));
                }
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aktors'), $filename);
                $aktors->foto = 'uploads/aktors/' . $filename;
            }

            $aktors->save();

            return response()->json([
                'status' => true,
                'message' => 'Actors data updated successfully.',
                'data' => $aktors
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
            $aktors = Aktor::find($id);
            if (!$aktors) {
                return response()->json([
                    'status' => false,
                    'message' => 'Actor not found.'
                ], 404);
            }

            // Delete photo if exists
            if ($aktors->foto && file_exists(public_path($aktors->foto))) {
                unlink(public_path($aktors->foto));
            }

            $aktors->delete();

            return response()->json([
                'status' => true,
                'message' => 'Actors data deleted successfully.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
