<?php

use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \app\models\ProfileForm */

$this->title = Yii::t('app', 'API');

$timestamp = Yii::$app->formatter->asTimestamp('now');
$sha1 = sha1($timestamp . Yii::$app->user->identity->api_key);
$baseUrl = Url::base(true);

BootstrapPluginAsset::register($this);
?>
<div class="form-group">
    <h1><?= Yii::t('app', 'How to use API?') ?></h1>
</div>

<div class="form-group">
    <?php if (empty(Yii::$app->user->identity->api_key)): ?>
        <p>
            <?= Yii::t('app', 'You currently don\'t have API access.') ?>
            <a href="<?= Url::to(['profile/grant']) ?>" data-method="post" class="btn btn-sm btn-primary">
                <i class="glyphicon glyphicon-flash"></i>
                <?= Yii::t('app', 'Grant yourself API access') ?>
            </a>
        </p>
    <?php else: ?>
        <p class="pull-right">
            <a href="<?= Url::to(['profile/change']) ?>" data-method="post" data-confirm="<?= Yii::t('app', 'Are you sure you want to change API key?') ?>" class="btn btn-sm btn-warning">
                <i class="glyphicon glyphicon-flash"></i>
                <?= Yii::t('app', 'Change API key') ?>
            </a>
            <a href="<?= Url::to(['profile/revoke']) ?>" data-method="post" data-confirm="<?= Yii::t('app', 'Are you sure you want to revoke API access?') ?>" class="btn btn-sm btn-danger">
                <i class="glyphicon glyphicon-off"></i>
                <?= Yii::t('app', 'Revoke API access') ?>
            </a>
        </p>
        <p><?= Yii::t('app', 'Your API identifier is {id} and your access key is {key}.', [
                'id' => Html::tag('kbd', Yii::$app->user->id),
                'key' => Html::tag('kbd', Yii::$app->user->identity->api_key),
            ]) ?></p>
    <?php endif; ?>
</div>

<div class="form-group">
    <h3><?= Yii::t('app', 'Authentication') ?></h3>
    <p><?= Yii::t('app', 'Every request made to API must be authenticated with Bearer token sent in Authorization header.') ?></p>
    <p><?= Yii::t('app', 'Bearer token must be made like following:') ?></p>
    <p><code><?= Yii::t('app', 'API identifier') ?>:<?= Yii::t('app', 'UNIX timestamp') ?>:<?= Yii::t('app', 'checksum') ?></code></p>
    <p><?= Yii::t('app', 'where:') ?></p>
    <ul>
        <li>
            <?= Yii::t('app', '{ID} is integer provided at this page and not changing. Your API identifier is {your ID}.', [
                'ID' => Html::tag('code', Yii::t('app', 'API identifier')),
                'your ID' => Html::tag('kbd', Yii::$app->user->id),
            ]) ?>
        </li>
        <li>
            <?= Yii::t('app', '{timestamp} is integer with number of seconds since the beginning of Unix Epoch on January 1st, 1970 at UTC. You must provide current timestamp like for example now - {now}. API will reject all requests older or younger by 1 minute from current time.', [
                'timestamp' => Html::tag('code', Yii::t('app', 'UNIX timestamp')),
                'now' => Html::tag('kbd', $timestamp) . ' (' . Yii::$app->formatter->asDatetime('now') . ')',
            ]) ?>
        </li>
        <li>
            <?= Yii::t('app', '{checksum} is SHA1 hash that takes as input UNIX timestamp described above concatenated with your API access key ({key}).', [
                'checksum' => Html::tag('code', Yii::t('app', 'checksum')),
                'key' => empty(Yii::$app->user->identity->api_key)
                    ? Html::a(
                            Html::tag('i', '', ['class' => 'glyphicon glyphicon-flash']) . ' ' . Yii::t('app', 'Grant yourself API access'),
                            ['profile/grant'],
                            ['class' => 'btn btn-xs btn-primary', 'data-method' => 'post']
                    )
                    : Html::tag('kbd', Yii::$app->user->identity->api_key),
            ]) ?> <?= !empty(Yii::$app->user->identity->api_key) ? Yii::t('app', 'In this example it is {sha1}', [
                    'sha1' => Html::tag('kbd', $sha1),
            ]) : '' ?>
        </li>
    </ul>
    <p><?= Yii::t('app', 'Authenticating header in our example would look like that:') ?></p>
    <p><kbd>Authorization Bearer <?= Yii::$app->user->id ?>:<?= $timestamp ?>:<?= $sha1 ?></kbd></p>
