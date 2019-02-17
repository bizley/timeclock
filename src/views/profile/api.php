<?php

use app\widgets\accordion\Accordion;
use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \app\models\ProfileForm */

$this->title = Yii::t('app', 'API');

$timestamp = Yii::$app->formatter->asTimestamp('now');
$sha1 = sha1($timestamp . Yii::$app->user->identity->api_key);
$baseUrl = Url::base(true);

?>
<div class="form-group mt-5">
    <h1><?= FA::icon('cloud') ?> <?= Yii::t('app', 'How to use API?') ?></h1>
</div>

<div class="form-group">
    <?php if (empty(Yii::$app->user->identity->api_key)): ?>
        <p>
            <?= Yii::t('app', 'You currently don\'t have API access.') ?>
            <a href="<?= Url::to(['profile/grant']) ?>" data-method="post" class="btn btn-sm btn-primary">
                <?= FA::icon('cloud') ?>
                <?= Yii::t('app', 'Grant yourself API access') ?>
            </a>
        </p>
    <?php else: ?>
        <p class="float-sm-right ml-1 mb-3">
            <a href="<?= Url::to(['profile/change']) ?>" <?= Confirm::ask(Yii::t('app', 'Are you sure you want to change API key?')) ?> class="btn btn-sm btn-warning">
                <?= FA::icon('redo-alt') ?>
                <?= Yii::t('app', 'Change API key') ?>
            </a>
            <a href="<?= Url::to(['profile/revoke']) ?>" <?= Confirm::ask(Yii::t('app', 'Are you sure you want to revoke API access?')) ?> class="btn btn-sm btn-danger">
                <?= FA::icon('power-off') ?>
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
    <h3><?= FA::icon('user-lock') ?> <?= Yii::t('app', 'Authentication') ?></h3>
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
    <h3><?= FA::icon('chevron-circle-right') ?> <?= $baseUrl ?>/api/sessions</h3>
</div>

