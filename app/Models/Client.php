<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dateFormat = 'Y-m-d G:i:s';
    protected $dates = ['updated_at', 'created_at','deleted_at'];
    protected $guarded = ['_id'];
    protected $fillable = [
        'type',
        'name',
        'cpf_cnpj',
        'email_address',
        'password',
		'created_at',
		'updated_at',
    ];

    public function out_transactions()
	{
		return $this->hasMany(Transaction::class, 'payer_id');
	}

    public function input_transactions()
	{
		return $this->hasMany(Transaction::class, 'payee_id');
	}

    public function wallet()
	{
		return $this->hasOne(Wallet::class);
	}

}
