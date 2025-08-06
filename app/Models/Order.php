<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $primaryKey = 'id_pesanan';

    public $incrementing = true;

    protected $fillable = [
        'id_user',
        'nama',
        'alamat',
        'email',
        'no_telepon',
        'metode_bayar',
        'tanggal_pesan',
        'tanggal_estimasi',
        'status',
        'nota',
        'desain',
        'ukuran',
        'fabric_type',
        'jumlah',
        'promo_code',
        'discount_amount',
        'total',
        'bukti_pembayaran',
    ];

    protected $casts = [
        'tanggal_pesan' => 'datetime',
        'tanggal_estimasi' => 'datetime',
        'total' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'id_pesanan', 'id_pesanan');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id_pesanan');
    }
}