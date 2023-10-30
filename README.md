### Docker安装项目
    docker pull hyperf/hyperf:8.0-alpine-v3.15-swoole
    
    docker run --name music -v D:\www\musicApi:/data/project -p 9501:9501 -it --entrypoint /bin/sh hyperf/hyperf:8.0-alpine-v3.15-swoole