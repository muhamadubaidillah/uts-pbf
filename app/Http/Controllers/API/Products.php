<?php

namespace App\Http\Controllers\API;

use App\Models\ModelProducts;
use App\Models\ModelCategories;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Products
{
    public function read(Request $request, Response $response)
    {
        $data = ModelProducts::all();

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
            'price' => 'required|integer|max:99999999999',
            'category_id' => 'required',
            'expired_at' => 'required|date|date_format:Y-m-d',
            'image' => 'required|file|mimes:jpg,png,jpeg,webp',
        ]);

        // Kalau validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'message' => 'payload tidak valid',
                'error' => $validator->errors(),
            ], 400);
        }

        // Kalau validasi berhasil
        // Ambil email dari JWT
        $client = JWTAuth::parseToken()->authenticate();

        // Simpan upload image
        $pathFile = $request->file('image')->store('public');

        // Check category_id
        $checkCategoryId = ModelCategories::where('name', 'like', "%{$data['category_id']}%")->get();

        if (count($checkCategoryId) <= 0)
            return response()->json([
                'message' => '"category_id" tidak tersedia'
            ], 404);

        $data['category_id'] = $checkCategoryId[0]['id'];

        // Mencoba meng-insert data
        try {
            ModelProducts::insert([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'price' => $data['price'],
                'image' => $pathFile,
                'category_id' => $data['category_id'],
                'expired_at' => $data['expired_at'],
                'modified_by' => $client->email
            ]);

            return response()->json([
                'message' => 'insert produk berhasil'
            ]);
        } catch (QueryException $e) {

            return response()->json([
                'message' => 'kesalahan pada server. gagal insert data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id = null, Request $request, Response $response)
    {
        $data = $request->all();

        // Validasi data
        $validator = Validator::make($data, [
            'name' => 'string|max:255',
            'price' => 'integer|max:99999999999',
            'expired_at' => 'date|date_format:Y-m-d',
            'image' => 'file|mimes:jpg,png,jpeg,webp',
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
        // Cek id
        $find = ModelProducts::find($id);

        if (!$find) {
            return response()->json([
                'message' => 'id tidak ditemukan'
            ], 404);
        }

        // Ambil email dari JWT
        $client = JWTAuth::parseToken()->authenticate();

        // Cek category_id
        if (isset($data['category_id'])) {

            $checkCategoryId = ModelCategories::where('name', 'like', "%{$data['category_id']}%")->get();

            if (count($checkCategoryId) <= 0)
                return response()->json([
                    'message' => '"category_id" tidak tersedia'
                ], 404);

            $data['category_id'] = $checkCategoryId[0]['id'];
        }

        $data['modified_by'] = $client->email;

        // Mencoba meng-update data
        try {
            $find->update($data);

            return response()->json([
                'message' => 'update produk berhasil'
            ]);
        } catch (QueryException $e) {

            return response()->json([
                'message' => 'kesalahan pada server. gagal update data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id = null, Request $request, Response $response)
    {
        // Check id
        $find = ModelProducts::find($id);

        if (!$find)
            return response()->json([
                'message' => 'id tidak ditemukan'
            ], 404);

        // Delete data
        try {
            $find->delete();

            return response()->json([
                'message' => 'delete produk berhasil'
            ]);
        } catch (QueryException $e) {

            return response()->json([
                'message' => 'kesalahan pada server. gagal delete data'
            ], 500);
        }
    }
}
