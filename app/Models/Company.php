<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
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
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // company users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // wallet transactions
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // company messages
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'company_id', 'company_id');
    }
}
