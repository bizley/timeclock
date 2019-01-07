<?php

declare(strict_types=1);

use app\widgets\modal\Clock;
use yii\bootstrap\Html;

?>
<div id="<?= Clock::CLOCK_MODAL ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="<?= Clock::CLOCK_MODAL ?>Label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?= Html::beginForm(['clock/start']) ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app', 'Close') ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 id="<?= Clock::CLOCK_MODAL ?>Label" class="modal-title"><?= Yii::t('app', 'Confirmation required') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <?= Yii::t('app', 'Are you sure you want to start session?') ?>
                    </div>
                    <?= Html::textarea(
                        'note',
                        '',
                        [
                            'class' => 'form-control',
                            'style' => 'resize:vertical',
                            'placeholder' => Yii::t('app', 'Optional Session Note'),
                        ]
                    ) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" id="clockCancel" class="btn btn-outline pull-left" data-dismiss="modal">
                        <i class="glyphicon glyphicon-ban-circle text-muted"></i> <?= Yii::t('app', 'Cancel') ?>
                    </button>
                    <?= Html::submitButton(
                        Html::tag('i', '', ['class' => 'glyphicon glyphicon-ok-circle'])
                        . ' ' . Yii::t('app', 'Confirm'),
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
