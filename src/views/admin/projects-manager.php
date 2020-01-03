<?php

use app\models\Project;
use app\models\User;
use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use app\widgets\modal\Project as ProjectModal;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $projects array */

BootstrapPluginAsset::register($this);

$this->title = Yii::t('app', 'Projects Manager');
$this->params[ProjectModal::PROJECT_MODAL] = true;

/**
 * @var $projects array
 * @var $users array
 * @var $project Project
 */

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Projects Manager') ?></h1>
</div>

<div class="form-group">
    <a href="#" class="btn btn-success" data-toggle="modal" data-target="#<?= ProjectModal::PROJECT_MODAL ?>">
        <?= FA::icon('plus') ?> <?= Yii::t('app', 'Add Project') ?>
    </a>
</div>

<div class="row">
    <div class="col-sm-12 table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col"><?= Yii::t('app', 'Name') ?></th>
                    <th scope="col"><?= Yii::t('app', 'Project Color') ?></th>
                    <th scope="col"><?= Yii::t('app', 'Assigned Employees') ?></th>
                    <th scope="col"><?= Yii::t('app', 'Status') ?></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr <?= $project->status === Project::STATUS_DELETED ? 'class="text-muted"' : '' ?>>
                        <td>
                            <?= $project->status === Project::STATUS_DELETED ? '<del>' : '' ?>
                            <?= Html::encode($project->name) ?>
                            <?= $project->status === Project::STATUS_DELETED ? '</del>' : '' ?>
                        </td>
                        <td><span class="badge" style="width:5rem; background-color:<?= $project->color ?>">&nbsp;</span></td>
                        <td>
                            <?php
                            $assignees = [];
                            foreach ((array) $project->assignees as $assignee) {
                                if (array_key_exists($assignee, $users)) {
                                    $assignees[] = Html::encode($users[$assignee]);
                                }
                            }
                            echo implode(', ', $assignees);
                            ?>
                        </td>
                        <td>
                            <?php switch($project->status) {
                                case Project::STATUS_ACTIVE: echo Yii::t('app', 'Active'); break;
                                case Project::STATUS_LOCKED: echo Yii::t('app', 'In Use'); break;
                                case Project::STATUS_DELETED: echo Yii::t('app', 'Archived'); break;
                            } ?>
                        </td>
                        <td class="text-right text-nowrap">
                            <a href="#" class="btn btn-success btn-sm" data-toggle="modal"
                               data-target="#<?= ProjectModal::PROJECT_EDIT_MODAL ?>"
                               data-id="<?= $project->id ?>"
                               data-name="<?= Html::encode($project->name) ?>"
                               data-color="<?= $project->color ?>"
                               data-assignees="<?= Html::encode(Json::encode($project->assignees)) ?>">
                                <?= FA::icon('pen') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'edit') ?></span>
                            </a>
                            <?php if ($project->status === Project::STATUS_ACTIVE): ?>
                                <a href="<?= Url::to(['admin/project-delete', 'id' => $project->id]) ?>" class="btn btn-danger btn-sm"
                                    <?= Confirm::ask(Yii::t('app', 'Are you sure you want to remove this project permanently?')) ?>>
                                    <?= FA::icon('trash') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'delete') ?></span>
                                </a>
                            <?php elseif ($project->status === Project::STATUS_LOCKED): ?>
                                <a href="<?= Url::to(['admin/project-archive', 'id' => $project->id]) ?>" class="btn btn-warning btn-sm"
                                    <?= Confirm::ask(Yii::t('app', 'Are you sure you want to archive this project?')) ?>>
                                    <?= FA::icon('archive') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'archive') ?></span>
                                </a>
                            <?php elseif ($project->status === Project::STATUS_DELETED): ?>
                                <a href="<?= Url::to(['admin/project-bring-back', 'id' => $project->id]) ?>" class="btn btn-info btn-sm"
                                    <?= Confirm::ask(Yii::t('app', 'Are you sure you want to bring back this project?')) ?>>
                                    <?= FA::icon('undo-alt') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'bring back') ?></span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
