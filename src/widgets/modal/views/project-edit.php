<?php

declare(strict_types=1);

use app\widgets\fontawesome\FA;
use app\widgets\modal\Project;
use yii\bootstrap4\Html;

/* @var $users array */
/* @var $color string */
?>
<div id="<?= Project::PROJECT_EDIT_MODAL ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="<?= Project::PROJECT_EDIT_MODAL ?>Label">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <?= Html::beginForm(['/admin/project-update']) ?>
                <input type="hidden" name="id" value="" id="projectEditId">
                <div class="modal-header">
                    <h4 id="<?= Project::PROJECT_EDIT_MODAL ?>Label" class="modal-title"><?= Yii::t('app', 'Update Project') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app', 'Close') ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="projectEditName"><?= Yii::t('app', 'Project Name') ?></label>
                        <input type="text" name="name" class="form-control" id="projectEditName">
                    </div>
                    <div class="form-group row">
                        <label for="projectEditColor" class="col-sm-4 col-form-label"><?= Yii::t('app', 'Project Color') ?></label>
                        <div class="col-sm-3">
                            <input type="color" name="color" class="form-control" id="projectEditColor" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="projectEditAssignees"><?= Yii::t('app', 'Project Assignees') ?></label>
                        <select name="assignees[]" class="custom-select" aria-describedby="assigneesEditHelp" id="projectEditAssignees" size="5" multiple>
                            <?php foreach ($users as $id => $user): ?>
                            <option value="<?= $id ?>"><?= Html::encode($user) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small id="assigneesEditHelp" class="form-text text-muted"><?= Yii::t('app', 'Control / shift + click to select / deselect multiple') ?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="projectEditCancel" class="btn btn-outline-secondary" data-dismiss="modal">
                        <?= FA::icon('times') ?> <?= Yii::t('app', 'Cancel') ?>
                    </button>
                    <?= Html::submitButton(
                        FA::icon('check-circle') . ' ' . Yii::t('app', 'Save'),
                        [
                            'id' => 'projectEditOk',
                            'class' => 'btn btn-success',
                            'data-pjax' => '0',
                        ]
                    ) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
