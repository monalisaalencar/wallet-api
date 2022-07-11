<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dateFormat = 'Y-m-d G:i:s';
    protected $dates = ['updated_at', 'created_at','deleted_at'];
    protected $guarded = ['_id'];
    protected $fillable = [
        'client_id',
        'balance_value',
		'created_at',
		'updated_at',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
