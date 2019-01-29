<?php

declare(strict_types=1);

namespace app\widgets\modal;

use app\widgets\fontawesome\FA;
use Yii;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;

/**
 * Class Clock
 * @package app\widgets\modal
 */
class Clock extends Widget
{
    public const CLOCK_MODAL = 'clockModal';

    /**
     * @var array
     */
    public $params = [];

    public static function button(): string
    {
        $modal = new static();

        BootstrapPluginAsset::register($modal->view);

        $modal->view->params[self::CLOCK_MODAL] = true;

        return Html::tag(
            'span',
            FA::icon('play')  . Yii::t('app', 'Start Session'),
            [
                'class' => 'btn btn-success btn-lg btn-block clock',
                'data-toggle' => 'modal',
                'data-target' => '#' . self::CLOCK_MODAL,
            ]
        );
    }

    /**
     * @return null|string
     */
    public function run(): ?string
    {
        if (array_key_exists(self::CLOCK_MODAL, $this->params)) {
            return $this->render('clock');
        }

        return null;
    }
}
