<?php

use yii\rest\UrlRule;

return [
    [
        'class' => UrlRule::class,
        'controller' => 'api/session',
        'extraPatterns' => ['GET summary' => 'summary'],
    ],
    [
        'class' => UrlRule::class,
        'controller' => 'api/off-time',
    ],
    [
        'class' => UrlRule::class,
        'controller' => 'api/holiday',
        'only' => ['index', 'fetch', 'options'],
        'extraPatterns' => ['POST fetch' => 'fetch'],
    ],
    [
        'class' => UrlRule::class,
        'controller' => 'api/key',
        'only' => ['index', 'options'],
        'pluralize' => false,
        'patterns' => [
            'POST' => 'index',
            '' => 'options',
        ],
    ],
    [
        'class' => UrlRule::class,
        'controller' => 'api/profile',
        'only' => ['view', 'update', 'options'],
        'pluralize' => false,
        'patterns' => [
            'GET,HEAD' => 'view',
            'PUT,PATCH' => 'update',
            '' => 'options',
        ],
    ],
    'site/new-password/<token:\w+>' => 'site/new-password',
    'clock/<action:[\w\-]+>/<day:\d+>/<month:\d+>/<year:\d+>' => 'clock/<action>',
    'clock/<action:[\w\-]+>/<month:\d+>/<year:\d+>' => 'clock/<action>',
    'admin/day/<day:\d+>/<month:\d+>/<year:\d+>/<employee:\d+>' => 'admin/day',
    'admin/<action:\w+>/<month:\d+>/<year:\d+>/<id:\d+>' => 'admin/<action>',
    'admin/<action:\w+>/<month:\d+>/<year:\d+>' => 'admin/<action>',
    'profile/theme/<theme:\w+>' => 'profile/theme',
    '<controller:\w+>/<action:[\w\-]+>/<id:\d+>' => '<controller>/<action>',
    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
];
