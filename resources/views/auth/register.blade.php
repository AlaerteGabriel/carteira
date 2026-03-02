@extends('layouts.auth')

@section('title', 'Cadastro - Carteira Financeira')

@section('content')
<div class="auth-container register card border-0 shadow-lg rounded-4">
    <div class="card-body p-5">
        <div class="text-center mb-4">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-person-plus" style="font-size: 2.5rem;"></i>
            </div>
            <h4 class="fw-bold">Criar Conta</h4>
            <p class="text-muted">Preencha os dados e junte-se a nós</p>
        </div>

        <form action="{{ route('carteira.cadastro.store') ?? '#' }}" method="POST">
            @csrf

            @if (session('success'))
                <div class="alert alert-success mb-4 rounded-3 border-0 bg-success bg-opacity-10 text-success" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mb-4 rounded-3 border-0 bg-danger bg-opacity-10 text-danger" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="mb-3">
                <label for="name" class="form-label fw-medium">Nome Completo</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 ps-0 @error('us_nome') is-invalid @enderror" name="us_nome" id="name" value="{{ old('us_nome') }}" required placeholder="Nome Completo" autofocus>
                </div>
                @error('us_nome')
                    <div class="small text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label fw-medium">CPF:</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-vcard text-muted"></i></span>
                    <input type="number" maxlength="10" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control bg-light border-start-0 ps-0 @error('us_cpf') is-invalid @enderror" name="us_cpf" id="cpf" value="{{ old('us_cpf') }}" required placeholder="CPF" autofocus>
                </div>
                @error('us_cpf')
                    <div class="small text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-medium">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" class="form-control bg-light border-start-0 ps-0 @error('us_email') is-invalid @enderror" name="us_email" id="email" value="{{ old('us_email') }}" required placeholder="seu@email.com">
                </div>
                @error('us_email')
                    <div class="small text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-medium">Senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" class="form-control bg-light border-start-0 ps-0 @error('us_password') is-invalid @enderror" name="us_password" id="password" required placeholder="sua senha">
                </div>
                @error('us_password')
                    <div class="small text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-medium">Confirmar Senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-check-circle text-muted"></i></span>
                    <input type="password" class="form-control bg-light border-start-0 ps-0" name="us_password_confirmation" id="password_confirmation" required placeholder="Repetir senha">
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success py-2 fw-medium rounded-pill border-0" style="background: linear-gradient(135deg, #198754, #20c997);">Finalizar Cadastro</button>
            </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <p class="mb-0 text-muted">Já tem uma conta? <a href="{{ route('carteira.login') ?? '#' }}" class="text-decoration-none fw-semibold text-success">Faça login</a></p>
        </div>
    </div>
</div>
@endsection
