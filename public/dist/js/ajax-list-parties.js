$(document).ready(function () {

    var url = "/admin/parties";



    //display modal form for task editing
    $('body').on('click', '.open-modal', function () {
        $('#myModal').modal('show');
        var party_id = $(this).val();

        $.get(url + '/' + party_id, function (data) {
            //success data
            console.log(data.user.nik_name);
            $('#party_id').val(data.id);
            $('#title_party').val(data.title_party);
            $('#description_party').val(data.description_party);
            $('#nik_name').val(data.user.nik_name);
            if(data.alcohol == 1){
                $('#alcohol').attr("checked",'checked') ;
            }
            $('#coordinates').val(data.coordinates);
            $('#count_people_max').val(data.max_count_people);
            $('#date_time').val(data.date_time);
            $("#status").val(data.status).change();
            $('#btn-save').val("update");



        })
    });


    $('body').on('click', '.modal-add-note', function () {
        $('#modelAddNote').modal('show');
        var user_id = $(this).val();

        $.get(url + '/' + user_id, function (data) {
            //success data
            console.log(data);
            $('#user_id_note').val(data.id);
        })
    });

    $('body').on('click', '.delete-party', function () {
        var party_id = $(this).val();

        if (confirm("Подтвердите свое действие.")) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                id: party_id,
            }
            var type = "delete";
            var my_url = url + '/' + party_id;
            $.ajax({

                type: type,
                url: my_url,
                data: formData,
                dataType: 'json',
                success: function (data) {

                    $("#party_" + party_id).replaceWith('');
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }


    });


    function formUser(data) {
        var user = '<tr  id="party_' + data.id + '"><td>' + data.id + '</td><td>' + data.title_party + '</td><td>' + data.user.nik_name + '</td><td>' + data.coordinates + '</td><td>' + data.datetime + '</td>';
        user += '<td class="text-center"><div class="btn-group">';
        user += '<button type="button" value="' + data.id + '" class="btn btn-default open-modal">Посмотреть информацию</button>';
        user += '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';
        user += '   <span class="caret"></span>';
        user += '   <span class="sr-only">Toggle Dropdown</span>';
        user += ' </button>';
        user += '<ul class="dropdown-menu" role="menu">';
        //user += ' <li><button type="button" class="modal-add-note" value="' + data.id + '">Добавить заметку</button></li>';
        user += ' <li><button type="button" class="delete-party" value="' + data.id + '">Удалить вписку</button></li>';
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



    $("#btn-save").click(function (e) {
        if (confirm("Подтвердите свое действие.")) {
            saveData(e);
        }
    });





    function saveData(e) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })

        e.preventDefault();
        var status = document.getElementById("status");

        var alcohol = 0;

        if ($('#alcohol').is(":checked"))
        {
            alcohol = 1;
        }

        var formData = {
            id: $('#party_id').val(),
            nik_name: $('#nik_name').val(),
            title_party:  $('#title_party').val(),
            description_party: $('#description_party').val(),
            alcohol:  alcohol,
            max_count_people:$('#count_people_max').val(),
            date_time:$('#date_time').val(),
            status: status.options[status.selectedIndex].value,
        }



        //used to determine the http verb to use [add=POST], [update=PUT]
        var state = $('#btn-save').val();

        var type = "POST"; //for creating new resource
        var party_id = $('#party_id').val();
        var my_url = url;

        if (state == "update") {
            type = "PUT"; //for updating existing resource
            my_url += '/' + party_id;
        }

        console.log(formData);

        $.ajax({

            type: type,
            url: my_url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log('Данные пришли: ', data);


                if (state == "add") { //if user added a new record
                    $('#tasks-list').append(user);
                } else { //if user updated an existing record

                    $("#party_id" + data.id).replaceWith(formUser(data));
                }


                $('#myModal').modal('hide')
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }






});