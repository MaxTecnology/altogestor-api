<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'auth.login')->name('login');
Route::view('/app', 'app')->name('app');
Route::view('/app/documentos/modelos', 'documentos.modelos')->name('documentos.modelos');
Route::view('/app/documentos/solicitacoes', 'documentos.solicitacoes')->name('documentos.solicitacoes');
Route::redirect('/', '/app');

require __DIR__.'/auth.php';
