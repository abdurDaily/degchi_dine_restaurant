<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyBookingInquiry extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'event_date',
        'guest_count',
        'party_package_id',
        'message',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function package()
    {
        return $this->belongsTo(PartyPackage::class, 'party_package_id');
    }
}
