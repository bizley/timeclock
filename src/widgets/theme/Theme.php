<?php

declare(strict_types=1);

namespace app\widgets\theme;

use app\assets\AppAsset;
use Yii;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;
use yii\helpers\Url;

/**
 * Class Theme
 * @package app\widgets\theme
 */
class Theme extends Widget
{
    /**
     * @return null|string
     */
    public function run(): ?string
    {
        if (!Yii::$app->user->isGuest) {

            $url = Url::to(['profile/theme']);
            $this->view->registerJs("\$(\"#themeSwitcher\").change(function() { if (\$(this).val() !== \"\") { window.location = \"$url/\" + $(this).val(); }});");

            $themes = AppAsset::themes();

            return Html::tag(
                'p',
                Html::dropDownList(
                    'theme',
                    null,
                    ['' => Yii::t('app', 'Switch theme')] + array_combine($themes, $themes),
                    [
                        'class' => 'form-control input-sm',
                        'id' => 'themeSwitcher'
                    ]
                ),
                ['class' => 'pull-right']
            );
        }

        return null;
    }
}
