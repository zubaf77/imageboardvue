@extends('layouts.dashboard')

@section('title', 'Треды')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Список тредов</h3>
        </div>

        @if(request('ip'))
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span>Фильтр по IP: <strong>{{ request('ip') }}</strong></span>
                <a href="{{ route('admin.threads.index') }}" class="btn btn-sm btn-secondary">Сбросить фильтр</a>
            </div>
        @endif

        @if(request('board'))
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span>Фильтр по борде: <strong>{{ request('board') }}</strong></span>
                <a href="{{ route('admin.threads.index') }}" class="btn btn-sm btn-secondary">Сбросить фильтр</a>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table card-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Заголовок</th>
                    <th>IP</th>
                    <th>Борда</th>
                    <th>Создан</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($threads as $thread)
                    <tr>
                        <td>{{ $thread->id }}</td>
                        <td>{{ Str::limit($thread->title, 50) }}</td>
                        <td>
                            {{ $thread->ip }}
                            @if($thread->ip)
                                <a href="{{ route('admin.threads.index', ['ip' => $thread->ip]) }}" class="btn btn-sm btn-outline-primary ml-2">Найти все</a>
                            @endif
                        </td>
                        <td>
                            {{ $thread->board->name ?? '-' }}
                            @if($thread->board)
                                <a href="{{ route('admin.threads.index', ['board' => $thread->board->name]) }}" class="btn btn-sm btn-outline-primary ml-2">Фильтр по борде</a>
                            @endif
                        </td>
                        <td>{{ $thread->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if($thread->ip)
                                @php
                                    $isBanned = \App\Models\BannedIp::where('ip', $thread->ip)->exists();
                                @endphp

                                @if($isBanned)
                                    <form action="{{ route('admin.unban.ip', ['ip' => $thread->ip]) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Разбанить этот IP?')">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Разбанить IP</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.threads.ban', $thread) }}" method="POST" class="d-inline-block" onsubmit="return confirmBan(this);">
                                        @csrf
                                        <input type="hidden" name="reason">
                                        <button class="btn btn-sm btn-warning">Забанить IP</button>
                                    </form>
                                @endif
                            @endif
                            @if(auth('admin')->check())
                                <form action="{{ route('admin.threads.delete', $thread) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Удалить тред?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Нет тредов</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $threads->appends(request()->query())->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmBan(form) {
                const reason = prompt('Укажите причину блокировки (необязательно):', '');
                if (reason === null) return false; // отмена
                form.querySelector('[name=reason]').value = reason || 'без причины';
                return true;
            }
        </script>
    @endpush

@endsection
