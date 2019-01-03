<?php

declare(strict_types=1);

namespace app\widgets\note;

use app\models\Off;
use Yii;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

/**
 * Class Note
 * @package app\widgets\note
 */
class Note extends Widget
{
    /**
     * @var Off
     */
    public $offtime;

    /**
     * @return null|string
     */
    public function run(): ?string
    {
        if (!empty($this->offtime->note)) {

            BootstrapPluginAsset::register($this->view);
            $this->view->registerJs('$("[data-toggle=\"popover\"]").popover();');

            return Html::tag(
                'span',
                Html::tag('i', '', ['class' => 'glyphicon glyphicon-comment']),
                [
                    'class' => 'text-info pull-right text-danger',
                    'title' => Yii::t('app', 'Off-time Note'),
                    'data-toggle' => 'popover',
                    'data-trigger' => 'hover',
                    'data-placement' => 'left',
                    'data-content' => Html::encode($this->offtime->note),
                ]
            );
        }

        return null;
    }
}
