@extends('layouts.dashboard')

@section('title', 'Посты')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Список постов</h3>
        </div>

        {{-- Фильтры --}}
        @if(request('ip') || request('thread_id'))
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <div>
                    @if(request('ip'))
                        <span>Фильтр по IP: <strong>{{ request('ip') }}</strong></span>
                    @endif
                    @if(request('thread_id'))
                        <span class="ml-3">Тред: <strong>#{{ request('thread_id') }}</strong></span>
                    @endif
                </div>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-secondary">Сбросить фильтр</a>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table card-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Контент</th>
                    <th>IP</th>
                    <th>Борда</th>
                    <th>Тред</th>
                    <th>Создан</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td>
                            {{ $post->id }}
                            @if($post->thread_id)
                                <a href="{{ route('admin.posts.index', ['thread_id' => $post->thread_id]) }}"
                                   class="btn btn-sm btn-outline-info ml-2">Тред</a>
                            @endif
                        </td>
                        <td>{{ Str::limit($post->content, 50) }}</td>
                        <td>
                            {{ $post->ip }}
                            @if($post->ip)
                                <a href="{{ route('admin.posts.index', ['ip' => $post->ip]) }}"
                                   class="btn btn-sm btn-outline-primary ml-2">Найти все</a>
                            @endif
                        </td>
                        <td>{{ $post->thread->board->slug ?? '-' }}</td>
                        <td>{{ $post->thread->title ?? '-' }}</td>
                        <td>{{ $post->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if($post->ip)
                                @php
                                    $isBanned = \App\Models\BannedIp::where('ip', $post->ip)->exists();
                                @endphp

                                @if($isBanned)
                                    <form action="{{ route('admin.unban.ip') }}" method="POST" class="d-inline-block" onsubmit="return confirm('Разбанить IP?')">
                                        @csrf
                                        <input type="hidden" name="ip" value="{{ $post->ip }}">
                                        <button class="btn btn-sm btn-success">Разбанить IP</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.posts.ban', $post) }}" method="POST" class="d-inline-block" onsubmit="return confirmBan(this);">
                                        @csrf
                                        <input type="hidden" name="reason">
                                        <button class="btn btn-sm btn-warning">Забанить IP</button>
                                    </form>
                                @endif
                                @if(auth('admin')->check())
                                    <form action="{{ route('admin.posts.delete', $post) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Удалить пост?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Удалить</button>
                                    </form>
                                @endif

                            @endif

                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">Нет постов</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $posts->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmBan(form) {
            const reason = prompt('Укажите причину блокировки (необязательно):', '');
            if (reason === null) return false;
            form.querySelector('[name=reason]').value = reason || 'без причины';
            return true;
        }
    </script>
@endpush