<div class="accordion" id="api-sessions">
    <?php Accordion::begin([
        'parentId' => 'api-sessions',
        'header' => Yii::t('app', 'View Session') . ' <span class="badge badge-success">GET, HEAD</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Work session of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/sessions/1
        </kbd></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
        <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
        <pre>{
    "id": 1,
    "userId": 1,
    "clockIn": 1545481724,
    "clockOut": null,
    "note": "Home office",
    "createdAt": 1545481724,
    "updatedAt": 1545481724
}</pre>
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-sessions',
        'header' => Yii::t('app', 'Sessions Index') . ' <span class="badge badge-success">GET, HEAD</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Work sessions index.') ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/sessions
        </kbd></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
        <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
        <pre>[
    {
        "id": 1,
        "userId": 1,
        "clockIn": 1545481724,
        "clockOut": null,
        "note": "Home office",
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
                'attributes' => '<code>' . implode('</code>, <code>', ['id', 'clockIn', 'clockOut', 'note', 'createdAt', 'updatedAt']) . '</code>',
            ]) ?>
        </p>
        <p>
            <?= Yii::t('app', 'To filter results send {filter} parameter with filter conditions as part of query or as JSON string. For example to get all results with ID greater than 5 and lesser or equal to 15 send:', [
                'filter' => Html::tag('code', 'filter'),
            ]) ?>
        </p>
        <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/sessions?filter[id][gt]=5&filter[id][lte]=15
        </kbd></p>
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
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-sessions',
        'header' => Yii::t('app', 'Create Session') . ' <span class="badge badge-primary">POST</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Create work session.') ?></p>
        <span class="badge badge-primary float-right"><?= Yii::t('app', 'Methods:') ?> POST</span>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <ul>
            <li>
                <p><?= Yii::t('app', 'Start session at current time') ?></p>
                <p><kbd class="p-2">
                    POST <?= $baseUrl ?>/api/sessions
                </kbd></p>
            </li>
            <li>
                <p><?= Yii::t('app', 'Start session at given time') ?></p>
                <p><kbd class="p-2">
                    POST <?= $baseUrl ?>/api/sessions
                </kbd></p>
                <table class="table table-sm">
                    <tr>
                        <th><?= Yii::t('app', 'Data') ?></th>
                        <th><?= Yii::t('app', 'Value') ?></th>
                    </tr>
                    <tr>
                        <td><code>clockIn</code></td>
                        <td><span class="badge badge-info">int</span> 1545485655</td>
                    </tr>
                    <tr>
                        <td><code>note</code></td>
                        <td><span class="badge badge-warning">string</span> Home office</td>
                    </tr>
                </table>
            </li>
            <li>
                <p><?= Yii::t('app', 'Add ended session at given time') ?></p>
                <p><kbd class="p-2">
                    POST <?= $baseUrl ?>/api/sessions
                </kbd></p>
                <table class="table table-sm">
                    <tr>
                        <th><?= Yii::t('app', 'Data') ?></th>
                        <th><?= Yii::t('app', 'Value') ?></th>
                    </tr>
                    <tr>
                        <td><code>clockIn</code></td>
                        <td><span class="badge badge-info">int</span> 1545485655</td>
                    </tr>
                    <tr>
                        <td><code>clockOut</code></td>
                        <td><span class="badge badge-info">int</span> 1545739655</td>
                    </tr>
                </table>
            </li>
        </ul>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 201</span>
        <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
        <pre>{
    "id": 2,
    "userId": 1,
    "clockIn": 1545485055,
    "clockOut": null,
    "note": "Home office",
    "createdAt": 1545485055,
    "updatedAt": 1545485055
}</pre>
        <span class="badge badge-warning float-right"><?= Yii::t('app', 'Status:') ?> 422</span>
        <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
        <pre>[
    {
        "field": "clockOut",
        "message": "Clock Out must be greater than \"Clock In\"."
    }
]</pre>
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-sessions',
        'header' => Yii::t('app', 'Update Session') . ' <span class="badge badge-info">PUT, PATCH</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Update work session of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            PUT <?= $baseUrl ?>/api/sessions/1
        </kbd></p>
        <table class="table table-sm">
            <tr>
                <th><?= Yii::t('app', 'Data') ?></th>
                <th><?= Yii::t('app', 'Value') ?></th>
            </tr>
            <tr>
                <td><code>clockOut</code></td>
                <td><span class="badge badge-info">int</span> 1545739655</td>
            </tr>
            <tr>
                <td><code>note</code></td>
                <td><span class="badge badge-warning">string</span> Birthday</td>
            </tr>
        </table>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
        <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
        <pre>{
    "id": 1,
    "userId": 1,
    "clockIn": 1545485055,
    "clockOut": 1545739655,
    "note": "Birthday",
    "createdAt": 1545485055,
    "updatedAt": 1545739655
}</pre>
        <span class="badge badge-warning float-right"><?= Yii::t('app', 'Status:') ?> 422</span>
        <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
        <pre>[
    {
        "field": "clockOut",
        "message": "Clock Out must be greater than \"Clock In\"."
    }
]</pre>
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-sessions',
        'header' => Yii::t('app', 'Delete Session') . ' <span class="badge badge-danger">DELETE</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Delete work session of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            DELETE <?= $baseUrl ?>/api/sessions/1
        </kbd></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 204</span>
        <p>&nbsp;</p>
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-sessions',
        'header' => Yii::t('app', 'Summarize Sessions') . ' <span class="badge badge-success">GET, HEAD</span>',
    ]); ?>
    <p><?= Yii::t('app', 'Summarize sessions time of all sessions that are closed and lasted between {FROM} and {TO} timestamps (default 0 and current timestamp, respectively).', [
            'FROM' => Html::tag('code', 'from'),
            'TO' => Html::tag('code', 'to')
        ]) ?></p>
    <p><?= Yii::t('app', 'Request example:') ?></p>
    <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/sessions/summary
        </kbd></p>
    <table class="table table-sm">
        <tr>
            <th><?= Yii::t('app', 'Data') ?></th>
            <th><?= Yii::t('app', 'Value') ?></th>
        </tr>
        <tr>
            <td><code>from</code></td>
            <td><span class="badge badge-info">int</span> 0</td>
        </tr>
        <tr>
            <td><code>to</code></td>
            <td><span class="badge badge-info">int</span> 1546124340</td>
        </tr>
    </table>
    <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
    <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
    <pre>{
    "userId": 1,
    "from": 0,
    "to": 1546124340,
    "summary": 1705
}</pre>
    <?php Accordion::end(); ?>
</div>

<hr>

<div class="form-group">
    <h3><?= FA::icon('chevron-circle-right') ?> <?= $baseUrl ?>/api/off-times</h3>
