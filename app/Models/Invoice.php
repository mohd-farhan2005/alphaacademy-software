<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'student_name',
        'batch',
        'price',
        'service',
        'payment_type',
    ];

    public function getInvoiceNumberAttribute()
    {
        return 'INV-' . $this->created_at->format('Y') . '-' . sprintf('%05d', $this->id);
    }
}
