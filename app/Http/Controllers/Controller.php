<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function resShowData($data): JsonResponse
    {
        return response()->json([
            'status_code' => 200,
            'message' => 'Data Berhasil Diambil',
            'data' => $data,
        ], 200);
    }

    public function resInvalidLogin()
    {
        return response(['message' => 'Email Atau Password Salah'], 409);
    }

    public function resUpdatedData($data): JsonResponse
    {
        return response()->json([
            'status_code' => 200,
            'message' => 'Data Berhasil Diubah',
            'data' => $data,
        ], 200);
    }

    public function resAddData($data): JsonResponse
    {
        return response()->json([
            'status_code' => 201,
            'message' => 'Data Berhasil Ditambahkan',
            'data' => $data,
        ], 201);
    }

    public function resUserLogout(): JsonResponse
    {
        return response()->json([
            'status_code' => 200,
            'message' => 'Berhasil Logout',
        ], 200);
    }

    public function resUserNotFound() : JsonResponse
    {
        return response()->json(['message' => 'User Tidak Ditemukan'], 404);
    }

    public function resUserNotAdmin()
    {
        return response(['message' => 'User Bukan Admin'], 403);
    }

    public function resDataNotFound($data)
    {
        return response()->json(['message' => $data.' Tidak Ditemukan'], 404);
    }

    public function resDataDeleted(): JsonResponse
    {
        return response()->json(['message' => 'Data Berhasil Dihapus'], 200);
    }
}