</div>

<hr>

<div class="form-group">
    <h3><?= $baseUrl ?>/api/sessions</h3>
</div>

<div class="panel-group" id="api-sessions" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-sessions-view-header">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#api-sessions" href="#api-sessions-view" aria-expanded="false" aria-controls="api-sessions-view">
                    <?= Yii::t('app', 'View Session') ?>
                </a>
            </h4>
        </div>
        <div id="api-sessions-view" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-sessions-view-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Work session of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> GET, HEAD</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    GET <?= $baseUrl ?>/api/sessions/1
                </div>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>{
    "id": 1,
    "userId": 1,
    "clockIn": 1545481724,
    "clockOut": null,
    "createdAt": 1545481724,
    "updatedAt": 1545481724
}</pre>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-sessions-index-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-sessions" href="#api-sessions-index" aria-expanded="false" aria-controls="api-sessions-index">
                    <?= Yii::t('app', 'Sessions Index') ?>
                </a>
            </h4>
        </div>
        <div id="api-sessions-index" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-sessions-index-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Work sessions index.') ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> GET, HEAD</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    GET <?= $baseUrl ?>/api/sessions
                </div>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>[
    {
        "id": 1,
        "userId": 1,
        "clockIn": 1545481724,
        "clockOut": null,
        "createdAt": 1545481724,
        "updatedAt": 1545481724
    }
]</pre>
                <p><?= Yii::t('app', 'Follow response headers for pagination:') ?></p>
                <ul>
                    <li><code>X-Pagination-Total-Count</code> <?= Yii::t('app', 'The total number of resources') ?></li>
                    <li><code>X-Pagination-Page-Count</code> <?= Yii::t('app', 'The number of pages') ?></li>
                    <li><code>X-Pagination-Current-Page</code> <?= Yii::t('app', 'The current page (1-based)') ?></li>
                    <li><code>X-Pagination-Per-Page</code> <?= Yii::t('app', 'The number of resources in each page') ?></li>
                    <li><code>Link</code> <?= Yii::t('app', 'A set of navigational links allowing client to traverse the resources page by page') ?></li>
                </ul>
                <p>
                    <?= Yii::t('app', 'To sort results send {sort} parameter with attribute name (or many attributes separated with comma). By default attributes are sorted in ascending order - to sort in descending order put minus before the attribute\'s name. Available attributes are: {attributes}.', [
                        'sort' => Html::tag('code', 'sort'),
                        'attributes' => '<code>' . implode('</code>, <code>', ['id', 'clockIn', 'clockOut', 'createdAt', 'updatedAt']) . '</code>',
                    ]) ?>
                </p>
                <p>
                    <?= Yii::t('app', 'To filter results send {filter} parameter with filter conditions as part of query or as JSON string. For example to get all results with ID greater than 5 and lesser or equal to 15 send:', [
                        'filter' => Html::tag('code', 'filter'),
                    ]) ?>
                </p>
                <div class="well well-sm">
                    GET <?= $baseUrl ?>/api/sessions?filter[id][gt]=5&filter[id][lte]=15
                </div>
                <p><?= Yii::t('app', 'Or in JSON:') ?></p>
                <pre>{
    'filter': {
        'id': {
            'gt': 5,
            'lte': 15
        }
    }
}</pre>
                <p><?= Yii::t('app', 'Filter available operators are {operators}.', [
                        'operators' => '<code>' . implode('</code>, <code>', ['and', 'or', 'not', 'lt', 'gt', 'lte', 'gte', 'eq', 'neq', 'in', 'nin', 'like']) . '</code>',
                    ]) ?></p>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-sessions-create-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-sessions" href="#api-sessions-create" aria-expanded="false" aria-controls="api-sessions-create">
                    <?= Yii::t('app', 'Create Session') ?>
                </a>
            </h4>
        </div>
        <div id="api-sessions-create" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-sessions-create-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Create work session.') ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> POST</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <ul>
                    <li>
                        <p><?= Yii::t('app', 'Start session at current time') ?></p>
                        <div class="well well-sm">
                            POST <?= $baseUrl ?>/api/sessions
                        </div>
                    </li>
                    <li>
                        <p><?= Yii::t('app', 'Start session at given time') ?></p>
                        <div class="well well-sm">
                            POST <?= $baseUrl ?>/api/sessions
                        </div>
                        <table class="table table-condensed">
                            <tr>
                                <th><?= Yii::t('app', 'Data') ?></th>
                                <th><?= Yii::t('app', 'Value') ?></th>
                            </tr>
                            <tr>
                                <td><code>clockIn</code></td>
                                <td><span class="label label-info">int</span> 1545485655</td>
                            </tr>
                        </table>
                    </li>
                    <li>
                        <p><?= Yii::t('app', 'Add ended session at given time') ?></p>
                        <div class="well well-sm">
                            POST <?= $baseUrl ?>/api/sessions
                        </div>
                        <table class="table table-condensed">
                            <tr>
                                <th><?= Yii::t('app', 'Data') ?></th>
                                <th><?= Yii::t('app', 'Value') ?></th>
                            </tr>
                            <tr>
                                <td><code>clockIn</code></td>
                                <td><span class="label label-info">int</span> 1545485655</td>
                            </tr>
                            <tr>
                                <td><code>clockOut</code></td>
                                <td><span class="label label-info">int</span> 1545739655</td>
                            </tr>
                        </table>
                    </li>
                </ul>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 201</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>{
    "id": 2,
    "userId": 1,
    "clockIn": 1545485055,
    "clockOut": null,
    "createdAt": 1545485055,
    "updatedAt": 1545485055
}</pre>
                <span class="label label-warning pull-right"><?= Yii::t('app', 'Status:') ?> 422</span>
                <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
                <pre>[
    {
        "field": "clockOut",
        "message": "Clock Out must be greater than \"Clock In\"."
    }
]</pre>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-sessions-update-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-sessions" href="#api-sessions-update" aria-expanded="false" aria-controls="api-sessions-update">
                    <?= Yii::t('app', 'Update Session') ?>
                </a>
            </h4>
        </div>
        <div id="api-sessions-update" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-sessions-update-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Update work session of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> PUT,PATCH</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    PUT <?= $baseUrl ?>/api/sessions/1
                </div>
                <table class="table table-condensed">
                    <tr>
                        <th><?= Yii::t('app', 'Data') ?></th>
                        <th><?= Yii::t('app', 'Value') ?></th>
                    </tr>
                    <tr>
                        <td><code>clockOut</code></td>
                        <td><span class="label label-info">int</span> 1545739655</td>
                    </tr>
                </table>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>{
    "id": 1,
    "userId": 1,
    "clockIn": 1545485055,
    "clockOut": 1545739655,
    "createdAt": 1545485055,
    "updatedAt": 1545739655
}</pre>
                <span class="label label-warning pull-right"><?= Yii::t('app', 'Status:') ?> 422</span>
                <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
                <pre>[
    {
        "field": "clockOut",
        "message": "Clock Out must be greater than \"Clock In\"."
    }
]</pre>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-sessions-delete-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-sessions" href="#api-sessions-delete" aria-expanded="false" aria-controls="api-sessions-delete">
                    <?= Yii::t('app', 'Delete Session') ?>
                </a>
            </h4>
        </div>
        <div id="api-sessions-delete" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-sessions-delete-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Delete work session of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> DELETE</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    DELETE <?= $baseUrl ?>/api/sessions/1
                </div>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 204</span>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="form-group">
    <h3><?= $baseUrl ?>/api/off-times</h3>
