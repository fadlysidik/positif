@extends('layout.admin')

@section('title', __('settings.Settings'))
@section('content-header', __('settings.Settings'))

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('settings.Update_Settings') }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('settings.store') }}" method="post">
            @csrf

            <!-- Nama Aplikasi -->
            <div class="form-group">
                <label for="app_name">{{ __('settings.App_name') }}</label>
                <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror" id="app_name" placeholder="{{ __('settings.App_name') }}" value="{{ old('app_name', config('settings.app_name')) }}">
                @error('app_name')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <!-- Deskripsi Aplikasi -->
            <div class="form-group">
                <label for="app_description">{{ __('settings.App_description') }}</label>
                <textarea name="app_description" class="form-control @error('app_description') is-invalid @enderror" id="app_description" placeholder="{{ __('settings.App_description') }}">{{ old('app_description', config('settings.app_description')) }}</textarea>
                @error('app_description')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <!-- Simbol Mata Uang -->
            <div class="form-group">
                <label for="currency_symbol">{{ __('settings.Currency_symbol') }}</label>
                <input type="text" name="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" id="currency_symbol" placeholder="{{ __('settings.Currency_symbol') }}" value="{{ old('currency_symbol', config('settings.currency_symbol')) }}">
                @error('currency_symbol')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <!-- Peringatan Stok -->
            <div class="form-group">
                <label for="warning_quantity">{{ __('settings.Warning_quantity') }}</label>
                <input type="number" name="warning_quantity" class="form-control @error('warning_quantity') is-invalid @enderror" id="warning_quantity" placeholder="{{ __('settings.Warning_quantity') }}" value="{{ old('warning_quantity', config('settings.warning_quantity')) }}">
                @error('warning_quantity')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <!-- Tombol Simpan -->
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('settings.Change_Setting') }}
            </button>
        </form>
    </div>
</div>
@endsection