</div>

<div class="accordion" id="api-off-times">
    <?php Accordion::begin([
        'parentId' => 'api-off-times',
        'header' => Yii::t('app', 'View Off-Time') . ' <span class="badge badge-success">GET, HEAD</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Off-time of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/off-times/1
        </kbd></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
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
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-off-times',
        'header' => Yii::t('app', 'Off-Times Index') . ' <span class="badge badge-success">GET, HEAD</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Off-times index.') ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/off-times
        </kbd></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
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
        <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/off-times?filter[id][gt]=5&filter[id][lte]=15
        </kbd></p>
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
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-off-times',
        'header' => Yii::t('app', 'Create Off-Time') . ' <span class="badge badge-primary">POST</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Create off-time.') ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            POST <?= $baseUrl ?>/api/off-times
        </kbd></p>
        <table class="table table-sm">
            <tr>
                <th><?= Yii::t('app', 'Data') ?></th>
                <th><?= Yii::t('app', 'Value') ?></th>
            </tr>
            <tr>
                <td><code>startAt</code></td>
                <td><span class="badge badge-info">int</span> 1545485655</td>
            </tr>
            <tr>
                <td><code>endAt</code></td>
                <td><span class="badge badge-info">int</span> 1545739655</td>
            </tr>
            <tr>
                <td><code>note</code></td>
                <td><span class="badge badge-warning">string</span> optional note</td>
            </tr>
        </table>
        <p><?= Yii::t('app', 'Start time is always normalized to 00:00:00 and end time is always normalized to 23:59:59 to cover whole day.') ?></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 201</span>
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
        <span class="badge badge-warning float-right"><?= Yii::t('app', 'Status:') ?> 422</span>
        <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
        <pre>[
    {
        "field": "endAt",
        "message": "End At must be greater than \"Start At\"."
    }
]</pre>
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-off-times',
        'header' => Yii::t('app', 'Update Off-Time') . ' <span class="badge badge-info">PUT, PATCH</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Update off-time of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            PUT <?= $baseUrl ?>/api/off-times/1
        </kbd></p>
        <table class="table table-sm">
            <tr>
                <th><?= Yii::t('app', 'Data') ?></th>
                <th><?= Yii::t('app', 'Value') ?></th>
            </tr>
            <tr>
                <td><code>endAt</code></td>
                <td><span class="badge badge-info">int</span> 1545869799</td>
            </tr>
            <tr>
                <td><code>note</code></td>
                <td><span class="badge badge-warning">string</span> Doctor</td>
            </tr>
        </table>
        <p><?= Yii::t('app', 'Start time is always normalized to 00:00:00 and end time is always normalized to 23:59:59 to cover whole day.') ?></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
        <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
        <pre>{
    "id": 3,
    "userId": 1,
    "startAt": 1545436800,
    "endAt": 1545868799,
    "note": "Doctor",
    "createdAt": 1545501625,
    "updatedAt": 1545501625
}</pre>
        <span class="badge badge-warning float-right"><?= Yii::t('app', 'Status:') ?> 422</span>
        <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
        <pre>[
    {
        "field": "endAt",
        "message": "End At must be greater than \"Start At\"."
    }
]</pre>
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-off-times',
        'header' => Yii::t('app', 'Delete Off-Time') . ' <span class="badge badge-danger">DELETE</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Delete off-time of given {ID}.', ['ID' => Html::tag('code', Yii::t('app', 'ID'))]) ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            DELETE <?= $baseUrl ?>/api/off-times/1
        </kbd></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 204</span>
        <p>&nbsp;</p>
    <?php Accordion::end(); ?>
</div>

<hr>

<div class="form-group">
    <h3><?= FA::icon('chevron-circle-right') ?> <?= $baseUrl ?>/api/holidays</h3>
</div>

