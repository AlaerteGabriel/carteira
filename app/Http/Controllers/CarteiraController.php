<?php

namespace App\Http\Controllers;

use App\Repositories\UsersRepository;
use Illuminate\Http\Request;

class CarteiraController extends Controller
{

    const PATH_VIEW = '/auth/';


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view(self::PATH_VIEW.'login');
    }

    public function cadastro()
    {
        return view(self::PATH_VIEW.'register');
    }

    public function store(Request $request, UsersRepository $usersRepository)
    {

        $valid = $request->validate([
            'us_nome' => 'string|required',
            'us_email' => 'string|email|unique:fi_users',
            'us_password' => 'required|min:8|confirmed',
            'us_cpf' => 'int|required|min:10',
        ]);

        //flag de controle para enviar email de cadastro ao cliente
        $valid['mailme'] = 'S';
        $valid['us_status'] = 2;

        return $usersRepository->add($valid);

    }

}