</div>

<div class="panel-group" id="api-off-times" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-off-times-view-header">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#api-off-times" href="#api-off-times-view" aria-expanded="false" aria-controls="api-off-times-view">
                    <?= Yii::t('app', 'View Off-Time') ?>
                </a>
            </h4>
        </div>
        <div id="api-off-times-view" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-off-times-view-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Off-time of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> GET, HEAD</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    GET <?= $baseUrl ?>/api/off-times/1
                </div>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>{
    "id": 1,
    "userId": 1,
    "startAt": 1545174000,
    "endAt": 1546124340,
    "note": "Off-time note",
    "createdAt": 1545245502,
    "updatedAt": 1545247839
}</pre>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-off-times-index-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-off-times" href="#api-off-times-index" aria-expanded="false" aria-controls="api-off-times-index">
                    <?= Yii::t('app', 'Off-Times Index') ?>
                </a>
            </h4>
        </div>
        <div id="api-off-times-index" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-off-times-index-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Off-times index.') ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> GET, HEAD</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    GET <?= $baseUrl ?>/api/off-times
                </div>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>[
    {
        "id": 1,
        "userId": 1,
        "startAt": 1545174000,
        "endAt": 1546124340,
        "note": "Off-time note",
        "createdAt": 1545245502,
        "updatedAt": 1545247839
    }
]</pre>
                <p><?= Yii::t('app', 'Follow response headers for pagination:') ?></p>
                <ul>
                    <li><code>X-Pagination-Total-Count</code> <?= Yii::t('app', 'The total number of resources') ?></li>
                    <li><code>X-Pagination-Page-Count</code> <?= Yii::t('app', 'The number of pages') ?></li>
                    <li><code>X-Pagination-Current-Page</code> <?= Yii::t('app', 'The current page (1-based)') ?></li>
                    <li><code>X-Pagination-Per-Page</code> <?= Yii::t('app', 'The number of resources in each page') ?></li>
                    <li><code>Link</code> <?= Yii::t('app', 'A set of navigational links allowing client to traverse the resources page by page') ?></li>
                </ul>
                <p>
                    <?= Yii::t('app', 'To sort results send {sort} parameter with attribute name (or many attributes separated with comma). By default attributes are sorted in ascending order - to sort in descending order put minus before the attribute\'s name. Available attributes are: {attributes}.', [
                        'sort' => Html::tag('code', 'sort'),
                        'attributes' => '<code>' . implode('</code>, <code>', ['id', 'startAt', 'endAt', 'note', 'createdAt', 'updatedAt']) . '</code>',
                    ]) ?>
                </p>
                <p>
                    <?= Yii::t('app', 'To filter results send {filter} parameter with filter conditions as part of query or as JSON string. For example to get all results with ID greater than 5 and lesser or equal to 15 send:', [
                        'filter' => Html::tag('code', 'filter'),
                    ]) ?>
                </p>
                <div class="well well-sm">
                    GET <?= $baseUrl ?>/api/off-times?filter[id][gt]=5&filter[id][lte]=15
                </div>
                <p><?= Yii::t('app', 'Or in JSON:') ?></p>
                <pre>{
    'filter': {
        'id': {
            'gt': 5,
            'lte': 15
        }
    }
}</pre>
                <p><?= Yii::t('app', 'Filter available operators are {operators}.', [
                        'operators' => '<code>' . implode('</code>, <code>', ['and', 'or', 'not', 'lt', 'gt', 'lte', 'gte', 'eq', 'neq', 'in', 'nin', 'like']) . '</code>',
                    ]) ?></p>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-off-times-create-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-off-times" href="#api-off-times-create" aria-expanded="false" aria-controls="api-off-times-create">
                    <?= Yii::t('app', 'Create Off-Time') ?>
                </a>
            </h4>
        </div>
        <div id="api-off-times-create" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-off-times-create-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Create off-time.') ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> POST</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    POST <?= $baseUrl ?>/api/off-times
                </div>
                <table class="table table-condensed">
                    <tr>
                        <th><?= Yii::t('app', 'Data') ?></th>
                        <th><?= Yii::t('app', 'Value') ?></th>
                    </tr>
                    <tr>
                        <td><code>startAt</code></td>
                        <td><span class="label label-info">int</span> 1545485655</td>
                    </tr>
                    <tr>
                        <td><code>endAt</code></td>
                        <td><span class="label label-info">int</span> 1545739655</td>
                    </tr>
                    <tr>
                        <td><code>note</code></td>
                        <td><span class="label label-warning">string</span> optional note</td>
                    </tr>
                </table>
                <p><?= Yii::t('app', 'Start time is always normalized to 00:00:00 and end time is always normalized to 23:59:59 to cover whole day.') ?></p>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 201</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>{
    "id": 2,
    "userId": 1,
    "startAt": 1545436800,
    "endAt": 1545868799,
    "note": null,
    "createdAt": 1545501234,
    "updatedAt": 1545501234
}</pre>
                <span class="label label-warning pull-right"><?= Yii::t('app', 'Status:') ?> 422</span>
                <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
                <pre>[
    {
        "field": "endAt",
        "message": "End At must be greater than \"Start At\"."
    }
]</pre>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-off-times-update-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-off-times" href="#api-off-times-update" aria-expanded="false" aria-controls="api-off-times-update">
                    <?= Yii::t('app', 'Update Off-Time') ?>
                </a>
            </h4>
        </div>
        <div id="api-off-times-update" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-off-times-update-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Update off-time of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> PUT,PATCH</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    PUT <?= $baseUrl ?>/api/off-times/1
                </div>
                <table class="table table-condensed">
                    <tr>
                        <th><?= Yii::t('app', 'Data') ?></th>
                        <th><?= Yii::t('app', 'Value') ?></th>
                    </tr>
                    <tr>
                        <td><code>endAt</code></td>
                        <td><span class="label label-info">int</span> 1545869799</td>
                    </tr>
                </table>
                <p><?= Yii::t('app', 'Start time is always normalized to 00:00:00 and end time is always normalized to 23:59:59 to cover whole day.') ?></p>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>{
    "id": 3,
    "userId": 1,
    "startAt": 1545436800,
    "endAt": 1545868799,
    "note": null,
    "createdAt": 1545501625,
    "updatedAt": 1545501625
}</pre>
                <span class="label label-warning pull-right"><?= Yii::t('app', 'Status:') ?> 422</span>
                <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
                <pre>[
    {
        "field": "endAt",
        "message": "End At must be greater than \"Start At\"."
    }
]</pre>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-off-times-delete-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-off-times" href="#api-off-times-delete" aria-expanded="false" aria-controls="api-off-times-delete">
                    <?= Yii::t('app', 'Delete Off-Time') ?>
                </a>
            </h4>
        </div>
        <div id="api-off-times-delete" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-off-times-delete-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Delete off-time of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> DELETE</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    DELETE <?= $baseUrl ?>/api/off-times/1
                </div>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 204</span>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="form-group">
    <h3><?= $baseUrl ?>/api/holidays</h3>
