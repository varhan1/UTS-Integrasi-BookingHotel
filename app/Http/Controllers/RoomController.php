<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Menampilkan semua kamar
    public function index() {
        return response()->json(Room::all(), 200); // Menampilkan data kamar dalam format JSON
    }

    // Menampilkan detail kamar berdasarkan ID
    public function show($id) {
        $room = Room::find($id);

        // Jika kamar tidak ditemukan, mengembalikan pesan error
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        return response()->json($room, 200); // Menampilkan kamar dalam format JSON
    }

    // Membuat kamar baru
    public function store(Request $request) {
        try {
            // Validasi input untuk memastikan data yang diterima valid
            $validated = $request->validate([
                'room_number' => 'required|string|unique:rooms,room_number',
                'type' => 'required|string',
                'price' => 'required|numeric|min:0',
                'is_available' => 'required|boolean',
            ]);

            // Membuat kamar baru dengan data yang telah tervalidasi
            $room = Room::create($validated);

            // Mengembalikan response jika berhasil
            return response()->json([
                'message' => 'Room created successfully',
                'data' => $room
            ], 201); // Status code 201 untuk Created
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, return error
            return response()->json([
                'message' => 'Failed to create room',
                'error' => $e->getMessage()
            ], 500); // Status code 500 untuk error server internal
        }
    }

    // Mengupdate data kamar
    public function update(Request $request, $id) {
        $room = Room::find($id);

        // Jika kamar tidak ditemukan, mengembalikan pesan error
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        // Validasi input untuk update
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $id,
            'type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        // Melakukan update data kamar
        $room->update($validated);

        return response()->json([
            'message' => 'Room updated successfully',
            'data' => $room
        ], 200); // Mengembalikan respon dengan status 200 (OK)
    }

    // Menghapus kamar
    public function destroy($id) {
        $room = Room::find($id);

        // Jika kamar tidak ditemukan, mengembalikan pesan error
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        // Menghapus kamar
        $room->delete();

        return response()->json(['message' => 'Room deleted successfully'], 200); // Mengembalikan respon sukses
    }
}
