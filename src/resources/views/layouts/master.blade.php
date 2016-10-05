<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>@yield('title', 'Home')</title>

        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/font-awesome.min.css">

        <script src="/js/jquery.min.js"></script>

        <style>
            li.not-read{background: #ddd;}
            body{min-height: 100vh; position: relative;}
            #popup{
                position: fixed; right: 0; bottom: 0; overflow-x: auto;
            }
            .chat-area{
                height: 180px; overflow-y: auto;
            }
            .chat_box, .group_box{
                width: 280px; border: 1px solid #ddd; padding: 10px;
                float: right; margin-right: 10px;
                background: #fff;
            }
            .room_chat_area{
                height: 300px; overflow-y: auto; background: #fefefe; border: 1px solid #ddd; padding: 10px;
            }
            .dropdown-menu{
                max-height: 80vh; overflow-x: auto;
            }
        </style>

        @yield('head')
    </head>
    <body>
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        Laravel 
                        @if(auth()->check())
                        <span class="small">(Login as: {{auth()->user()->name}})</span>
                        @endif
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li><a href="{{ url('/notify') }}">Notify</a></li>
                        <li><a href="{{ url('/chat') }}">Chat</a></li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                        <li><a href="{{ url('/auth/login') }}">Login</a></li>
                        <li><a href="{{ url('/auth/register') }}">Register</a></li>
                        @else
                        <li class="dropdown chat-alert notify-box">
                            <a id="chat-btn" href="#" class="dropdown-toggle notify-btn" data-toggle="dropdown"><i class="fa fa-envelope"></i> <span class="num">{{Auth::user()->notify()->whereIn('notify_type', ['group', 'chat'])->where('is_read', 0)->count()}}</span></a>
                            <ul class="dropdown-menu" role="menu">
                                @if(Auth::user()->notify)
                                <?php $user_notify = Auth::user()->notify()->whereIn('notify_type', ['group', 'chat'])->orderBy('updated_at', 'desc')->paginate(15); ?>
                                @foreach($user_notify as $note)
                                <li id="notify-chat-{{$note->id}}" data-id="{{$note->id}}" class="{{$note->is_read ? '' : 'not-read'}}"><a href="#" data-type="{{$note->notify_type}}" data-object-id="{{$note->object_id}}">{!!$note->content!!}</a></li>
                                @endforeach
                                @endif
                            </ul>
                        </li>
                        <li class="dropdown notify-alert notify-box">
                            <?php $normal_notify = Auth::user()->notify()->where('notify_type', 'normal')->orderBy('updated_at', 'desc')->paginate(15); ?>
                            <a id="notify-btn" href="#" class="dropdown-toggle notify-btn" data-toggle="dropdown"><i class="fa fa-bell"></i> <span class="num">{{Auth::user()->notify()->where('notify_type', 'normal')->where('is_read', 0)->count()}}</span></a>
                            <ul class="dropdown-menu" role="menu">
                                @if($normal_notify)
                                @foreach($normal_notify as $note)
                                <li id="notify-chat-{{$note->id}}" data-id="{{$note->id}}" class="{{$note->is_read ? '' : 'not-read'}}"><a href="#" data-type="{{$note->notify_type}}" data-object-id="{{$note->object_id}}">{!!$note->content!!}</a></li>
                                @endforeach
                                @endif
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/auth/logout') }}"><i class="fa fa-btn fa-sign-out"></i> Logout</a></li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <section id="main_body">
            <div class="container">
                @yield('content')
            </div>
        </section>

        <script src="/js/bootstrap.min.js"></script>


        <!-- Modal -->
        <div class="modal fade" id="add_room_modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Create room</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{route('chat.create_room')}}">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="room_name" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="group_box" class="hidden">
            <div class="chat_head"><span class="group_name">None</span> <button class="close pull-right">x</button></div>
            <hr />
            <ul class="chat-area list-unstyled">

            </ul>
            <hr />
            <form class="room_form">
                <div class="input-group">
                    <input type="text" class="room_text form-control" autocomplete="off" placeholder="Text here ...">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </span>
                </div>
            </form>
        </div>
        
        <div id="chat_box" class="hidden">
            <div class="chat_head"><span class="chat_with">None</span> <button class="close pull-right">x</button></div>
            <hr />
            <ul class="chat-area list-unstyled">

            </ul>
            <hr />
            <form class="chat_form">
                <div class="input-group">
                    <input type="text" class="chat-text form-control" autocomplete="off" placeholder="Text here ...">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </span>
                </div>
            </form>
        </div>

        <div id="popup">

        </div>

        @if(auth()->check())    
        <script src="http://localhost:8000/socket.io/socket.io.js"></script>
        <script>
        var current_user_id = '{{auth()->id()}}';
        var current_user_name = '{{auth()->user()->name}}';
        var _token = '{{csrf_token()}}';
        var _send_mess_url = "{{route('chat.send_mess')}}";
        var _get_mess_url = "{{route('chat.get_mess')}}";
        var _init_chat_group_url = "{{route('chat.init_chat_group')}}";
        var _read_notify_url = "{{route('chat.set_read_notify')}}";
        var socket = io.connect('{{request()->getHost()}}:8000');
        </script>
        <script src="/clients/chat.js"></script>
        <script src="/clients/room.js"></script>
        <script src="/clients/notify.js"></script>
        @else
        <script>
        (function ($) {
            $('.list-users li a, .list-rooms li a, #create_room_btn').on('click', function (e) {
                e.preventDefault();
                alert('Login!');
            });
        })(jQuery);
        </script>
        @endif


        @yield('foot')
    </body>
</html>
