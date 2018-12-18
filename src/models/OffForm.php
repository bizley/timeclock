<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Class OffForm
 * @package app\models
 */
class OffForm extends Model
{
    /**
     * @var int
     */
    public $startYear;

    /**
     * @var int
     */
    public $startMonth;

    /**
     * @var int
     */
    public $startDay;

    /**
     * @var int
     */
    public $endYear;

    /**
     * @var int
     */
    public $endMonth;

    /**
     * @var int
     */
    public $endDay;

    private $off;

    public function __construct(Off $off, array $config = [])
    {
        $this->off = $off;

        $this->startYear = Yii::$app->formatter->asDate($off->start_at, 'y');
        $this->startMonth = Yii::$app->formatter->asDate($off->start_at, 'M');
        $this->startDay = Yii::$app->formatter->asDate($off->start_at, 'd');
        $this->endYear = $off->end_at ? Yii::$app->formatter->asTime($off->end_at, 'y') : null;
        $this->endMonth = $off->end_at ? Yii::$app->formatter->asTime($off->end_at, 'M') : null;
        $this->endDay = $off->end_at ? Yii::$app->formatter->asDate($off->end_at, 'd') : null;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['startYear', 'startMonth', 'startDay'], 'required'],
            [['startYear', 'endYear'], 'number', 'min' => 2018],
            [['startMonth', 'endMonth'], 'number', 'min' => 1, 'max' => 12],
            [['startDay', 'endDay'], 'number', 'min' => 1, 'max' => 31],
            [['startDay', 'endDay'], 'maxDay'],
            [['startDay'], 'verifyStart'],
            [['endDay'], 'verifyEnd'],
        ];
    }

    /**
     * @param int|string $year
     * @param int|string $month
     * @param int|string $day
     * @param int|string $hour
     * @param int|string $minute
     * @return string
     */
    public function prepareDate($year, $month, $day, $hour, $minute): string
    {
        return $year
            . '-'
            . ($month < 10 ? '0' : '')
            . $month
            . '-'
            . ($day < 10 ? '0' : '')
            . $day
            . ' '
            . ($hour < 10 ? '0' : '')
            . $hour
            . ':'
            . ($minute < 10 ? '0' : '')
            . $minute
            . ':00';
    }

    /**
     * @param string $attribute
     */
    public function maxDay(string $attribute): void
    {
        if ($attribute === 'startDay') {
            $year = $this->startYear;
            $month = $this->startMonth;
        } else {
            $year = $this->endYear;
            $month = $this->endMonth;
        }

        $maxDaysInMonth = date('t', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($year, $month, 1, 1, 0)));

        if ($this->$attribute > $maxDaysInMonth) {
            $this->addError($attribute, "Wybrany miesiąc ma tylko $maxDaysInMonth dni.");
        }
    }

    public function verifyStart(): void
    {
        $conditions = [
            'and',
            ['user_id' => Yii::$app->user->id],
            ['<=', 'start_at', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($this->startYear, $this->startMonth, $this->startDay, 0, 0))],
            ['>=', 'end_at', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($this->startYear, $this->startMonth, $this->startDay, 0, 0))],
        ];

        if ($this->off->id !== null) {
            $conditions[] = ['<>', 'id', $this->off->id];
        }

        if (!$this->hasErrors() && Off::find()->where($conditions)->exists()) {
            $this->addError('startDay', 'Wybrany dzień pokrywa się z innym okresem wolnym.');
        }
    }

    public function verifyEnd(): void
    {
        if (!$this->hasErrors()) {
            if (
                ($this->endYear !== '' && $this->endYear !== null)
                || ($this->endMonth !== '' && $this->endMonth !== null)
                || ($this->endDay !== '' && $this->endDay !== null)
            ) {
                if ($this->endYear === '' || $this->endYear === null) {
                    $this->addError('endYear', 'Podaj rok zakończenia okresu wolnego.');
                }
                if ($this->endMonth === '' || $this->endMonth === null) {
                    $this->addError('endMonth', 'Podaj miesiąc zakończenia okresu wolnego.');
                }
                if ($this->endDay === '' || $this->endDay === null) {
                    $this->addError('endDay', 'Podaj dzień zakończenia okresu wolnego.');
                }
            }
        }

        if (
            $this->endYear !== ''
            && $this->endYear !== null
            && $this->endMonth !== ''
            && $this->endMonth !== null
            && $this->endDay !== ''
            && $this->endDay !== null
        ) {
            if (!$this->hasErrors()
                && Yii::$app->formatter->asTimestamp($this->prepareDate($this->startYear, $this->startMonth, $this->startDay, 0, 0))
                >= Yii::$app->formatter->asTimestamp($this->prepareDate($this->endYear, $this->endMonth, $this->endDay, 23, 59))) {
                $this->addError('endDay', 'Dzień zakończenia wolnego nie może być wcześniejszy niż dzień rozpoczęcia.');
            }

            $conditions = [
                'and',
                ['user_id' => Yii::$app->user->id],
                ['<=', 'start_at', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($this->endYear, $this->endMonth, $this->endDay, 23, 59))],
                ['>=', 'end_at', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($this->endYear, $this->endMonth, $this->endDay, 23, 59))],
            ];

            if ($this->off->id !== null) {
                $conditions[] = ['<>', 'id', $this->off->id];
            }

            if (!$this->hasErrors() && Off::find()->where($conditions)->exists()) {
                $this->addError('endDay', 'Wybrany dzień pokrywa się z innym okresem wolnym.');
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'startYear' => 'Rok',
            'startMonth' => 'Miesiąc',
            'startDay' => 'Dzień',
            'endYear' => 'Rok',
            'endMonth' => 'Miesiąc',
            'endDay' => 'Dzień',
        ];
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->off->user_id === null) {
            $this->off->user_id = Yii::$app->user->id;
        }

        $this->off->start_at = (new \DateTime(
            $this->prepareDate($this->startYear, $this->startMonth, $this->startDay, 0, 0),
            new \DateTimeZone(Yii::$app->timeZone))
        )->getTimestamp();

        if ($this->endDay !== '' && $this->endDay !== null) {
            $this->off->end_at = (new \DateTime(
                $this->prepareDate($this->endYear, $this->endMonth, $this->endDay, 23, 59),
                new \DateTimeZone(Yii::$app->timeZone))
            )->getTimestamp();
        } else {
            $this->off->end_at = (new \DateTime(
                $this->prepareDate($this->startYear, $this->startMonth, $this->startDay, 23, 59),
                new \DateTimeZone(Yii::$app->timeZone))
            )->getTimestamp();
        }

        return $this->off->save();
    }
}
