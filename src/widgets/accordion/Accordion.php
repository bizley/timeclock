<?php

declare(strict_types=1);

namespace app\widgets\accordion;

use app\widgets\fontawesome\FA;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;

/**
 * Class Accordion
 * @package app\widgets\accordion
 */
class Accordion extends Widget
{
    /**
     * @var string
     */
    public $parentId;

    /**
     * @var string
     */
    public $header;

    public function init(): void
    {
        parent::init();

        $out = Html::beginTag('div', ['class' => 'card']);
        $out .= Html::beginTag(
            'div',
            [
                'class' => 'card-header p-1',
                'id' => 'heading-' . $this->getId(),
            ]
        );
        $out .= Html::beginTag('h5', ['class' => 'mb-0']);
        $out .= Html::button(
            FA::icon('angle-right') . ' ' . $this->header,
            [
                'class' => 'btn btn-link',
                'data-toggle' => 'collapse',
                'data-target' => '#collapse-' . $this->getId(),
                'aria-expanded' => 'false',
                'aria-controls' => 'collapse-' . $this->getId(),
            ]
        );
        $out .= Html::endTag('h5');
        $out .= Html::endTag('div');
        $out .= Html::beginTag(
            'div',
            [
                'class' => 'collapse',
                'id' => 'collapse-' . $this->getId(),
                'aria-labelledby' => 'heading-' . $this->getId(),
                'data-parent' => '#' . $this->parentId,
            ]
        );
        $out .= Html::beginTag('div', ['class' => 'card-body']);

        echo $out;
    }

    /**
     * @return null|string
     */
    public function run(): ?string
    {
        BootstrapPluginAsset::register($this->view);

        return Html::endTag('div') . Html::endTag('div') . Html::endTag('div');
    }
}
