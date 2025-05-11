@extends('layouts.dashboard')

@section('title', 'Профиль')

@section('content')
    @push('scripts')
    @endpush
    <form action="{{ route('admin.profile.update') }}" method="POST" class="card">
        <input type="hidden" name="recaptcha_token" id="recaptcha-token">
        @csrf
        <div class="card-header">
            <h3 class="card-title">Редактировать профиль</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="name">Имя</label>
                <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Новый пароль</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Подтвердите пароль</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
            </div>
            @error('password')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
@endsection