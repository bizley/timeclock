<?php

use app\base\Alert;
use app\models\User;
use yii\caching\FileCache;
use yii\debug\Module;
use yii\log\FileTarget;
use yii\swiftmailer\Mailer;

$config = [
    'id' => 'timeclock',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(__DIR__) . '/../vendor',
    'runtimePath' => dirname(__DIR__) . '/../runtime',
    'bootstrap' => ['log'],
    'timeZone' => 'Europe/Warsaw',
    'language' => 'pl',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'JIaOBg9h_jQT42KwuxzA3M4TxerBjzfx',
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'alert' => [
            'class' => Alert::class,
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => [

        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require 'db.php',
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'site/new-password/<token:\w+>' => 'site/new-password',
                'clock/<action:[\w\-]+>/<month:\d+>/<year:\d+>' => 'clock/<action>',
                'admin/<action:\w+>/<month:\d+>/<year:\d+>/<id:\d+>' => 'admin/<action>',
                'admin/<action:\w+>/<month:\d+>/<year:\d+>' => 'admin/<action>',
                '<controller:\w+>/<action:[\w\-]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
    'params' => [
        'company' => 'Company Name',
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => Module::class,
    ];
}

return $config;
