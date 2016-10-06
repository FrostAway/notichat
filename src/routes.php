<?php

$route_group = ['namespace' => 'App\Http\Controllers'];
$version = app()->version();
if (!preg_match('/5\.1/', $version)){
   $route_group['middleware'] = 'web';
}

Route::group($route_group, function () {

    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');

    Route::group(['middleware' => 'auth'], function () {
        Route::group(['prefix' => 'notify', 'as' => 'notify.'], function() {
            Route::get('/', ['as' => 'index', 'uses' => 'NotifyController@index']);
            Route::get('/make-notify', ['as' => 'make', 'uses' => 'NotifyController@makeNotify']);
        });
        Route::group(['prefix' => 'chat', 'as' => 'chat.'], function() {
            Route::get('/', ['as' => 'index', 'uses' => 'ChatController@index']);
            Route::post('/send-message', ['as' => 'send_mess', 'uses' => 'ChatController@sendMessage']);
            Route::get('/get-message', ['as' => 'get_mess', 'uses' => 'ChatController@getMessage']);
            Route::post('/create-room', ['as' => 'create_room', 'uses' => 'ChatController@createRoom']);
            Route::get('/init-chat-group', ['as' => 'init_chat_group', 'uses' => 'ChatController@initChatGroup']);
            Route::get('/set-read-notify', ['as' => 'set_read_notify', 'uses' => 'ChatController@setReadNotify']);
        });
    });

    $auth_prefix = ['', 'auth'];
    foreach ($auth_prefix as $prefix) {
        Route::group(['prefix' => $prefix, 'namespace' => 'Auth'], function () {
// Authentication routes...
            Route::get('login', 'AuthController@getLogin');
            Route::post('login', 'AuthController@postLogin');
            Route::get('logout', 'AuthController@getLogout');

// Registration routes...
            Route::get('register', 'AuthController@getRegister');
            Route::post('register', 'AuthController@postRegister');

            Route::controllers([
                'password' => 'PasswordController',
            ]);
        });
    }
});
