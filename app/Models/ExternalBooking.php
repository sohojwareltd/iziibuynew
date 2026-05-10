<?php

namespace App\Models;

use App\Casts\Price;
use App\Models\Traits\HasMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalBooking extends Model
{
    use HasFactory, HasMeta;

    protected $guarded = [];

    public $casts = [
        'customer_details' => 'array',
        'service_details' => 'array',
        'tax' => Price::class,
        'subtotal' => Price::class,
        'total' => Price::class,
        'paid_at' => 'datetime',
        
    ];

    public $meta_attributes = [
        'elavon_transaction_id',
    ];

    public function paymentMethodAccess()
    {
        return $this->belongsTo(PaymentMethodAccess::class);
    }
    public function status()
    {
        $map = [
            'PENDING'   => 'words.pending',
            'COMPLETED' => 'words.completed',
        ];

        return $map[$this->status] ?? 'words.unknown';
    }

    public function paymentStatus()
    {
        $map = [
            'PENDING' => 'words.pending',
            'PAID'    => 'words.paid',
        ];

        return $map[$this->payment_status] ?? 'words.unknown_payment_status';
    }
    public function taxPercentage()
    {
        if ($this->subtotal == 0) {
            return 0;
        }
        return (int) round(($this->tax / $this->subtotal) * 100, 2);
    }
}
