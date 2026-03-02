<?php

namespace App\Http\Controllers;

use App\Models\Transacoes;
use App\Services\TransacoesService;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{

    const PATH_VIEW = 'dashboard/';


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $transacoes = Transacoes::where('tr_us_id', $user->us_id)
        ->orWhere('tr_beneficiario_id', $user->us_id)
        ->orWhere('tr_pagador_id', $user->us_id)
        ->with(['pagador', 'beneficiario', 'transacaoOriginal'])
        ->latest()
        ->paginate(10);

        // Resumo para os "Cards" do topo do dashboard
        $summary = [
            'entradas'  => Transacoes::where('tr_beneficiario_id', $user->us_id)->orWhere(fn($q) => $q->where('tr_us_id', $user->us_id)->where('tr_tipo', 'deposito'))->sum('tr_valor'),
            'saidas' => Transacoes::where('tr_pagador_id', $user->us_id)->sum('tr_valor'),
        ];

        return view(self::PATH_VIEW.'index', ['user' => $user, 'transacoes' => $transacoes, 'summary' => $summary]);
    }

}
