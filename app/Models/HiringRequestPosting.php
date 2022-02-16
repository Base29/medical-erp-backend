<?php

namespace App\Models;

use App\Models\HiringRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HiringRequestPosting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hiring_request_id',
        'site_name',
        'posted_on',
        'ends_on',
        'link',
    ];

    public function hiringRequest()
    {
        return $this->belongsTo(HiringRequest::class);
    }
}