</div>

<div class="panel-group" id="api-holidays" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-holidays-index-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-holidays" href="#api-holidays-index" aria-expanded="false" aria-controls="api-holidays-index">
                    <?= Yii::t('app', 'Holidays Index') ?>
                </a>
            </h4>
        </div>
        <div id="api-holidays-index" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-holidays-index-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Fetched holidays index.') ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> GET, HEAD</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    GET <?= $baseUrl ?>/api/holidays
                </div>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>[
    {
        "year": 2018,
        "month": 1,
        "day": 1
    },
    {
        "year": 2018,
        "month": 1,
        "day": 6
    },
]</pre>
                <p><?= Yii::t('app', 'Follow response headers for pagination:') ?></p>
                <ul>
                    <li><code>X-Pagination-Total-Count</code> <?= Yii::t('app', 'The total number of resources') ?></li>
                    <li><code>X-Pagination-Page-Count</code> <?= Yii::t('app', 'The number of pages') ?></li>
                    <li><code>X-Pagination-Current-Page</code> <?= Yii::t('app', 'The current page (1-based)') ?></li>
                    <li><code>X-Pagination-Per-Page</code> <?= Yii::t('app', 'The number of resources in each page') ?></li>
                    <li><code>Link</code> <?= Yii::t('app', 'A set of navigational links allowing client to traverse the resources page by page') ?></li>
                </ul>
                <p>
                    <?= Yii::t('app', 'To sort results send {sort} parameter with attribute name (or many attributes separated with comma). By default attributes are sorted in ascending order - to sort in descending order put minus before the attribute\'s name. Available attributes are: {attributes}.', [
                        'sort' => Html::tag('code', 'sort'),
                        'attributes' => '<code>' . implode('</code>, <code>', ['id', 'startAt', 'endAt', 'note', 'createdAt', 'updatedAt']) . '</code>',
                    ]) ?>
                </p>
                <p>
                    <?= Yii::t('app', 'To filter results send {filter} parameter with filter conditions as part of query or as JSON string. For example to get all results with ID greater than 5 and lesser or equal to 15 send:', [
                        'filter' => Html::tag('code', 'filter'),
                    ]) ?>
                </p>
                <div class="well well-sm">
                    GET <?= $baseUrl ?>/api/holidays?filter[id][gt]=5&filter[id][lte]=15
                </div>
                <p><?= Yii::t('app', 'Or in JSON:') ?></p>
                <pre>{
    'filter': {
        'id': {
            'gt': 5,
            'lte': 15
        }
    }
}</pre>
                <p><?= Yii::t('app', 'Filter available operators are {operators}.', [
                        'operators' => '<code>' . implode('</code>, <code>', ['and', 'or', 'not', 'lt', 'gt', 'lte', 'gte', 'eq', 'neq', 'in', 'nin', 'like']) . '</code>',
                    ]) ?></p>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-holidays-fetch-header">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#api-holidays" href="#api-holidays-fetch" aria-expanded="false" aria-controls="api-holidays-fetch">
                    <?= Yii::t('app', 'Fetch Holidays') ?>
                </a>
            </h4>
        </div>
        <div id="api-holidays-fetch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-holidays-fetch-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Fetch holidays of given year.') ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> POST</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    POST <?= $baseUrl ?>/api/holidays/fetch
                </div>
                <table class="table table-condensed">
                    <tr>
                        <th><?= Yii::t('app', 'Data') ?></th>
                        <th><?= Yii::t('app', 'Value') ?></th>
                    </tr>
                    <tr>
                        <td><code>year</code></td>
                        <td><span class="label label-info">int</span> 2019</td>
                    </tr>
                </table>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 204</span>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="form-group">
    <h3><?= $baseUrl ?>/api/profile</h3>
