## Notify and chat realtime

    composer require lamnv/noti-chat dev-master      

## Install

1. Config

    config/app.php: 

    providers => [ ... Lamnv\NotiChat\NotifyProvider::class ... ]

    php artisan vendor:publish    

2. predis/predis

    composer require predis/predis    

3. Nodejs/socket.io

    cd public/server    
    npm install    

4. Redis

    redis-cli.exe   

## Run

    node public/server/server.js    
    redis-cli.exe