<div class="accordion" id="api-holidays">
    <?php Accordion::begin([
        'parentId' => 'api-holidays',
        'header' => Yii::t('app', 'Holidays Index') . ' <span class="badge badge-success">GET, HEAD</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Fetched holidays index.') ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/holidays
        </kbd></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
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
        <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/holidays?filter[id][gt]=5&filter[id][lte]=15
        </kbd></p>
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
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-holidays',
        'header' => Yii::t('app', 'Fetch Holidays') . ' <span class="badge badge-primary">POST</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Fetch holidays of given year.') ?></p>
        <span class="badge badge-primary float-right"><?= Yii::t('app', 'Methods:') ?> POST</span>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            POST <?= $baseUrl ?>/api/holidays/fetch
        </kbd></p>
        <table class="table table-sm">
            <tr>
                <th><?= Yii::t('app', 'Data') ?></th>
                <th><?= Yii::t('app', 'Value') ?></th>
            </tr>
            <tr>
                <td><code>year</code></td>
                <td><span class="badge badge-info">int</span> 2019</td>
            </tr>
        </table>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 204</span>
        <p>&nbsp;</p>
    <?php Accordion::end(); ?>
</div>

<hr>

<div class="form-group">
    <h3><?= FA::icon('chevron-circle-right') ?> <?= $baseUrl ?>/api/profile</h3>
</div>

<div class="accordion" id="api-profile">
    <?php Accordion::begin([
        'parentId' => 'api-profile',
        'header' => Yii::t('app', 'View Profile') . ' <span class="badge badge-success">GET, HEAD</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Profile details.') ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            GET <?= $baseUrl ?>/api/profile
        </kbd></p>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
        <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
        <pre>{
    "id": 1,
    "name": "John",
    "email": "john@company.com",
    "phone": "7575 333 888",
    "createdAt": 1545245502,
    "updatedAt": 1545247839
}</pre>
    <?php Accordion::end(); ?>

    <?php Accordion::begin([
        'parentId' => 'api-profile',
        'header' => Yii::t('app', 'Update Profile') . ' <span class="badge badge-info">PUT, PATCH</span>',
    ]); ?>
        <p><?= Yii::t('app', 'Update profil with new name.') ?></p>
        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
            PUT <?= $baseUrl ?>/api/profile
        </kbd></p>
        <table class="table table-sm">
            <tr>
                <th><?= Yii::t('app', 'Data') ?></th>
                <th><?= Yii::t('app', 'Value') ?></th>
            </tr>
            <tr>
                <td><code>name</code></td>
                <td><span class="badge badge-warning">string</span> Bruce Wayne</td>
            </tr>
            <tr>
                <td><code>phone</code></td>
                <td><span class="badge badge-warning">string</span> 7432 999 777</td>
            </tr>
        </table>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
        <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
        <pre>{
    "id": 1,
    "name": "Bruce Wayne",
    "email": "john@company.com",
    "phone": "7432 999 777",
    "createdAt": 1545245502,
    "updatedAt": 1545255438
}</pre>
        <span class="badge badge-warning float-right"><?= Yii::t('app', 'Status:') ?> 422</span>
        <p><?= Yii::t('app', 'Error response example (JSON):') ?></p>
        <pre>[
    {
        "field": "name",
        "message": "First And Last Name cannot be blank."
    }
]</pre>
    <?php Accordion::end(); ?>
</div>

<hr>

<div class="form-group">
    <h3><?= FA::icon('chevron-circle-right') ?> <?= $baseUrl ?>/api/key</h3>
</div>

<div class="accordion" id="api-key">
    <?php Accordion::begin([
        'parentId' => 'api-key',
        'header' => Yii::t('app', 'API Key') . ' <span class="badge badge-primary">POST</span>',
    ]); ?>
        <p><strong><?= Yii::t('app', 'This request does not require authentication token.') ?></strong></p>
        <p><?= Yii::t('app', 'You can get API access key only when PIN has been already generated.') ?></p>

        <p><?= Yii::t('app', 'Request example:') ?></p>
        <p><kbd class="p-2">
                POST <?= $baseUrl ?>/api/key
            </kbd></p>
        <table class="table table-sm">
            <tr>
                <th><?= Yii::t('app', 'Data') ?></th>
                <th><?= Yii::t('app', 'Value') ?></th>
            </tr>
            <tr>
                <td><code>pin</code></td>
                <td><span class="badge badge-warning">string</span> 1054</td>
            </tr>
        </table>
        <span class="badge badge-success float-right"><?= Yii::t('app', 'Status:') ?> 200</span>
        <p><?= Yii::t('app', 'Response example (JSON):') ?></p>
        <pre>{
    "userId": 1,
    "apiKey": "goBnoSjSSToUTXv744iV"
}</pre>
    <?php Accordion::end(); ?>
</div>