</div>

<div class="panel-group" id="api-profile" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-profile-view-header">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#api-profile" href="#api-profile-view" aria-expanded="false" aria-controls="api-profile-view">
                    <?= Yii::t('app', 'View Profile') ?>
                </a>
            </h4>
        </div>
        <div id="api-profile-view" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-profile-view-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Profile details.') ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> GET, HEAD</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    GET <?= $baseUrl ?>/api/profile
                </div>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>{
    "id": 1,
    "name": "John",
    "email": "john@company.com",
    "createdAt": 1545245502,
    "updatedAt": 1545247839
}</pre>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-profile-update-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-profile" href="#api-profile-update" aria-expanded="false" aria-controls="api-profile-update">
                    <?= Yii::t('app', 'Update Profile') ?>
                </a>
            </h4>
        </div>
        <div id="api-profile-update" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-profile-update-header">
            <div class="panel-body">
                <p><?= Yii::t('app', 'Update profil with new name.') ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> PUT,PATCH</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    PUT <?= $baseUrl ?>/api/profile
                </div>
                <table class="table table-condensed">
                    <tr>
                        <th><?= Yii::t('app', 'Data') ?></th>
                        <th><?= Yii::t('app', 'Value') ?></th>
                    </tr>
                    <tr>
                        <td><code>name</code></td>
                        <td><span class="label label-warning">string</span> Bruce Wayne</td>
                    </tr>
                </table>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>{
    "id": 1,
    "name": "Bruce Wayne",
    "email": "john@company.com",
    "createdAt": 1545245502,
    "updatedAt": 1545255438
}</pre>
                <span class="label label-warning pull-right"><?= Yii::t('app', 'Status:') ?> 422</span>
                <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
                <pre>[
    {
        "field": "name",
        "message": "First And Last Name cannot be blank."
    }
]</pre>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="form-group">
    <h3><?= $baseUrl ?>/api/key</h3>
