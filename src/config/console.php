<?php

use yii\caching\FileCache;
use yii\log\FileTarget;

return [
    'id' => 'timeclock-console',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(__DIR__) . '/../vendor',
    'runtimePath' => dirname(__DIR__) . '/../runtime',
    'bootstrap' => ['log'],
    'timeZone' => 'Europe/Warsaw',
    'language' => 'pl',
    'components' => [
        'cache' => [
            'class' => FileCache::class,
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
    ],
    'params' => [
        'company' => 'Company Name',
    ],
];
