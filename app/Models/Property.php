<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
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
        if (!is_null(request('user_uuid')) && !empty(request('user_uuid'))) {
            $user_ids = User::where('uuid', request('user_uuid'))->pluck('id');
            $q->whereIn('user_id', $user_ids);
        }
        if (!is_null(request('county_uuid')) && !empty(request('county_uuid'))) {
            $county_ids = County::where('uuid', request('county_uuid'))->pluck('id');
            $q->whereIn('county_id', $county_ids);
        }
        if (!is_null(request('name')) && !empty(request('name'))) {
            $q->where('name', 'like', '%'.request('name').'%');
        }
        if (!is_null(request('no_of_units')) && !empty(request('no_of_units'))) {
            $q->where('no_of_units', request('no_of_units'));
        }
        if (!is_null(request('type')) && !empty(request('type'))) {
            $q->where('type', request('type'));
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

    // property units
    public function units(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Unit::class);
    }

    // property user
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // property county
    public function county(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    // property inquiries
    public function inquiries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Inquiry::class);
    }
}
