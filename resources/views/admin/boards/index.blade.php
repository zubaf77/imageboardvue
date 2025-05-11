@extends('layouts.dashboard')

@section('title', 'Борды')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Список борд</h3>
            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#createBoardModal">Создать борду</a>
        </div>

        <div class="table-responsive">
            <table class="table card-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Слаг</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($boards as $board)
                    <tr>
                        <td>{{ $board->id }}</td>
                        <td>{{ $board->name }}</td>
                        <td>{{ $board->slug }}</td>
                        <td>
                            <!-- Кнопка открытия модального окна редактирования -->
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editBoardModal-{{ $board->id }}">Редактировать</button>

                            <!-- Кнопка удаления -->
                            <form action="{{ route('admin.boards.destroy', $board) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Удалить борду?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Удалить</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Модальное окно редактирования -->
                    <div class="modal fade" id="editBoardModal-{{ $board->id }}" tabindex="-1" role="dialog" aria-labelledby="editBoardModalLabel-{{ $board->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form action="{{ route('admin.boards.update', $board) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editBoardModalLabel-{{ $board->id }}">Редактировать борду</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Название</label>
                                            <input type="text" name="name" class="form-control" value="{{ $board->name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Слаг</label>
                                            <input type="text" name="slug" class="form-control" value="{{ $board->slug }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                @empty
                    <tr><td colspan="4">Борд нет</td></tr>
                @endforelse

                <!-- Модальное окно создания борды -->
                <div class="modal fade" id="createBoardModal" tabindex="-1" role="dialog" aria-labelledby="createBoardModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form action="{{ route('admin.boards.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createBoardModalLabel">Создать борду</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Название</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Слаг</label>
                                        <input type="text" name="slug" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                    <button type="submit" class="btn btn-primary">Создать</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                </tbody>
            </table>
        </div>
    </div>
@endsection
