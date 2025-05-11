@extends('layouts.dashboard')

@section('title', 'Пользователи')

@section('content')
    <div class="row">
        {{-- Форма добавления --}}
        <div class="col-lg-6">
            <form method="POST" action="{{ route('admin.users.add') }}" class="card">
                @csrf
                <div class="card-header"><h3 class="card-title">Добавить пользователя</h3></div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="form-group">
                        <label>Имя</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Подтверждение пароля</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Роль</label>
                        <select name="role" class="form-control">
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="moderator" {{ old('role') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-primary">Добавить</button>
                </div>
            </form>
        </div>

        {{-- Таблица пользователей --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Список пользователей</h3></div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                        <tr>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Роль</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td class="text-right">
                                    @if(auth()->user()->isOwner() && auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                              onsubmit="return confirm('Удалить пользователя?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Удалить</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">Пользователей нет</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
