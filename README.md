### Docker安装项目
    
    docker pull hyperf/hyperf:8.1-alpine-v3.18-swoole-v5.0
    
    docker run --name music -v D:\www\musicApi:/data/musicApi -p 9501:9501 -it --privileged -u root --entrypoint /bin/sh hyperf/hyperf:8.1-alpine-v3.18-swoole-v5.0
