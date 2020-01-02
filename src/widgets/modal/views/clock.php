<?php

declare(strict_types=1);

use app\widgets\fontawesome\FA;
use app\widgets\modal\Clock;
use yii\bootstrap4\Html;

/* @var $projects array */
/* @var $defaultProject null|string|int */

?>
<div id="<?= Clock::CLOCK_MODAL ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="<?= Clock::CLOCK_MODAL ?>Label">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <?= Html::beginForm(['/clock/start']) ?>
                <div class="modal-header">
                    <h4 id="<?= Clock::CLOCK_MODAL ?>Label" class="modal-title"><?= Yii::t('app', 'Confirmation required') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app', 'Close') ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong><?= Yii::t('app', 'Are you sure you want to start session?') ?></strong>
                    </div>
                    <div class="form-group">
                        <?= Html::label(
                            Yii::t('app', 'Select project assigned to this session:'),
                            'clock-project'
                        ) ?>
                        <?= Html::dropDownList(
                            'project_id',
                            $defaultProject,
                            $projects,
                            [
                                'class' => 'custom-select',
                                'id' => 'clock-project',
                            ]
                        ) ?>
                    </div>
                    <?= Html::textarea(
                        'note',
                        '',
                        [
                            'class' => 'form-control',
                            'placeholder' => Yii::t('app', 'Optional Session Note'),
                        ]
                    ) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" id="clockCancel" class="btn btn-outline-secondary" data-dismiss="modal">
                        <?= FA::icon('times') ?> <?= Yii::t('app', 'Cancel') ?>
                    </button>
                    <?= Html::submitButton(
                        FA::icon('check-circle') . ' ' . Yii::t('app', 'Confirm'),
                        [
                            'id' => 'clockOk',
                            'class' => 'btn btn-success',
                            'data-pjax' => '0',
                        ]
                    ) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
