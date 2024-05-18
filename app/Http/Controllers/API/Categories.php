<?php

namespace App\Http\Controllers\API;

use App\Models\ModelCategories;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class Categories
{
    public function read(Request $request, Response $response)
    {
        $data = ModelCategories::all();

        return response()->json([
            'message' => 'get data berhasil',
            'data' => $data
        ]);
    }

    public function create(Request $request, Response $response)
    {
        $data = $request->all();

        // Validasi data
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
        ]);

        // Kalau validasi gagal
        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'payload tidak valid',
                    'error' => $validator->errors(),
                ],
                400
            );
        }

        // Kalau validasi berhasil
        // Mencoba meng-insert data
        try {
            ModelCategories::insert([
                'name' => $data['name']
            ]);

            return response()->json([
                'message' => 'insert kategori berhasil'
            ]);
        } catch (QueryException $e) {

            return response()->json([
                'message' => 'kesalahan pada server. gagal insert data'
            ], 500);
        }
    }

    public function update($id = null, Request $request, Response $response)
    {
        $data = $request->all();

        // Validasi data
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
        ]);

        // Kalau validasi gagal
        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'payload tidak valid',
                    'error' => $validator->errors(),
                ],
                400
            );
        }

        // Kalau validasi berhasil
        // Check id
        $find = ModelCategories::find($id);

        if (!$find)
            return response()->json([
                'message' => 'id tidak ditemukan'
            ], 404);

        // Update data
        try {
            $find->update([
                'name' => $data['name'],
            ]);

            return response()->json([
                'message' => 'update kategori berhasil'
            ]);
        } catch (QueryException $e) {

            return response()->json([
                'message' => 'kesalahan pada server. gagal update data'
            ], 500);
        }
    }

    public function delete($id = null, Request $request, Response $response)
    {
        // Check id
        $find = ModelCategories::find($id);

        if (!$find)
            return response()->json([
                'message' => 'id tidak ditemukan'
            ], 404);

        // Delete data
        try {
            $find->delete();

            return response()->json([
                'message' => 'delete kategori berhasil'
            ]);
        } catch (QueryException $e) {

            return response()->json([
                'message' => 'kesalahan pada server. gagal delete data'
            ], 500);
        }
    }
}
