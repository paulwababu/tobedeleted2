<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = ['id', 'deleted_at', 'updated_at'];

    protected static function boot(): void
    {
        parent::boot();
        self::creating(function ($col){
            $col->uuid = Str::orderedUuid()->toString();
        });
    }
    public function scopeFilter($q){
        if (!is_null(request('property_uuid')) && !empty(request('property_uuid'))) {
            $property_ids = Property::where('uuid', request('property_uuid'))->pluck('id');
            $q->whereIn('property_id', $property_ids);
        }
        if (!is_null(request('unit_type_id')) && !empty(request('unit_type_id'))) {
            $q->where('unit_type_id', request('unit_type_id'));
        }
        if (!is_null(request('label')) && !empty(request('label'))) {
            $q->where('label', request('label'));
        }
        if (!is_null(request('deposit')) && !empty(request('deposit'))) {
            $q->where('deposit', request('deposit'));
        }
        if (!is_null(request('rent')) && !empty(request('rent'))) {
            $q->where('rent', request('rent'));
        }
        if (!is_null(request('min_price')) && !empty(request('min_price'))) {
            $q->where('rent', '>=', request('min_price'));
        }
        if (!is_null(request('max_price')) && !empty(request('max_price'))) {
            $q->where('rent', '<=', request('max_price'));
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

    // unit property
    public function property(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // unit type
    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }

    // unit photos
    public function photos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Photo::class);
    }

    // unit tenants
    public function tenants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}
