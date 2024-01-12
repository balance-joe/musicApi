### Docker安装项目
    
#### 下载镜像
```shell
  docker pull hyperf/hyperf:8.1-alpine-v3.18-swoole-v5.0
```

##### 运行镜像,请注意更换目录地址
```shell
  docker run --name music -v ${PWD}:/data/musicApi -p 9501:9501 -it --privileged -u root --entrypoint /bin/sh hyperf/hyperf:8.1-alpine-v3.18-swoole-v5.0
```

##### 进入镜像
```shell
  docker exec -ti music /bin/bash
```

##### 运行项目
```shell
  cd /data/musicApi
  php bin/hyperf.php server:watch
```


