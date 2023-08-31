<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WalletTransaction extends Model
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
        if (!is_null(request('company_uuid')) && !empty(request('company_uuid'))) {
            $company_ids = Company::where('uuid', request('company_uuid'))->pluck('id');
            $q->whereIn('company_id', $company_ids);
        }
        if (!is_null(request('date')) && !empty(request('date'))) {
            $q->whereDate('date', Carbon::parse(request('date'))->toDateString());
        }
        if (!is_null(request('details')) && !empty(request('details'))) {
            $q->where('details', 'like', '%'.request('details').'%');
        }
        if (!is_null(request('trans_id')) && !empty(request('trans_id'))) {
            $q->where('trans_id', request('trans_id'));
        }
        if (!is_null(request('min_amount')) && !empty(request('min_amount'))) {
            $q->where('amount', '>=', request('min_amount'));
        }
        if (!is_null(request('max_amount')) && !empty(request('max_amount'))) {
            $q->where('amount', '<=', request('max_amount'));
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

    // company
    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
