<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
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
        if (!is_null(request('unit_uuid')) && !empty(request('unit_uuid'))) {
            $unit_ids = Unit::where('uuid', request('unit_uuid'))->pluck('id');
            $q->whereIn('unit_id', $unit_ids);
        }
        if (!is_null(request('user_uuid')) && !empty(request('user_uuid'))) {
            $user_ids = User::where('uuid', request('user_uuid'))->pluck('id');
            $q->whereIn('user_id', $user_ids);
        }
        if (!is_null(request('company_uuid')) && !empty(request('company_uuid'))) {
            $company_ids = Company::where('uuid', request('company_uuid'))->pluck('id');
            $q->whereIn('company_id', $company_ids);
        }
        if (!is_null(request('name')) && !empty(request('name'))) {
            $q->where('name', 'like', '%'.request('name').'%');
        }
        if (!is_null(request('msisdn')) && !empty(request('msisdn'))) {
            $q->where('msisdn', request('msisdn'));
        }
        if (!is_null(request('email')) && !empty(request('email'))) {
            $q->where('email', request('email'));
        }
        if (!is_null(request('no_of_occupants')) && !empty(request('no_of_occupants'))) {
            $q->where('no_of_occupants', request('no_of_occupants'));
        }
        if (!is_null(request('check_in')) && !empty(request('check_in'))) {
            $q->where('check_in', request('check_in'));
        }
        if (!is_null(request('check_out')) && !empty(request('check_out'))) {
            $q->where('check_out', request('check_out'));
        }
        if (!is_null(request('tenant_confirmation')) && !empty(request('tenant_confirmation'))) {
            $q->where('tenant_confirmation', request('tenant_confirmation'));
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

    public function user()
    {
        return $this->belongsTo(User::class, 'msisdn', 'msisdn');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
