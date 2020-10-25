<?php

use app\models\TerminalForm;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $model TerminalForm
 */

$this->title = Yii::t('app', 'Adding Terminal Data');

$this->registerJs(<<<JS
    $(document).ready(function() { 
        document.getElementById('terminalform-delete').onclick = function() {
                var box = document.querySelector('input[name="TerminalForm[delete]"]:checked');
                if (box) {
                    if (box.value === '1') {
                        document.getElementsByClassName('field-terminalform-imagefile')[0].style.display = 'none';
                    } else {
                        document.getElementsByClassName('field-terminalform-imagefile')[0].style.display = 'block';
                    }
                }
        }
    });
JS
);
?>

<div class="form-group">
    <h1><?= Yii::t('app', 'Adding Terminal Data') ?></h1>
</div>

<div class="row">
    <div class="col-lg-2">
        <div class="form-group">
            <a href="<?= Url::previous() ?>" class="btn btn-outline-primary btn-block">
                <?= FA::icon('backward') ?>
                <?= Yii::t('app', 'Go Back') ?>
            </a>
        </div>
    </div>
    <div class="col-lg-8">
        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'tag')->textInput() ?>
            <?= $form->field($model, 'delete')->radioList([
                    true => Yii::t('app', 'Delete'),
                    false => Yii::t('app', 'Upload / Keep')])
            ?>
            <?= $form->field($model, 'imageFile')->fileInput() ?>
            <div class="form-group text-right">
                <?= Html::submitButton(
                    FA::icon('check-circle') . ' ' . Yii::t('app', 'Save'),
                    [
                        'class' => 'btn btn-primary',
                        'name' => 'save-button',
                    ]
                ) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
