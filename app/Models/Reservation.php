<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['user_id', 'room_id', 'check_in', 'check_out'];

    // Definisikan relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);  // Relasi user dengan reservation
    }

    // Definisikan relasi ke Room
    public function room()
    {
        return $this->belongsTo(Room::class);  // Relasi room dengan reservation
    }
}
