(function ($) {

    var chat_alert = $('.chat-alert ul');
    var notify_alert = $('.notify-alert ul');
    var chat_note_num = $('#chat-btn .num');
    var notify_note_num = $('#notify-btn .num');
    socket.on('notify-chat-' + current_user_id, function (data) {
        var notify = data.notify;
        console.log(notify);
        if (notify.notify_type === 'chat' || notify.notify_type === 'group') {
            var old_num = parseInt(chat_note_num.text() || 0);
            chat_note_num.text(old_num + 1);
            var el_note = $('#notify-chat-' + notify.id);
            if (el_note.length > 0) {
                el_note.detach().prependTo(chat_alert);
            } else {
                chat_alert.prepend('<li id="notify-chat-' + notify.id + '" data-id="' + notify.id + '" class="' + (notify.is_read === 1 ? '' : 'not-read') + '"><a href="#" data-type="' + notify.notify_type + '" data-object-id="' + notify.object_id + '">' + notify.content + '</a></li>');
            }
        } else if (notify.notify_type === 'normal') {
            var old_num = parseInt(notify_note_num.text() || 0);
            notify_note_num.text(old_num + 1);
            notify_alert.prepend('<li id="notify-chat-' + notify.id + '" data-id="' + notify.id + '" class="' + (notify.is_read === 1 ? '' : 'not-read') + '"><a href="#" data-type="' + notify.notify_type + '" data-object-id="' + notify.object_id + '">' + notify.content + '</a></li>');
        }
    });

    $('body').on('click', '.chat-alert ul a', function (e) {
        e.preventDefault();
        var el_li = $(this).parent();
        var type = $(this).data('type');
        var obj_id = $(this).data('objectId');
        var name = $(this).find('strong').text();
        var note_id = el_li.data('id');
        if (type === 'chat') {
            add_chat_box({id: obj_id, name: name});
        } else if (type === 'group') {
            socket.emit('join-chat-group', {room: name});
            add_group_box(name, obj_id);
        }
//        if (el_li.hasClass('not-read')) {
        $.ajax({
            url: _read_notify_url,
            type: 'GET',
            data: {
                note_id: note_id,
                set_by: 'note_id'
            },
            success: function (data) {
                el_li.removeClass('not-read');
                chat_note_num.text(data.count);
            }
        });
//        }
    });

    $('body').on('click', '.chat_form .chat-text', function () {
        var chat_box = $(this).closest('.chat_box');
        var to_user_id = chat_box.data('userId');
        if (parseInt(chat_note_num.text())) {
            $.ajax({
                url: _read_notify_url,
                type: 'GET',
                data: {
                    notify_type: 'chat',
                    from_user_id: current_user_id,
                    to_obj_id: to_user_id,
                    set_by: 'user_id'
                },
                success: function (data) {
                    chat_note_num.text(data.count);
                }
            });
        }
    });

    $('body').on('click', '.room_form .room_text', function () {
        var group_box = $(this).closest('.group_box');
        var group_id = group_box.attr('group-id');
        if (parseInt(chat_note_num.text())) {
            $.ajax({
                url: _read_notify_url,
                type: 'GET',
                data: {
                    notify_type: 'group',
                    from_user_id: current_user_id,
                    to_obj_id: group_id,
                    set_by: 'user_id'
                },
                success: function (data) {
                    chat_note_num.text(data.count);
                }
            });
        }
    });

})(jQuery);

