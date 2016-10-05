@extends('layouts.master')

@section('head')

@stop

@section('content')

<h1>Notify</h1>

<div class="row">
    <div class="col-sm-4">
        <ul id="notify">
            
        </ul>
    </div>
    <div class="col-sm-4">
        @if($users)
        <button type="button" class="btn btn-primary" id="make_notify">Make notify to user</button>
        <select id="note_user" class="">
            @foreach($users as $user)
                <option value="{{$user->id}}">{{$user->name}}</option>
            @endforeach
        </select>
        @endif
    </div>
</div>

@stop

@section('foot')
<script src="http://localhost:8000/socket.io/socket.io.js"></script>
<script>
    @if(auth()->check())
        var current_id = "{{auth()->id()}}";
    @else
        var current_id = null;
    @endif
    (function ($) {
        var socket = io.connect('http://localhost:8000');
        socket.on('connected', function (data) {
            console.log(data.message);
        });
        socket.on('like-event-'+current_id+':App\\Events\\LikeEvent', function (data) {
            console.log(data);
            $('#notify').append('<li><strong>'+data.name+': </strong> <span>'+data.message+'</span></li>');
        });
        var event_route = "{{route('notify.make')}}";
        var num = 0;
        $('#make_notify').click(function (e) {
            e.preventDefault();
            num ++;
            var user_id = $('#note_user').val();
            $.ajax({
                url: event_route,
                data: {
                    num: num,
                    user_id: user_id
                }
            });
        });
        var chat_note_num = $('#notify-btn .num');
        $('body').on('click', '.notify-alert ul a', function (e) {
            e.preventDefault();
                var el_li = $(this).parent();
                var note_id = el_li.data('id');
                if (el_li.hasClass('not-read')) {
                    $.ajax({
                        url: _read_notify_url,
                        type: 'GET',
                        data: {
                            note_id: note_id
                        },
                        success: function (data) {
                            el_li.removeClass('not-read');
                            chat_note_num.text(data.count);
                        }
                    });
                }
        });
    })(jQuery);
</script>
@stop

