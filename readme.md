后台: http://localhost/adm888/login.php 
esyyw  123456789


安装php

curl -s http://php-osx.liip.ch/install.sh | bash -s 5.6

/usr/local/php5/

sudo /usr/local/php5/sbin/php-fpm

http://193.112.74.74/phpMyAdmin_pang_he/

# nginx

```
server {
    listen       80;
    server_name  kaxiaoquan.com;
    charset utf-8;
    access_log  /data/wwwlogs/kaxiaoquan.com.log  combined;
    root /data/wwwroot/zeai;
    index index.html index.htm index.php;

    location ~ \.php$ {
        fastcgi_pass   unix:/dev/shm/php-cgi.sock;
        #fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }

    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf|flv|mp4|ico)$ {
      expires 30d;
      access_log off;
    }
    location ~ .*\.(js|css)?$ {
      expires 7d;
      access_log off;
    }
    location ~ /\.ht {
      deny all;
    }
}
```