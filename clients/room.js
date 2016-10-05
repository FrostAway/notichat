(function ($) {

    socket.on('new_group_chat', function (data) {
        var room_area = $('body').find('#chat_group_' + data.room.id + ' .chat-area');
        if (room_area.length > 0) {
            room_area.append('<li><strong>' + data.message.from_user_name + ': </strong> ' + data.message.message + '</li>');
            scrollBottom(room_area);
        }
    });

    socket.on('init-chat-group:' + current_user_id, function (data) {
        socket.emit('join-chat-group', {room: data.room.name});
    });

    $('.list-rooms li a').click(function (e) {
        e.preventDefault();
        var room_id = $(this).data('id');
        var room_name = $(this).data('name');
        if ($('body').find('#chat_group_' + room_id).length > 0) {
            return;
        }
        add_group_box(room_name, room_id);
        $.ajax({
            url: _init_chat_group_url,
            type: 'GET',
            data: {
                room_id: room_id
            }
        });
    });

    $('body').on('click', '.group_box .close', function (e) {
        e.preventDefault();
        var group_box = $(this).closest('.group_box');
        socket.emit('leave_group', {room: group_box.find('.group_name').text()});
        group_box.remove();
    });

    $('body').on('submit', '.room_form', function () {
        var text = $(this).find('.room_text');
        var message = text.val();
        var group_id = $(this).closest('.group_box').attr('group-id');
        text.val('');
        $.ajax({
            url: _send_mess_url,
            type: 'POST',
            data: {
                _token: _token,
                room_id: group_id,
                message: message,
                type: 'group'
            }
        });
        return false;
    });

})(jQuery);


function add_group_box(box_name, room_id) {
    room_id = room_id || 0;
    if ($('body').find('#chat_group_' + room_id).length > 0) {
        return;
    }
    var chat_box = $('#group_box').clone();
    chat_box.removeAttr('id').removeClass('hidden').addClass('group_box');
    chat_box.attr('id', 'chat_group_' + room_id);
    chat_box.attr('group-id', room_id);
    chat_box.find('.group_name').html(box_name);
    chat_box.appendTo('#popup');
    load_group_messages(room_id, chat_box);
}

function load_group_messages(room_id, group_box) {
    $.ajax({
        url: _get_mess_url,
        type: 'GET',
        data: {
            room_id: room_id
        },
        success: function (data) {
            var html = '';
            for (var i in data) {
                html += '<li><strong>' + data[i].from_user_name + ': </strong> ' + data[i].message + '</li>';
            }
            group_box.find('.chat-area').html(html);
            scrollBottom(group_box.find('.chat-area'));
        }
    });
}

