@extends('layouts.app')

@section('title', 'Verifikasi Email - SAK-KU')

@section('content')
<div class="auth-container">
    <div class="auth-card" style="text-align: center; max-width: 400px; margin: 50px auto; padding: 30px; background: var(--bg-card); border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 20px; color: var(--primary-color);">
            <i data-feather="mail" style="width: 48px; height: 48px;"></i>
        </div>
        
        <h2 style="margin-bottom: 15px; color: var(--text-main);">Verifikasi Email Anda</h2>
        
        <p style="margin-bottom: 20px; color: var(--text-muted); line-height: 1.5;">
            Terima kasih telah mendaftar! Sebelum mulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan.
        </p>
        
        @if (session('message'))
            <div style="margin-bottom: 20px; padding: 10px; background-color: rgba(46, 204, 113, 0.1); color: #2ecc71; border-radius: 8px; font-size: 14px;">
                {{ session('message') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom: 15px;">
            @csrf
            <button type="submit" class="auth-btn login-btn" style="width: 100%; justify-content: center;">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background: none; border: none; color: var(--text-muted); text-decoration: underline; cursor: pointer; font-size: 14px;">
                Logout
            </button>
        </form>
    </div>
</div>

<style>
    body {
        background-color: var(--bg-main);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
    }
</style>
@endsection
