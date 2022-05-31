<?php

return [

    /**
     * 选择model 请求地址
     * local 本地 ， 远端填写 hosts 中的键名
     */
    'host' => env("MODEL_PLUS_HOST","local"),

    /**
     * 远端密钥
     * 远端程式的Encryption Key
     */
    'secret' =>  env("MODEL_PLUS_KEY",env('APP_KEY')),

    /**
     * 远端地址
     * host IP地址
     * port 端口
     * timeout 链接超时
     */
    'hosts' => [
/*        'cloud' => [
            'host' => '127.0.0.1',
            'port' => '8080',
            'timeout' => 3000
        ]*/
    ]

];
