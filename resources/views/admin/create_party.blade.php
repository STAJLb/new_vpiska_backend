@extends('admin.main')

@section('title')
    Форма создания события
@endsection

@section('content')
    <section class="content">
        <!-- /.row -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Форма создания события</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive ">
                        <div class="modal fade">
                            <h4 class="modal-title" id="myModalLabel">Форма редактирования вписки</h4>

                                    </div>
                                    <div class="modal-body">
                                        @if( count($errors) > 0 )
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach( $errors->all() as $error ) <li>{{ $error }}</li> @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <form method="post" action="/admin/parties/create" class="form-horizontal" >

                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">ID создателя</label>
                                                <div class="col-sm-9">
                                                    <input readonly  type="text" class="form-control" id="created_id" name="created_id"  value="58">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Ник создателя</label>
                                                <div class="col-sm-9">
                                                    <input readonly  type="text" class="form-control" id="nik_name" name="nik_name" value="Admin">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Название cобытия</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="title_party" name="title_party" placeholder="Название события" value="{{ old('title_party') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Описание cобытия</label>
                                                <div class="col-sm-9">
                                                    <textarea type="text" class="form-control" id="description_party" name="description_party" placeholder="Описание cобытия"">{{ old('description_party') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Адрес cобытия</label>
                                                <div class="col-sm-9">
                                                    <textarea type="text" class="form-control" id="address_party" name="address_party" placeholder="Адрес cобытия" ">{{ old('address_party') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Координаты</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="coordinates" name="coordinates" placeholder="Координаты" value="{{ old('coordinates') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Дата и время</label>
                                                <div class="col-sm-9">
                                                    <input type="datetime-local" class="form-control" id="date_time" name="date_time" value="{{ old('date_time') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Тип</label>
                                                <div class="col-sm-9">
                                                    <select name="type_party" class="form-control">
                                                        <option value="walk">Прогулки</option>
                                                        <option value="tea">Домашнаяя посиделка</option>
                                                        <option value="picture">Выставка</option>
                                                        <option value="sacs">Концерт</option>
                                                        <option value="game">Игры</option>
                                                        <option value="film">Кино</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Алкоголь</label>
                                                <div class="col-sm-9">
                                                    <input type="checkbox" id="alcohol" name="alcohol" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Максимальное количество пользователей</label>
                                                <div class="col-sm-9">
                                                    <input type="number" class="form-control" id="count_people" name="count_people" value="{{ old('count_people') }}">
                                                </div>
                                            </div>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit"  class="btn btn-primary" id="btn-save" value="add">Создать</button>
                                        </form>
                                        <hr>
                                    </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </section>


@endsection

