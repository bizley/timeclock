<?php

declare(strict_types=1);

namespace app\widgets\date;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class DatePickerAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@npm/air-datepicker/dist/';

    /**
     * {@inheritdoc}
     */
    public $css = ['css/datepicker.min.css'];

    /**
     * {@inheritdoc}
     */
    public $js = ['js/datepicker.min.js'];

    /**
     * @var array
     */
    public $depends = [JqueryAsset::class];

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $language = DatePicker::getLanguage();

        $this->js[] = "js/i18n/datepicker.$language.js";

        parent::init();
    }
}
