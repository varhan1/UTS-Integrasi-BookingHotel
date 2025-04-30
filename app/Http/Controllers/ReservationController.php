<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ReservationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     tags={"Reservasi-Service"},
     *     summary="List semua reservasi",
     *     @OA\Response(
     *         response=200,
     *         description="Daftar semua reservasi"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Reservation::with(['user', 'room'])->get(), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/reservations/{id}",
     *     tags={"Reservasi-Service"},
     *     summary="Tampilkan detail reservasi berdasarkan ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detail reservasi ditemukan"),
     *     @OA\Response(response=404, description="Reservasi tidak ditemukan")
     * )
     */
    public function show($id)
    {
        $reservation = Reservation::with(['user', 'room'])->find($id);
        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }
        return response()->json($reservation, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     tags={"Reservasi-Service"},
     *     summary="Buat reservasi baru",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "room_id", "check_in", "check_out"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="room_id", type="integer", example=2),
     *             @OA\Property(property="check_in", type="string", format="date", example="2025-05-01"),
     *             @OA\Property(property="check_out", type="string", format="date", example="2025-05-03")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Reservasi berhasil dibuat"),
     *     @OA\Response(response=500, description="Kesalahan server")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        try {
            $reservation = Reservation::create($validated);
            return response()->json([
                'message' => 'Reservation created successfully',
                'data' => $reservation
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/reservations/{id}",
     *     tags={"Reservasi-Service"},
     *     summary="Update reservasi berdasarkan ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "room_id", "check_in", "check_out"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="room_id", type="integer", example=2),
     *             @OA\Property(property="check_in", type="string", format="date", example="2025-05-02"),
     *             @OA\Property(property="check_out", type="string", format="date", example="2025-05-04")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Reservasi berhasil diupdate"),
     *     @OA\Response(response=404, description="Reservasi tidak ditemukan")
     * )
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $reservation->update($validated);

        return response()->json([
            'message' => 'Reservation updated successfully',
            'data' => $reservation
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/reservations/{id}",
     *     tags={"Reservasi-Service"},
     *     summary="Hapus reservasi berdasarkan ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Reservasi berhasil dihapus"),
     *     @OA\Response(response=404, description="Reservasi tidak ditemukan")
     * )
     */
    public function destroy($id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        $reservation->delete();
        return response()->json(['message' => 'Reservation deleted successfully'], 200);
    }
}
