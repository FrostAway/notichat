var express = require('express');
var app = express();
var server = require('http').createServer(app);
var config = require('./config/config');
var io = require('socket.io')(server);
var Redis = require('ioredis');
var redis = new Redis();

server.listen(config.port, config.ip, function () {
    console.log('Server running on port ' + config.port);
});

io.on('connection', function (socket) {
    socket.emit('connected', {message: 'Connected'});

    socket.on('leave_group', function (data) {
        socket.leave(data.room);
    });

    socket.on('join-chat-group', function (data) {
        socket.join(data.room);
    });
    
    socket.on('request_chat_message', function (data) {
        var from_user = data.from_user;
        var to_user = data.to_user;
        io.emit('reciever_mess_to_' + to_user.id, data);
        io.emit('reciever_mess_from_' + from_user.id, data);
    });
});

redis.psubscribe('*', function (err, count) {

});

redis.on('pmessage', function (subscribed, channel, result) {
    result = JSON.parse(result);
    var data = result.data;
    switch (result.event) {
        case 'App\\Events\\LikeEvent':
            io.emit(channel + ':' + result.event, result.data.message);
            break;
        case 'App\\Events\\SendMess':
            if (data.type === 'group') {
                io.sockets.in(data.room.name).emit('new_group_chat', data);
            }
            break;
        case 'App\\Events\\InitChatGroup':
            var user_ids = data.user_ids;
            for (var i in user_ids) {
                io.emit(channel + ':' + user_ids[i], data);
            }
            break;
        default :
            io.emit(channel, data);
            break;
    }
});

