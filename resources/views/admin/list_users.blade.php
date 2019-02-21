@extends('admin.main')

@section('title')
    Список пользователей
    <meta http-equiv="Cache-Control" content="no-cache">
@endsection

@section('content')
    <style>
        .page_avatar {
            text-align: center;
            overflow: hidden;
            border-radius: 15px;
            padding-bottom: 20px;
        }
        .page_avatar_img {
            vertical-align: top;
            border-radius: 15px;
            display: block;
            width: 30%;
            height: 30%;
            margin: 0 auto;
        }
    </style>
    <section class="content">
        <!-- /.row -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Таблица пользователей</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive ">
                        <table id="example1" class="table table-bordered table-striped ">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя и фамилия</th>
                                <th>Никнейм</th>
                                <th>Пол</th>
                                <th>Возраст</th>
                                <th>Статус</th>
                                <th>Дата создания</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($appUsers as $appUser)
                                <tr @if($appUser->status == 3){ style="background-color: red;" } @endif
                                id="user_app_{{$appUser->id}}">
                                    <td>{{$appUser->id}}</td>
                                    <td>{{$appUser->first_name}}</td>
                                    <td>{{$appUser->nik_name}}</td>
                                    <td>
                                        @if($appUser->sex == "m")
                                            <span>Мужчина</span>
                                        @else
                                            <span>Женщина</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{$appUser->age}}
                                    </td>
                                    <td>
                                        {{--//0 - обычный, 1 - админ, 2 - модератор, 3 - забанен--}}
                                        @if($appUser->status == 0)
                                            Обычный пользователь
                                        @elseif($appUser->status == 1)
                                            <b>Администратор</b>
                                        @elseif($appUser->status == 2)
                                            <span>Модератор</span>
                                        @else
                                            Заблокированный
                                        @endif
                                    </td>
                                    <td>
                                        {{$appUser->created_at}}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" value="{{$appUser->id}}" class="btn btn-default open-modal">Посмотреть информацию</button>
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><button type="button" class="modal-add-note" value="{{$appUser->id}}">Добавить заметку</button></li>
                                                <li><button type="button" class="delete-user" value="{{$appUser->id}}">Удалить пользователя</button></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Форма редактирования пользователя</h4>
                </div>
                <div class="modal-body">
                    <div id="page_avatar" class="page_avatar"><img class="page_avatar_img" id="image" src="" alt="ПУСТО" ></div><form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">


                        <div class="form-group error">
                            <label for="inputTask" class="col-sm-3 control-label">Имя и Фамилия</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="first_name" name="first_name"
                                       placeholder="Имя и Фамилия" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Ник</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nik_name" name="nik_name" placeholder="Ник"
                                       value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Возраст</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="age" name="age" placeholder="Возраст"
                                       value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Пол</label>
                            <div class="col-sm-9">
                                <select id="sex" class="form-control">
                                    <option value="m">Мужчина</option>
                                    <option value="w">Женщина</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Статус</label>
                            <div class="col-sm-9">
                                <select id="status" class="form-control">
                                    <option value="0">Обычный пользователь</option>
                                    <option value="1">Администратор</option>
                                    <option value="2">Модератор</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">IMEI</label>
                            <div class="col-sm-9">
                                <input disabled type="text" class="form-control disabled" id="imei" name="imei"
                                       placeholder="IMEI" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Рейтинг</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="rating" name="rating" placeholder="Рейтинг"
                                       value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Баланс</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="balance" name="balance" placeholder="Баланс"
                                       value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Дата обновления рейтинга</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="date_update_rating" name="date_update_rating" placeholder="Дата обновления рейтинга"
                                       value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Статус отключения рекламы</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="ads_disabled" name="ads_disabled" placeholder="Статус отключения рекламы"
                                       value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Дата создания</label>
                            <div class="col-sm-9">
                                <input disabled type="text" class="form-control disabled" id="created_at" name="created_at"
                                       placeholder="Дата создания" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Дата обновления</label>
                            <div class="col-sm-9">
                                <input disabled type="text" class="form-control disabled" id="updated_at" name="updated_at"
                                       placeholder="Дата обновления" value="">
                            </div>
                        </div>
                    </form>
                    <hr>
                    <h4 class="title" id="myModalLabel">История банов</h4>
                    <div id="history-bans">

                    </div>
                    <hr>
                    <h4 class="title" id="myModalLabel">История заметок</h4>
                    <div id="history-notes">

                    </div>
                    <hr>
                    <div class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" id="btn-ban-imei" value="imei">Бан по
                            IMEI
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-ban-nikname" value="nikname">Бан по
                            Нику
                        </button>
                    </div>
                    <hr>
                    <div class="text-center">
                        <button type="button" class="btn btn-info btn-sm"  id="btn-disabled-ban" >
                            Снять все блокировки и вернуть в статус обычного пользователя.
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-save" value="add">Сохранить</button>

                    <input type="hidden" id="user_id" name="user_id" value="0">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modelAddNote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Форма добавления заметки к пользователю</h4>
                </div>
                <div class="modal-body">
                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Форма добавления заметки</label>
                            <div class="col-sm-9">

                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Введите заметку"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-add-note" value="add">Добавить заметку</button>

                    <input type="hidden" id="user_id_note" name="user_id_note" value="0">
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
    <script src="{{asset('dist/js/ajax-list-users.js?v=17')}}"></script>
@endsection