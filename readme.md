# Team Management

## Usage

* Clone the repo and cd into it

* Install all the dependencies using composer
```sh
$ composer install
```
* rename or copy ``.env.example`` to ``.env``
```sh
$ cp .env.example .env
```
* Generate a new application key  
```sh 
$ php artisan key:generate
```
>## 1. Setup your database connection in your `.env` file

>## 2. Create a virtual host under dev.teamManagement.com 

### If you're using nginx 
```sh
server {
    listen 80;
    root {BASE_PATH}/teamManagement/public;
    index index.php index.html index.htm;
    server_name dev.teamManagement.com;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9001;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
### If you choose another server_name or if you prefer just to use #php artisan serve make sure to update the BASE_URL here:

### ``` teamManagement/team-front/dev.env.js```
```sh
 BASE_API: '"http://127.0.0.1:8000/api/"'
 ```

>## 3. Run the migrations with the factory to fill the db  

```sh 
$ php artisan migrate --seed
```

>## 4. Finally
```sh
$ cd team-front
$ npm install
$ npm run build
#if you want to use the localhost use the next lines: [make sure to update the BASE_API constant after the previous step]
$ php artisan serve
> If you face some issues with the assets
$ php -S localhost:8000 -t public
```

* Then navigate to the server
```sh
dev.teamManagement.com 
or
[your custom server address]
```
* Admin credentials 
```
email: anass.nadir@gmail.com
password: 123456
```
