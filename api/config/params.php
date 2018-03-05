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

    'WX_PAY' => [
        'APP_ID' => 'wx9c044f98156b8e20',
        'APP_SECRET' => '3fd73bb4cd76af92528e3c393435e2ab',
        'WX_PAY_APP_ID' => 'wx9c044f98156b8e20',
        'WX_PAY_APP_SECRET' => '3fd73bb4cd76af92528e3c393435e2ab',
        'WX_PAY_MERCHANT_ID' => '1355373002',
        'WX_PAY_KEY_FOR_SIGN' => 'l2hq3qtidf11kza2hqvmhtwdvp5r4xp4',
        'WX_CERT_PATH' => '/home/deploy/check_in_backend/pay/apiclient_cert.pem',
        'WX_KEY_PATH' => '/home/deploy/check_in_backend/pay/apiclient_key.pem',
        'PAY_NOTIFY_URL' => '/index.php?r=activity/notify',
    ]
];
