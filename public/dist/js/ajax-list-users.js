$(document).ready(function () {

    var url = "/admin/users";



    //display modal form for task editing
    $('body').on('click', '.open-modal', function () {
        $('#myModal').modal('show');
        var user_id = $(this).val();

        $.get(url + '/' + user_id, function (data) {
            //success data
            console.log(data.ban.length);
            var  historyBans = '';

                if (data.ban.length != 0) {
                    for(k = data.ban.length-1; k>=0; k--) {
                        if (data.ban[k].imei_ban == null) {
                            var imeiBan = '<i>Отсутсвует</i>';
                        } else {
                            var imeiBan = 'Да';
                        }
                        if (data.ban[k].nik_name_ban == null) {
                            var nikNameBan = '<i>Отсутсвует</i>';
                        } else {
                            var nikNameBan = 'Да';
                        }
                        if (data.ban[k].status == 0) {
                            var status = '<i>В истории</i>';
                        } else {
                            var status = 'ДЕЙСТВУЕТ';
                        }
                        historyBans += '<hr><p><b>IMEI бан:</b> ' + imeiBan
                            + '<br> <b>Бан по нику</b> ' + nikNameBan + '<br> <b>Статус:</b> ' + status  +
                            '<br> <b>Причина блокировки:</b> ' + data.ban[k].reason
                             + '<br><b>Дата выдачи: </b>' + data.ban[k].created_at + '</p>';
                    }
                } else {
                     historyBans += '<i>Нет данных</i>'
                }

            var  historyNotes = '';
                console.log(data.note);
            if (data.note.length != 0) {
                for(k = data.note.length-1; k>=0; k--) {
                    historyNotes += '<hr><p><b>Заметка:</b> ' + data.note[k].note + '<br><b>Дата создания: </b>' + data.note[k].created_at + '</p>';
                }
            } else {
                historyNotes += '<i>Нет данных</i>';
            }

            if(data.sex == 'm'){
                console.log('Test',data.sex);
                $('#sex').val('m').change();
               // $("#sex option[value='m']").attr("selected", "selected");
            }else {
                console.log('Test',data.sex);
                $('#sex').val('w').change();
              //  $("#sex option[value='w']").attr("selected", "selected");
            }

            $('#history-bans').html(historyBans);
            $('#history-notes').html(historyNotes);
            console.log(data);
            $('#user_id').val(data.id);
            $('#first_name').val(data.first_name);
            $('#nik_name').val(data.nik_name);
            $('#age').val(data.age);
            $('#rating').val(data.rating);
            $('#balance').val(data.balance);
            $('#imei').val(data.imei);
            $("#image").attr("src", data.image);
            $('#date_update_rating').val(data.date_update_rating);
            $('#ads_disabled').val(data.ads_disabled);
            $('#created_at').val(data.created_at);
            $('#updated_at').val(data.updated_at);
            $('#btn-save').val("update");

        })
    });

    function getAge( y, m, d) {
        if(m > date('m') || m == date('m') && d > date('d'))
            return (date('Y') - y - 1);
        else
            return (date('Y') - y);
    }

    $('body').on('click', '.modal-add-note', function () {
        $('#modelAddNote').modal('show');
        var user_id = $(this).val();

        $.get(url + '/' + user_id, function (data) {
            //success data
            console.log(data);
            $('#user_id_note').val(data.id);
        })
    });

    $('body').on('click', '.delete-user', function () {
        var user_id = $(this).val();

        if (confirm("Подтвердите свое действие.")) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                id: user_id,
            }
            var type = "delete";
            var my_url = url + '/' + user_id;
            $.ajax({

                type: type,
                url: my_url,
                data: formData,
                dataType: 'json',
                success: function (data) {
                        console.log(user_id);
                        $("#user_app_" + user_id).replaceWith('');
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }


    });

    function deleteBan(e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })
        var formData = {
            id: $('#user_id').val(),
        }
        var user_id = $('#user_id').val();
        var type = "delete";
        var my_url = url + '/ban/' + user_id;
        $.ajax({

            type: type,
            url: my_url,
            data: formData,
            dataType: 'json',
            success: function (data) {

                if(data.sex == "m"){
                    sex = "Мужчина";
                }else {
                    sex = "Женщина";
                }

                //0 - обычный, 1 - админ, 2 - модератор, 3 - забанен
                if(data.status == '0'){
                    status = 'Обычный пользователь';
                }else if(data.status == '1'){
                    status = 'Администратор';
                }else if(data.status == '2'){
                    status = 'Модератор';
                }else{
                    status = 'Заблокированный';
                }


                $("#user_app_" + data.id).replaceWith(formUser(data));
                $('#myModal').modal('hide')
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    function formUser(data) {
        var user = '<tr  id="user_app_' + data.id + '"><td>' + data.id + '</td><td>' + data.first_name + '</td><td>' + data.nik_name + '</td><td>' + 'Информация недоступна' + '</td><td>' + data.age + '</td><td>' + status + '</td><td>' + data.created_at + '</td>';
        user += '<td class="text-center"><div class="btn-group">';
        user += '<button type="button" value="' + data.id + '" class="btn btn-default open-modal">Посмотреть информацию</button>';
        user += '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';
        user += '   <span class="caret"></span>';
        user += '   <span class="sr-only">Toggle Dropdown</span>';
        user += ' </button>';
        user += '<ul class="dropdown-menu" role="menu">';
        user += ' <li><button type="button"" class="modal-add-note" value="' + data.id + '">Добавить заметку</button></li>';
        user += ' <li><button type="button" class="delete-user" value="' + data.id + '">Удалить пользователя</button></li>';
        user += '</ul>';
        user += ' </div> </td></tr>';

        return user;

    }

    //display modal form for creating new task
    $('#btn-add').click(function () {
        $('#btn-save').val("add");
        $('#frmTasks').trigger("reset");
        $('#myModal').modal('show');
    });


    $("#btn-disabled-ban").click(function (e) {
        if (confirm("Подтвердите свое действие.")) {
            deleteBan(e);
        }
    });

    $("#btn-save").click(function (e) {
        if (confirm("Подтвердите свое действие.")) {
            saveData(e);
        }
    });

    $("#btn-ban-imei").click(function (e) {
        if (confirm("Подтвердите свое действие.")) {
            ban(e, "imei");
        }
    });

    $("#btn-ban-nikname").click(function (e) {
        if (confirm("Подтвердите свое действие.")) {
            ban(e, "nik_name");
        }
    });

    $("#btn-add-note").click(function (e) {
        if (confirm("Подтвердите свое действие.")) {
            addNote(e);
        }
    });




    function saveData(e) {
        var sex;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })

        e.preventDefault();
        var status = document.getElementById("status");
        var sex = document.getElementById("sex");

        var formData = {
            id: $('#user_id').val(),
            first_name: $('#first_name').val(),
            nik_name: $('#nik_name').val(),
            balance: $('#balance').val(),
            sex: sex.options[sex.selectedIndex].value,
            status: status.options[status.selectedIndex].value,
        }


        console.log(sex);
        //used to determine the http verb to use [add=POST], [update=PUT]
        var state = $('#btn-save').val();

        var type = "POST"; //for creating new resource
        var user_id = $('#user_id').val();
        var my_url = url;

        if (state == "update") {
            type = "PUT"; //for updating existing resource
            my_url += '/' + user_id;
        }

        console.log(formData);

        $.ajax({

            type: type,
            url: my_url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                if(data.sex == 'm'){
                    sex = 'Мужчина';
                }else {
                    sex = 'Женщина';
                }
                //0 - обычный, 1 - админ, 2 - модератор, 3 - забанен
                if(data.status == '0'){
                    status = 'Обычный пользователь';
                }else if(data.status == '1'){
                    status = 'Администратор';
                }else if(data.status == '2'){
                    status = 'Модератор';
                }else{
                    status = 'Заблокированный';
                }

                console.log('Данные пришли: ', data);



                if (state == "add") { //if user added a new record
                    $('#tasks-list').append(user);
                } else { //if user updated an existing record

                    $("#user_app_" + data.id).replaceWith(formUser(data));
                }


                $('#myModal').modal('hide')
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }


    function ban(e, state) {
        var sex;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })

        e.preventDefault();

        var reason = prompt('Введите причину блокировки пользователя');
        var formData = {
            id: $('#user_id').val(),
            type_ban: state,
            nik_name: $('#nik_name').val(),
            imei: $('#imei').val(),
            reason: reason,
        }
        if(reason == null){
            return;
        }
        var user_id = $('#user_id').val();
        //used to determine the http verb to use [add=POST], [update=PUT]
        type = "PUT"; //for updating existing resource
        var my_url = url + '/ban/' + user_id;

        $.ajax({

            type: type,
            url: my_url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log('Данные пришли: ', data);
                if(data.sex == "m"){
                    sex = "Мужчина";
                }else {
                    sex = "Женщина";
                }

                //0 - обычный, 1 - админ, 2 - модератор, 3 - забанен
                if(data.status == '0'){
                    status = 'Обычный пользователь';
                }else if(data.status == '1'){
                    status = 'Администратор';
                }else if(data.status == '2'){
                    status = 'Модератор'
                }else{
                    status = 'Заблокированный'
                }

                console.log(sex);

                $("#user_app_" + data.id).replaceWith(formUser(data));

                $('#myModal').modal('hide')
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    function addNote(e) {


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })

        e.preventDefault();


        var formData = {
            id: $('#user_id_note').val(),
            note: $('#note').val(),
        }

        var user_id = $('#user_id_note').val();
        //used to determine the http verb to use [add=POST], [update=PUT]
        type = "PUT"; //for updating existing resource
        var my_url = url + '/note/' + user_id;

        $.ajax({

            type: type,
            url: my_url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log('Данные пришли: ', data);

                $('#modelAddNote').modal('hide')
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }



});