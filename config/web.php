<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'container' => [
        'singletons' => [
            'ContactRepository' => ['class' => 'app\repositories\ContactRepository'],
            'ProjectRepository' => ['class' => 'app\repositories\ProjectRepository'],
            'ProjectService' => ['class' => 'app\services\ProjectService'],
            'ContactService' => ['class' => 'app\services\ContactService'],
            'ProjectValidation' => ['class' => 'app\validations\ProjectValidation'],
            'ContactValidation' => ['class' => 'app\validations\ContactValidation'],
            'app\repositories\ProjectRepositoryInterface' => ['class' => 'app\repositories\ProjectRepository'],
            'app\repositories\ContactRepositoryInterface' => ['class' => 'app\repositories\ContactRepository'],
        ],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'qv8U4cZTVenHWQsZvuxV1pefEowxcIAo',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'DELETE projects/<id:\d+>' => 'project/delete',
                'GET projects/<id:\d+>' => 'project/view',
                'PUT,PATCH projects/<id:\d+>' => 'project/update',
                'POST projects' => 'project/create',
                'GET projects' => 'project/index',
                'GET /' => 'project/index',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