</div>

<div class="panel-group" id="api-key" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="api-key-index-header">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#api-key" href="#api-key-index" aria-expanded="false" aria-controls="api-key-index">
                    <?= Yii::t('app', 'API Key') ?>
                </a>
            </h4>
        </div>
        <div id="api-key-index" class="panel-collapse collapse" role="tabpanel" aria-labelledby="api-key-index-header">
            <div class="panel-body">
                <p><strong><?= Yii::t('app', 'This request does not require authentication token.') ?></strong></p>
                <p><?= Yii::t('app', 'You can get API access key only when PIN has been already generated.') ?></p>
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Methods:') ?> POST</span>
                <p><?= Yii::t('app', 'Request example:') ?></p>
                <div class="well well-sm">
                    POST <?= $baseUrl ?>/api/key
                </div>
                <table class="table table-condensed">
                    <tr>
                        <th><?= Yii::t('app', 'Data') ?></th>
                        <th><?= Yii::t('app', 'Value') ?></th>
                    </tr>
                    <tr>
                        <td><code>pin</code></td>
                        <td><span class="label label-warning">string</span> 1054</td>
                    </tr>
                </table>
                <span class="label label-success pull-right"><?= Yii::t('app', 'Status:') ?> 200</span>
                <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
                <pre>{
    "apiKey": "goBnoSjSSToUTXv744iV"
}</pre>
            </div>
        </div>
    </div>
</div>
