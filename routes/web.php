<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\CarteiraController;
use App\Http\Controllers\TransacoesController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;



// Route::get($uri, $callback);
// Route::post($uri, $callback);
// Route::put($uri, $callback);
// Route::patch($uri, $callback);
// Route::delete($uri, $callback);
// Route::options($uri, $callback);

// Verb	        Path	                Action	    Route Name
// GET	        /photo	                index	    photo.index
// GET	        /photo/create	        create	    photo.create
// POST	        /photo	                store	    photo.store
// GET	        /photo/{photo}	        show	    photo.show
// GET	        /photo/{photo}/edit	    edit	    photo.edit
// PUT/PATCH    /photo/{photo}	        update	    photo.update
// DELETE	    /photo/{photo}	        destroy	    photo.destroy

// Route::get('/locations/{location:slug}', [LocationsController::class, 'show'])->name('locations.view')->missing(function (Request $request) {
//     return Redirect::route('locations.index');
// });

//Auth::routes(['verify' => true]);

Route::prefix('/')->group(function(){

    //Redirecionamos o acesso principal para a rota de login.
    Route::redirect('', '/login')->name('carteira.login');

    Route::prefix('check')->controller(LoginController::class)->group(function(){
        Route::get('email/{id}/{hash}', 'checkEmail')->name('carteira.checkemail');
    });

    Route::get('/cadastro', [CarteiraController::class, 'cadastro'])->name('carteira.cadastro');
    Route::post('/cadastro', [CarteiraController::class, 'store'])->middleware(ProtectAgainstSpam::class)->name('carteira.cadastro.store');

    //rotas para redefinição de senha
    Route::get('redefinir-senha', [PasswordController::class, 'linkCreate'])->name('carteira.password.request');
    Route::post('link-senha', [PasswordController::class, 'linkStore'])->middleware(ProtectAgainstSpam::class)->name('carteira.password.email');

    Route::get('nova-senha/{token}', [PasswordController::class, 'create'])->name('password.reset');
    Route::post('nova-senha', [PasswordController::class, 'store'])->middleware(ProtectAgainstSpam::class)->name('carteira.password.store');
    //--FIMrotas para redefinição de senha

    Route::prefix('login')->controller(LoginController::class)->group(function(){
        Route::get('', 'index')->name('carteira.login.index');
        Route::post('/logar','logar')->middleware(ProtectAgainstSpam::class)->name('carteira.login.logar');
        Route::get('/logout', 'destroy')->name('carteira.login.logout');
    });

    Route::prefix('acc')->middleware(['auth', 'auth.session'])->controller(AccountController::class)->group(function(){
        Route::get('/', 'index')->name('carteira.acc.index');
        Route::post('/transferir', [TransacoesController::class, 'storeTransfer'])->middleware(['checkDuplicidade'])->name('carteira.transferir.store');
        Route::post('/depositar', [TransacoesController::class, 'storeDeposit'])->middleware(['checkDuplicidade'])->name('carteira.deposito.store');
        Route::delete('/encerrar', 'destroy')->name('carteira.acc.destroy');
        Route::post('/transacoes/{transaction}/estornar', [TransacoesController::class, 'estornar'])->name('carteira.transacoes.estorno');
    });

});
