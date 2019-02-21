@extends('admin.main')

@section('title')
    Квартирник  | Админ-панель
@endsection


@section('content')
    <center>
        <H2>Статистика приложения:</H2>
    </center>
    <p>Добавились сегодня к событию:  {{$countMembersToday}}</p>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Таблица пользователей</h3>
        </div>
    <div class="box-body table-responsive ">
        <table id="example1" class="table table-bordered table-striped ">
            <thead>
            <tr>
                <th>ID</th>
                <th>ID cобытия</th>
                <th>Никнейм</th>
                <th>Дата добавления</th>

            </tr>
            </thead>
            <tbody>
            @foreach($membersToday as $memberToday)
                <tr>
                    <td>{{$memberToday->id}}</td>
                    <td>{{$memberToday->id_party}}</td>
                    <td>{{$memberToday->name_member}}</td>
                    <td>{{$memberToday->created_at}}</td>

                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
    </div>

    <p>Добавились к событиям:  {{$countMembers}}</p>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Таблица пользователей</h3>
        </div>
        <div class="box-body table-responsive ">
            <table id="example2" class="table table-bordered table-striped ">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>ID cобытия</th>
                    <th>Никнейм</th>
                    <th>Дата добавления</th>

                </tr>
                </thead>
                <tbody>
                @foreach($members as $member)
                    <tr>
                        <td>{{$member->id}}</td>
                        <td>{{$member->id_party}}</td>
                        <td>{{$member->name_member}}</td>
                        <td>{{$member->created_at}}</td>

                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <p>Комментарии, добавленные сегодня:  {{$countReviewsToday}}</p>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Таблица пользователей</h3>
        </div>
        <div class="box-body table-responsive ">
            <table id="example2" class="table table-bordered table-striped ">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>ID cобытия</th>
                    <th>Никнейм</th>
                    <th>Дата добавления</th>

                </tr>
                </thead>
                <tbody>
                @foreach($reviews as $review)
                    <tr>
                        {{--<td>{{$review->id}}</td>--}}
                        {{--<td>{{$review->id_party}}</td>--}}
                        {{--<td>{{$review->name_member}}</td>--}}
                        {{--<td>{{$review->created_at}}</td>--}}
                        <i>В разработке.</i>

                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>


@endsection
@section('script')
    <script>
        $(function () {
            $("#example1").DataTable();
        });
        $(function () {
            $("#example2").DataTable();
        });
    </script>

@endsection