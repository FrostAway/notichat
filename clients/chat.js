(function ($) {
    var list_user_el = $('.list-users li a');

    socket.on('reciever_mess_to_' + current_user_id, function (data) {
        console.log('reciever message from '+data.from_user.name);
        var box_name = 'chat_with_' + data.from_user.id;
        append_mess(box_name, data);
    });

    socket.on('reciever_mess_from_' + current_user_id, function (data) {
        console.log('send message to '+data.to_user.name);
        var box_name = 'chat_with_' + data.to_user.id;
        append_mess(box_name, data);
        $.ajax({
            url: _send_mess_url,
            type: 'POST',
            data: {
                _token: _token,
                message: data.message,
                to_user: data.to_user.id,
                type: 'chat'
            }
        });
    });

    list_user_el.on('click', function () {
        var to_id = $(this).data('id');
        if (to_id === current_user_id) {
            return;
        }
        var to_name = $(this).data('name');
        var to_user = {id: to_id, name: to_name};
        add_chat_box(to_user);
    });

    $('body').on('click', '.chat_box .close', function (e) {
        e.preventDefault();
        var chat_box = $(this).closest('.chat_box');
        chat_box.remove();
    });

    $('body').on('submit', '.chat_form', function () {
        var chat_box = $(this).closest('.chat_box');
        var to_user_id = chat_box.data('userId');
        var to_user_name = chat_box.data('userName');
        var el_mess = $(this).find('.chat-text');
        var mess = el_mess.val();
        socket.emit('request_chat_message', {
           from_user: {id: current_user_id, name: current_user_name},
           to_user: {id: to_user_id, name: to_user_name},
           message: mess
        });
        el_mess.val('');
        return false;
    });
    
})(jQuery);

function add_chat_box(to_user) {
    var box_name = 'chat_with_' + to_user.id;
    if ($('body').find('#' + box_name).length > 0) {
        return;
    }
    var chat_box = $('#chat_box').clone();
    chat_box.removeAttr('id').removeClass('hidden').addClass('chat_box');
    chat_box.attr('id', box_name).attr('data-user-id', to_user.id).attr('data-user-name', to_user.name);
    chat_box.find('.chat_with').html(to_user.name);
    chat_box.appendTo('#popup');
    load_messages(current_user_id, to_user.id, chat_box);
}

function append_mess(box_name, data) {
    var chat_area = $('body').find('#' + box_name + ' .chat-area');
    var mess = '<li><strong>' + data.from_user.name + ': </strong> ' + data.message + '</li>';
    chat_area.append(mess);
    scrollBottom(chat_area);
}

function load_messages(from_user_id, to_user_id, chat_box) {
    $.ajax({
        url: _get_mess_url,
        type: 'GET',
        data: {
            to_user: to_user_id,
            from_user: from_user_id
        },
        success: function (data) {
            var html = '';
            for (var i in data) {
                html += '<li><strong>' + data[i].from_user_name + ': </strong> ' + data[i].message + '</li>';
            }
            chat_box.find('.chat-area').html(html);
            scrollBottom(chat_box.find('.chat-area'));
        }
    });
}

function scrollBottom(element) {
    element.animate({scrollTop: element.prop('scrollHeight')}, 100);
}