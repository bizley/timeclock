<?php

use yii\web\JsonParser;
use app\api\Api;
use app\base\Alert;
use app\models\User;
use yii\caching\FileCache;
use yii\debug\Module;
use yii\i18n\PhpMessageSource;
use yii\log\FileTarget;
use yii\rest\UrlRule;
use yii\swiftmailer\Mailer;

$config = [
    'id' => 'timeclock',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(__DIR__) . '/../vendor',
    'runtimePath' => dirname(__DIR__) . '/../runtime',
    'bootstrap' => ['log'],
    'timeZone' => 'UTC',
    'language' => 'en-US',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'api' => Api::class,
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'JIaOBg9h_jQT42KwuxzA3M4TxerBjzfx',
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
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
            'translations' => [
                'app*' => [
                    'class' => PhpMessageSource::class,
                    'basePath' => '@app/messages'
                ],
            ],
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
                [
                    'class' => UrlRule::class,
                    'controller' => ['api/session', 'api/off-time'],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => 'api/holiday',
                    'only' => ['index', 'fetch', 'options'],
                    'extraPatterns' => ['POST fetch' => 'fetch']
                ],
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
        'email' => 'email@company.com',
        'allowedDomains' => ['@company.com'],
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
