<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Inquiry extends Model
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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'response_by', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
