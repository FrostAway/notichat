## Notify and chat realtime

    composer require lamnv/noti-chat dev-master      

## Install

### Config

config/app.php: 

    providers => 
    [ ... Lamnv\NotiChat\NotifyProvider::class ... ]

    php artisan vendor:publish   

    php artisan migrate  

add to .env file:

    BROADCAST_DRIVER=redis

### Nodejs/socket.io

    cd public/server    
    npm install    

### Redis
    
Install redis on windows : [download link](https://github.com/MSOpenTech/redis/releases/download/win-3.0.504/Redis-x64-3.0.504.msi). 
Add environment variable and Run command: 

    "redis-cli.exe"

## Run
      
    redis-cli.exe
    node public/server/server.js