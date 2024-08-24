<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'invoice_number',
        'issued_at',
        'total_amount',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issued_at' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the order that owns the invoice.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
