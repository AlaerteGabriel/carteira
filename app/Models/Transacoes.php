<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\UtilHelper AS HP;

class Transacoes extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'fi_transacoes';
    protected $primaryKey = 'tr_id';
    protected $fillable = [
        'tr_us_id',
        'tr_beneficiario_id',
        'tr_pagador_id',
        'tr_estorno_id',
        'tr_valor',
        'tr_tipo',
        'tr_descricao',
        'tr_estornado',
        'created_at',
        'updated_at'
    ];

    // Relacionamento com o dono da conta que visualiza a transação
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tr_us_id', 'us_id');
    }

    // Se for uma transferência, quem enviou
    public function pagador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tr_pagador_id', 'us_id');
    }

    // Se for uma transferência, quem recebeu
    public function beneficiario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tr_beneficiario_id', 'us_id');
    }

    // Relacionamento para saber qual transação esta reversão está anulando
    public function transacaoOriginal(): BelongsTo
    {
        return $this->belongsTo(Transacoes::class, 'tr_estorno_id', 'tr_id');
    }


}
