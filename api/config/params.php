<?php
return [
    'adminEmail' => 'admin@example.com',
    // token 有效期默认1天
    'user.apiTokenExpire' => 1*24*3600,
    'WECHAT' => [
        'APPID' => 'wx9c044f98156b8e20',
        'APPSECRET' => '3fd73bb4cd76af92528e3c393435e2ab',
    ],
    'DEFAULT_TARGET_URL' => 'http://localhost:8300/stopCar',
    'FREE_TIMES' => 3, //首个微信用户的免费次数
    'DISTANCE' => 500, //默认距离（米）
];
