@extends('layout.member')
@section('content')
 <div class="container">
    <h3 class="text-xl font-semibold">Selamat datang, {{ Auth::user()->name }} 👋</h3>
    <p class="text-gray-600">Senang melihat Anda kembali! Anda login sebagai <strong>Member</strong>.</p>
 </div>
@endsection