<?php

declare(strict_types=1);

namespace app\widgets\fontawesome;

use app\assets\FontAwesomeAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class FA
 * @package app\widgets\fontawesome
 *
 * FontAwesome icon renderer.
 * Usage:
 *  FA::icon('arrow-right')
 *  FA::icon('arrow-right', ['style' => FA::REGULAR])
 *  FA::icon('arrow-right', ['options' => ['class' => 'danger']])
 *
 * Icons: https://fontawesome.com/icons?d=gallery&m=free
 */
class FA extends Widget
{
    public const SOLID = 'fas';
    public const REGULAR = 'far';
    public const BRANDS = 'fab';

    /**
     * @var string Icon style
     * Available: 'fas' = solid (default), 'far' = regular, 'fab' = brands.
     */
    public $style = self::SOLID;

    /**
     * @var string Name of the icon (without 'fa-' prefix)
     */
    public $icon;

    /**
     * @var array HTML options to merge with $style and $icon
     */
    public $options = [];

    /**
     * Renders the icon.
     * @param string $icon
     * @param array $config widget additional config
     * @return string
     */
    public static function icon(string $icon, array $config = []): ?string
    {
        try {
            ob_start();
            ob_implicit_flush(0);
            try {
                /* @var $widget Widget */
                $config['class'] = static::class;
                $config['icon'] = $icon;
                $widget = Yii::createObject($config);
                $out = '';
                if ($widget->beforeRun()) {
                    $result = $widget->run();
                    $out = $widget->afterRun($result);
                }
            } catch (\Exception $e) {
                // close the output buffer opened above if it has not been closed already
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                throw $e;
            }

            return ob_get_clean() . $out;

        } catch (\Throwable $exc) {
            Yii::error(['FA widget error', $exc->getMessage(), $exc->getTraceAsString()]);
            return '';
        }
    }

    /**
     * @return string
     */
    public function run(): string
    {
        FontAwesomeAsset::register($this->view);
        $this->prepareOptions();
        return Html::tag('i', '', $this->options);
    }

    /**
     * Merges $style and $icon with $options.
     */
    public function prepareOptions(): void
    {
        $class = ArrayHelper::remove($this->options, 'class', '');
        $class .= ' ' . $this->style . ' fa-' . $this->icon;
        $this->options['class'] = implode(' ', array_filter(array_unique(explode(' ', $class))));
    }
}
