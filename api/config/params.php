<?php
return [
    'adminEmail' => 'admin@example.com',
    // token 有效期默认1天
    'user.apiTokenExpire' => 1*24*3600,
    'WECHAT' => [
        'APPID' =>  env('APP_ID','wx9c044f98156b8e20'),
        'APPSECRET' => env('APP_SECRET','3fd73bb4cd76af92528e3c393435e2ab'),
    ],
    'DEFAULT_TARGET_URL' =>env('DEFAULT_TARGET_URL','http://localhost:8300/stopCar'),
    'FREE_TIMES' => 3, //首个微信用户的免费次数
    'DISTANCE' => 500, //默认距离（米）

    'WX_PAY' => [
        'APP_ID' => env('APP_ID','wx064cd93da2e5faed'),
        'APP_SECRET' => env('APP_SECRET','04b01064c6c584541f64b2280fba8a56'),
        'WX_PAY_APP_ID' =>  env('WX_PAY_APP_ID','wx9c044f98156b8e20'),
        'WX_PAY_APP_SECRET' =>  env('WX_PAY_APP_SECRET','wx9c044f98156b8e20'),
        'WX_PAY_MERCHANT_ID' => env('WX_PAY_MERCHANT_ID','wx9c044f98156b8e20'),
        'WX_PAY_KEY_FOR_SIGN' => env('WX_PAY_KEY_FOR_SIGN','wx9c044f98156b8e20'),
        'WX_CERT_PATH' =>  env('WX_CERT_PATH','/home/deploy/check_in_backend/pay/apiclient_cert.pem'),
        'WX_KEY_PATH' =>  env('WX_KEY_PATH','/home/deploy/check_in_backend/pay/apiclient_key.pem'),
        'PAY_NOTIFY_URL' => env('PAY_NOTIFY_URL','/api/v1/user/notify'),
    ]
];
