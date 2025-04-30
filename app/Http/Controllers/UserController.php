<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Menampilkan semua pengguna
    public function index() {
        return response()->json(User::all(), 200); // Menampilkan semua data pengguna dalam format JSON
    }

    // Menampilkan detail pengguna berdasarkan ID
    public function show($id) {
        $user = User::find($id);

        // Jika pengguna tidak ditemukan, mengembalikan pesan error
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user, 200); // Menampilkan data pengguna dalam format JSON
    }

    // Membuat pengguna baru
    public function store(Request $request) {
        try {
            // Validasi input untuk memastikan data yang diterima valid
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed', // Menambahkan konfirmasi password
            ]);

            // Membuat pengguna baru dengan data yang telah tervalidasi
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]);

            // Mengembalikan respons jika berhasil
            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ], 201); // Mengembalikan respons dengan status 201 (Created)

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Menangani kesalahan validasi
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422); // Status 422 untuk validasi gagal

        } catch (\Exception $e) {
            // Menangani kesalahan lain (misalnya, kesalahan server)
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500); // Status 500 untuk error server internal
        }
    }

    // Mengupdate data pengguna
    public function update(Request $request, $id) {
        // Mencari pengguna berdasarkan ID
        $user = User::find($id);

        // Jika pengguna tidak ditemukan, mengembalikan pesan error
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validasi input untuk update
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        // Melakukan update data pengguna
        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ], 200); // Mengembalikan respons dengan status 200 (OK)
    }

    // Menghapus pengguna
    public function destroy($id) {
        // Mencari pengguna berdasarkan ID
        $user = User::find($id);

        // Jika pengguna tidak ditemukan, mengembalikan pesan error
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Menghapus pengguna
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200); // Mengembalikan respons sukses
    }
}
