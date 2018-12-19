<?php

declare(strict_types=1);

namespace app\assets;

use Yii;
use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yii\bootstrap\BootstrapAsset;

/**
 * Class AppAsset
 * @package app\assets
 */
class AppAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@webroot';

    /**
     * @var string
     */
    public $baseUrl = '@web';

    /**
     * @var array
     */
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $cssTheme = 'light';

        if (!Yii::$app->user->isGuest && \in_array(Yii::$app->user->identity->theme, ['dark', 'light'], true)) {
            $cssTheme = Yii::$app->user->identity->theme;
        }

        $this->css[] = 'css/timeclock.css';
        $this->css[] = "css/$cssTheme.css";

        parent::init();
    }
}
