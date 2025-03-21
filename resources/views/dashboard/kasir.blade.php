@extends('layout.kasir')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <h2 class="fw-bold">Annyeonghaseyo, {{ Auth::user()->name }}! 👋</h2>
            <h4>Selamat datang di POSITIF, semoga harimu menyenangkan!</h4>
        </div>

        {{-- <div class="col-md-3">
            <a href="{{ route('penjualan.create') }}" class="text-decoration-none">
                <div class="card text-white shadow-sm" style="background-color: #344CB7;">
                    <div class="card-body text-center">
                        <i class="fas fa-cash-register fa-2x mb-2"></i>
                        <h5 class="card-title mb-0">Kasir</h5>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-3">
            <a href="{{ route('penjualan.index') }}" class="text-decoration-none">
                <div class="card text-white shadow-sm" style="background-color: #344CB7;">
                    <div class="card-body text-center">
                        <i class="fas fa-list-alt fa-2x mb-2"></i>
                        <h5 class="card-title mb-0">Detail Penjualan</h5>
                    </div>
                </div>
            </a>
        </div> --}}
    </div>
@endsection
