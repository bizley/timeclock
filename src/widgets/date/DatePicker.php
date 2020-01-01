<?php

declare(strict_types=1);

namespace app\widgets\date;

use Yii;
use yii\bootstrap4\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

use function in_array;
use function is_numeric;
use function substr;

/**
 * Class DatePicker
 * @package app\widgets\date
 *
 * http://t1m0n.name/air-datepicker/docs/
 * https://github.com/t1m0n/air-datepicker
 */
class DatePicker extends InputWidget
{
    /**
     * @var string Date format allowed by widget
     */
    public $dateFormat = 'yyyy-mm-dd';

    /**
     * @var bool Whether to add time part
     */
    public $timePicker = true;

    /**
     * @var string Time format allowed by widget
     */
    public $timeFormat = 'hh:ii';

    /**
     * @var string Minute step for time part scroller
     */
    public $minutesStep = 5;

    /**
     * @var bool Whether to add Today button
     */
    public $todayButton = true;

    /**
     * @var bool Whether to show days from other months
     */
    public $showOtherMonths = true;

    /**
     * @var int Default date to be preselected
     */
    public $date;

    /**
     * @var array Dates to be marked on calendar. Format should be yyyy-MM-dd
     */
    public $marked = [];

    /**
     * @return string
     */
    public static function getLanguage(): string
    {
        $availableLanguages = ['cs', 'da', 'de', 'es', 'fi', 'fr', 'hu', 'nl', 'pl', 'pt', 'ro', 'sk', 'zh'];

        $language = substr(Yii::$app->language, 0, 2);

        if (in_array($language, $availableLanguages, true)) {
            return $language;
        }

        return 'en';
    }

    /**
     * @param array $options
     */
    public function prepareSimpleMarkedDates(array &$options): void
    {
        $dates = Json::encode($this->marked);

        $options['onRenderCell'] = new JsExpression(<<<JS
function(date, cellType) {
    let month = date.getMonth() + 1;
    let day = date.getDate();
    let currentDate = date.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-" + (day < 10 ? "0" : "") + day;
    let markedDates = {$dates};
    if (cellType === "day") {
        if (typeof markedDates.find(function(element) {
            return element === currentDate;
        }) !== "undefined") {
            return {
                classes: "day-taken",
                disabled: true
            };
        }
    }
}
JS
        );
    }

    /**
     * @return string
     */
    public function run(): string
    {
        DatePickerAsset::register($this->view);

        $options = [
            'language' => static::getLanguage(),
            'dateFormat' => $this->dateFormat,
            'onSelect' => new JsExpression(<<<JS
function(formattedDate) {
    $("#{$this->options['id']}").val(formattedDate);
}
JS
            ),
        ];

        if ($this->marked) {
            $this->prepareSimpleMarkedDates($options);
        }

        if ($this->showOtherMonths === false) {
            $options['showOtherMonths'] = false;
        }

        if ($this->todayButton) {
            $options['todayButton'] = new JsExpression('new Date()');
        }

        if ($this->timePicker) {
            $options['timepicker'] = true;
            $options['timeFormat'] = $this->timeFormat;
            $options['minutesStep'] = $this->minutesStep;
        }

        $js = "$(\"#{$this->id}\").datepicker(" . Json::encode($options) . ')';

        if ($this->date !== null) {
            if (is_numeric($this->date)) {
                $milliseconds = $this->date * 1000;
            } else {
                $milliseconds = (int)Yii::$app->formatter->asTimestamp($this->date . ' 12:00:00') * 1000;
            }

            $js .= ".data(\"datepicker\").selectDate(new Date({$milliseconds}))";
        }

        $this->view->registerJs($js . ';');

        return $this->renderInputHtml('hidden') . Html::tag('div', '', ['id' => $this->id]);
    }
}
