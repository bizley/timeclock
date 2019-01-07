<?php

declare(strict_types=1);

namespace app\widgets\note;

use app\models\NoteInterface;
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
     * @var NoteInterface
     */
    public $model;

    /**
     * @return null|string
     */
    public function run(): ?string
    {
        if ($this->model instanceof NoteInterface && $this->model->getNote() !== null) {

            BootstrapPluginAsset::register($this->view);
            $this->view->registerJs('$("[data-toggle=\"popover\"]").popover();');

            return Html::tag('div', '', [
                    'class' => 'note',
                    'title' => Yii::t('app', 'Note'),
                    'data-toggle' => 'popover',
                    'data-trigger' => 'hover',
                    'data-placement' => 'left',
                    'data-content' => Html::encode($this->model->getNote()),
                ]
            );
        }

        return null;
    }
}
