<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RentalPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = ['id', 'user_id', 'deleted_at', 'updated_at'];

    protected static function boot(): void
    {
        parent::boot();
        self::creating(function ($col){
            $col->uuid = Str::orderedUuid()->toString();
        });
    }
    public function scopeFilter($q){
        if (!is_null(request('unit_uuid')) && !empty(request('unit_uuid'))) {
            $unit_ids = Unit::where('uuid', request('unit_uuid'))->pluck('id');
            $q->whereIn('unit_id', $unit_ids);
        }
        if (!is_null(request('name')) && !empty(request('name'))) {
            $q->where('name', 'like', '%'.request('name').'%');
        }
        if (!is_null(request('msisdn')) && !empty(request('msisdn'))) {
            $q->where('msisdn', request('msisdn'));
        }
        if (!is_null(request('rental_month')) && !empty(request('rental_month'))) {
            $q->where('rental_month', request('rental_month'));
        }
        if (!is_null(request('min_amount')) && !empty(request('min_amount'))) {
            $q->where('payable_amount', '>=', request('min_amount'));
        }
        if (!is_null(request('max_amount')) && !empty(request('max_amount'))) {
            $q->where('payable_amount', '<=', request('max_amount'));
        }
        if (!is_null(request('status')) && !empty(request('status'))) {
            $q->where('status', request('status'));
        }
        if (!is_null(request('start')) && !empty(request('start'))) {
            $q->where('created_at', '>=', request('start'));
        }
        if (!is_null(request('end')) && !empty(request('end'))) {
            $q->where('created_at', '<=',request('end'));
        }
        if (!is_null(request('sort_by')) && !empty(request('sort_by'))) {
            if (request('sort_by') == 'random') {
                $q->inRandomOrder();
            }
            if (request('sort_by') == 'latest') {
                $q->latest();
            }
            if (request('sort_by') == 'oldest') {
                $q->oldest();
            }
        }
        return $q;
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // unit
    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
