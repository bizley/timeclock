<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Class ClockForm
 * @package app\models
 */
class ClockForm extends Model
{
    /**
     * @var int
     */
    public $year;

    /**
     * @var int
     */
    public $month;

    /**
     * @var int
     */
    public $day;

    /**
     * @var int
     */
    public $startHour;

    /**
     * @var int
     */
    public $endHour;

    /**
     * @var int
     */
    public $startMinute;

    /**
     * @var int
     */
    public $endMinute;

    private $session;

    public function __construct(Clock $session, array $config = [])
    {
        $this->session = $session;

        $this->year = Yii::$app->formatter->asDate($session->clock_in, 'y');
        $this->month = Yii::$app->formatter->asDate($session->clock_in, 'M');
        $this->day = Yii::$app->formatter->asDate($session->clock_in, 'd');
        $this->startHour = Yii::$app->formatter->asTime($session->clock_in, 'H');
        $this->startMinute = $this->roundToFive((int) Yii::$app->formatter->asTime($session->clock_in, 'm'));
        $this->endHour = $session->clock_out ? Yii::$app->formatter->asTime($session->clock_out, 'H') : null;
        $this->endMinute = $session->clock_out ? $this->roundToFive((int) Yii::$app->formatter->asTime($session->clock_out, 'm')) : null;

        parent::__construct($config);
    }

    /**
     * @param int $value
     * @return int
     */
    public function roundToFive(int $value): int
    {
        $mod = $value % 5;

        if ($mod <= 2) {
            return $value - $mod;
        }

        return $value + 5 - $mod;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['year', 'month', 'day', 'startHour', 'startMinute'], 'required'],
            [['year'], 'number', 'min' => 2018],
            [['month'], 'number', 'min' => 1, 'max' => 12],
            [['day'], 'number', 'min' => 1, 'max' => 31],
            [['day'], 'maxDay'],
            [['startHour', 'endHour'], 'number', 'min' => 0, 'max' => 23],
            [['startMinute', 'endMinute'], 'number', 'min' => 0, 'max' => 59],
            [['startHour', 'startMinute'], 'verifyStart'],
            [['endHour', 'endMinute'], 'verifyEnd'],
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

    public function maxDay(): void
    {
        $maxDaysInMonth = date('t', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($this->year, $this->month, 1, 1, 0)));

        if ($this->day > $maxDaysInMonth) {
            $this->addError('day', "Wybrany miesiąc ma tylko $maxDaysInMonth dni.");
        }
    }

    public function verifyStart(): void
    {
        $conditions = [
            'and',
            ['user_id' => Yii::$app->user->id],
            ['<=', 'clock_in', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($this->year, $this->month, $this->day, $this->startHour, $this->startMinute))],
            ['>=', 'clock_out', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($this->year, $this->month, $this->day, $this->startHour, $this->startMinute))],
        ];

        if ($this->session->id !== null) {
            $conditions[] = ['<>', 'id', $this->session->id];
        }

        if (!$this->hasErrors() && Clock::find()->where($conditions)->exists()) {
            $this->addError('startHour', 'Wybrana godzina pokrywa się z inną zamkniętą sesją.');
        }
    }

    public function verifyEnd(): void
    {
        if (!$this->hasErrors()) {
            if ($this->endHour !== '' && $this->endHour !== null && ($this->endMinute === '' || $this->endMinute === null)) {
                $this->addError('endMinute', 'Podaj minuty zakończenia sesji.');
            }
            if ($this->endMinute !== '' && $this->endMinute !== null && ($this->endHour === '' || $this->endHour === null)) {
                $this->addError('endHour', 'Podaj godzinę zakończenia sesji.');
            }
        }

        if ($this->endMinute !== '' && $this->endHour !== '' && $this->endMinute !== null && $this->endHour !== null) {
            if (!$this->hasErrors()
                && Yii::$app->formatter->asTimestamp($this->prepareDate($this->year, $this->month, $this->day, $this->startHour, $this->startMinute))
                >= Yii::$app->formatter->asTimestamp($this->prepareDate($this->year, $this->month, $this->day, $this->endHour, $this->endMinute))) {
                $this->addError('endHour', 'Godzina zakończenia sesji musi być późniejsza niż godzina rozpoczęcia.');
            }

            $conditions = [
                'and',
                ['user_id' => Yii::$app->user->id],
                ['<=', 'clock_in', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($this->year, $this->month, $this->day, $this->endHour, $this->endMinute))],
                ['>=', 'clock_out', (int) Yii::$app->formatter->asTimestamp($this->prepareDate($this->year, $this->month, $this->day, $this->endHour, $this->endMinute))],
            ];

            if ($this->session->id !== null) {
                $conditions[] = ['<>', 'id', $this->session->id];
            }

            if (!$this->hasErrors() && Clock::find()->where($conditions)->exists()) {
                $this->addError('endHour', 'Wybrana godzina pokrywa się z inną zamkniętą sesją.');
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'year' => 'Rok',
            'month' => 'Miesiąc',
            'day' => 'Dzień',
            'startHour' => 'Start',
            'endHour' => 'Koniec',
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

        if ($this->session->user_id === null) {
            $this->session->user_id = Yii::$app->user->id;
        }
        $this->session->clock_in = (new \DateTime(
            $this->prepareDate($this->year, $this->month, $this->day, $this->startHour, $this->startMinute),
            new \DateTimeZone(Yii::$app->timeZone))
        )->getTimestamp();

        if ($this->endHour !== '' && $this->endHour !== null) {
            $this->session->clock_out = (new \DateTime(
                $this->prepareDate($this->year, $this->month, $this->day, $this->endHour, $this->endMinute),
                new \DateTimeZone(Yii::$app->timeZone))
            )->getTimestamp();
        }

        return $this->session->save();
    }
}
