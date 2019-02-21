@extends('admin.main')

@section('title')
    Список событий
@endsection

@section('content')
    <section class="content">
        <!-- /.row -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Таблица событий</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive ">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-ban"></i>Успешно!</h4>
                                {{ session('success') }}
                            </div>
                        @endif
                        <table id="example1" class="table table-bordered table-striped ">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название события</th>
                                <th>Создатель</th>
                                <th>Координаты</th>
                                <th>Тип</th>
                                <th>Дата</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($parties as $party)
                                <tr id="party_{{$party->id}}">
                                    <td>{{$party->id}}</td>
                                    <td>{{$party->title_party}}</td>
                                    <td>{{$party->user->nik_name}}</td>
                                    <td>{{$party->coordinates}}</td>
                                    <td>{{$party->type}}</td>
                                    <td>{{$party->date_time}}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-xs open-modal" value="{{$party->id}}">Посмотреть информацию</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody></table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </section>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Форма редактирования события</h4>
                </div>
                <div class="modal-body">
                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Ник создателя</label>
                            <div class="col-sm-9">
                                <input disabled type="text" class="form-control" id="nik_name" name="nik_name" placeholder="Никнейм создателя" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Название события</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="title_party" name="title_party" placeholder="Название события" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Описание события</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" noresize id="description_party" name="description_party" placeholder="Описание события"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Координаты</label>
                            <div class="col-sm-9">
                                <input type="text" disabled class="form-control" id="coordinates" name="coordinates" placeholder="Координаты" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Дата и время</label>
                            <div class="col-sm-9">
                                <input type="text"  class="form-control" id="date_time" name="date_time" placeholder="Дата и время" value="">
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
                                <input type="number" class="form-control" id="count_people_max" name="count_people_max" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Статус</label>
                            <div class="col-sm-9">
                                <select id="status" class="form-control">
                                    <option value="1">Открытая</option>
                                    <option value="2">Прошедшая</option>

                                </select>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <h4 class="title" id="myModalLabel">История вписки</h4>
                    <div id="history-party">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-save" value="add">Сохранить</button>
                    <input type="hidden" id="party_id" name="party_id" value="0">
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(function () {
            $("#example1").DataTable();
        });
    </script>
    <script src="{{asset('dist/js/ajax-list-parties.js?v=6')}}"></script>
@endsection
