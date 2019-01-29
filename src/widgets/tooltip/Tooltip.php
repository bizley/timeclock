<?php

declare(strict_types=1);

namespace app\widgets\tooltip;

use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Widget;

/**
 * Class Tooltip
 * @package app\widgets\tooltip
 */
class Tooltip extends Widget
{
    /**
     * @param string|null $title
     * @param string $placement
     * @return string|null
     */
    public static function add(?string $title = null, string $placement = 'top'): ?string
    {
        if ($title !== null) {

            $tooltip = new static();

            BootstrapPluginAsset::register($tooltip->view);
            $tooltip->view->registerJs('$("[data-toggle=\"tooltip\"]").tooltip();');

            return 'data-toggle="tooltip" data-placement="' . $placement . '" title="' . str_replace('"', '\"', $title) . '"';
        }

        return null;
    }
}
