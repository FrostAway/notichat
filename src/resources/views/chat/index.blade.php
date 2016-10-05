@extends('layouts.master')

@section('title', 'Chat')

@section('content')

<?php
function list_users ($users) {
    $rs = '';
    foreach ($users as $user) {
        $rs .= $user->name.', ';
    }
    return (trim($rs, ', '));
}
?>

<div class="row">
    <div class="col-sm-4">
        <h1 class="page-header">Select User</h1>
        @if($users)
        <ul class="list-users">
            @foreach($users as $user)
            <li><a data-id="{{$user->id}}" data-name="{{$user->name}}" href="javascript:void(0)">{{$user->name}}</a></li>
            @endforeach
        </ul>
        @endif
    </div>
    <div class="col-sm-8">
        <h1 class="page-header">Select Group</h1>
        @if($rooms)
        <ul class="list-rooms">
            @foreach($rooms as $room)
            <li><a data-id="{{$room->id}}" data-name="{{$room->name}}" href="javascript:void(0)">{{$room->name}}</a> | ({{list_users($room->users)}})</li>
            @endforeach
        </ul>
        @endif
        <!--<div><button id="create_room_btn" class="btn btn-primary" data-toggle="modal" data-target="#add_room_modal">Create room</button></div>-->
    </div>
</div>

@stop
