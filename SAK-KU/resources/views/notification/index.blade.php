@extends('layouts.app')

@section('title', 'Notifikasi | SAK-KU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/notifikasi.css') }}">
@endpush

@section('content')
    <header class="top-navbar">
        <div class="nav-content">
            <button class="icon-btn" onclick="window.location.href='{{ url('/dashboard') }}'">
                <i data-feather="arrow-left"></i>
            </button>
            
            <h1>Notifikasi</h1>
            
            <div class="nav-actions">
                <button class="icon-btn" id="themeToggle"></button>
                <button class="icon-btn" id="btnLogout" title="Keluar">
                    <i data-feather="log-out"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="notif-container">
        @if($notifikasi->isNotEmpty() && $notifikasi->where('is_read', false)->isNotEmpty())
            <div class="notif-header-actions">
                <form action="{{ url('/notifikasi/read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="read-all-btn">
                        <i data-feather="check-square"></i> Tandai Semua Dibaca
                    </button>
                </form>
            </div>
        @endif

        @forelse ($notifikasi as $notif)
            @if(!$notif->is_read)
                <form id="mark-read-{{ $notif->id }}" action="{{ url('/notifikasi/' . $notif->id . '/read') }}" method="POST" style="display: none;">
                    @csrf
                    @method('PATCH')
                </form>
            @endif

            <div class="notif-item {{ $notif->is_read ? '' : 'unread' }}" 
                 @if(!$notif->is_read) onclick="document.getElementById('mark-read-{{ $notif->id }}').submit();" @endif>
                
                @php
                    $iconClass = 'info';
                    $iconName = 'info';
                    if ($notif->type === 'danger') {
                        $iconClass = 'danger';
                        $iconName = 'alert-triangle';
                    } elseif ($notif->type === 'warning') {
                        $iconClass = 'warning';
                        $iconName = 'alert-circle';
                    } elseif ($notif->type === 'success') {
                        $iconClass = 'success';
                        $iconName = 'check-circle';
                    }
                @endphp

                <div class="notif-icon {{ $iconClass }}">
                    <i data-feather="{{ $iconName }}"></i>
                </div>
                <div class="notif-content">
                    <h4>{{ $notif->title }}</h4>
                    <p>{{ $notif->message }}</p>
                    <span class="notif-time" title="{{ $notif->created_at->format('d M Y H:i') }}">
                        {{ $notif->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        @empty
            <div class="notif-empty">
                <i data-feather="bell-off"></i>
                <p>Tidak ada notifikasi saat ini.</p>
            </div>
        @endforelse
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/notifikasi.js') }}"></script>
@endpush