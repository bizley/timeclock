<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class FontAwesomeAsset
 * @package app\assets
 *
 * FontAwesome 5 Free CSS
 * https://fontawesome.com/
 */
class FontAwesomeAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@npm/fortawesome--fontawesome-free/';

    /**
     * {@inheritdoc}
     */
    public $css = ['css/all.css'];

    /**
     * {@inheritdoc}
     */
    public $publishOptions = ['only' => ['css/all.css', 'webfonts/*']];
}
