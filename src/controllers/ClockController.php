<?php

declare(strict_types=1);

namespace app\controllers;

use app\base\BaseController;
use app\models\Clock;
use app\models\ClockForm;
use app\models\Holiday;
use app\models\Off;
use app\models\OffForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Class ClockController
 * @package app\controllers
 */
class ClockController extends BaseController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'off-delete' => ['post'],
                    'start' => ['post'],
                    'stop' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function remember(): array
    {
        return array_merge(parent::remember(), [
            'history',
            'calendar',
            'edit',
            'add',
            'off-add',
            'off-edit',
        ]);
    }

    /**
     * @return Response
     */
    public function actionStart(): Response
    {
        $clock = new Clock();

        if (!$clock->start()) {
            Yii::$app->alert->danger(Yii::t('app', 'Error while starting session.'));
        }

        return $this->redirect(['site/index']);
    }

    /**
     * @return Response
     */
    public function actionStop(): Response
    {
        $clock = Clock::session();

        if ($clock === null) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find any started session.'));
        } elseif (!$clock->stop()) {
            Yii::$app->alert->danger(Yii::t('app', 'Error while ending session.'));
        }

        return $this->redirect(['site/index']);
    }

    /**
     * @param string|int|null $month
     * @param string|int|null $year
     * @return array
     */
    public function getMonthsAndYears($month, $year): array
    {
        if (!is_numeric($month) || $month < 1 || $month > 12) {
            $month = date('n');
        }
        if (!is_numeric($year) || $year < 2018) {
            $year = date('Y');
        }

        $month = (int) $month;
        $year = (int) $year;

        $previousYear = $year;
        $previousMonth = $month - 1;

        if ($previousMonth === 0) {
            $previousMonth = 12;
            $previousYear--;
        }

        $nextYear = $year;
        $nextMonth = $month + 1;

        if ($nextMonth === 13) {
            $nextMonth = 1;
            $nextYear++;
        }

        return [$month, $year, $previousMonth, $previousYear, $nextMonth, $nextYear];
    }

    /**
     * @param string|int|null $month
     * @param string|int|null $year
     * @return string
     */
    public function actionHistory($month = null, $year = null): string
    {
        [$month, $year, $previousMonth, $previousYear, $nextMonth, $nextYear] = $this->getMonthsAndYears($month, $year);

        return $this->render('history', [
            'months' => Clock::months(),
            'year' => $year,
            'month' => $month,
            'previous' => Clock::months()[$previousMonth],
            'previousYear' => $previousYear,
            'previousMonth' => $previousMonth,
            'next' => Clock::months()[$nextMonth],
            'nextYear' => $nextYear,
            'nextMonth' => $nextMonth,
            'clock' => Clock::find()->where([
                'and',
                ['>=', 'clock_in', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 00:00:00')],
                ['<', 'clock_in', (int) Yii::$app->formatter->asTimestamp($nextYear . '-' . ($nextMonth < 10 ? '0' : '') . $nextMonth . '-01 00:00:00')],
                ['user_id' => Yii::$app->user->id],
            ])->orderBy(['clock_in' => SORT_ASC])->all(),
            'off' => Off::find()->where([
                'and',
                ['<', 'start_at', (int) Yii::$app->formatter->asTimestamp($nextYear . '-' . ($nextMonth < 10 ? '0' : '') . $nextMonth . '-01 00:00:00')],
                ['>', 'end_at', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 00:00:00')],
                ['user_id' => Yii::$app->user->id],
            ])->orderBy(['start_at' => SORT_ASC])->all(),
        ]);
    }

    /**
     * @param string|int|null $month
     * @param string|int|null $year
     * @return string
     */
    public function actionCalendar($month = null, $year = null): string
    {
        [$month, $year, $previousMonth, $previousYear, $nextMonth, $nextYear] = $this->getMonthsAndYears($month, $year);

        $firstDayInMonth = date('N', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 12:00:00'));
        $daysInMonth = (int) date('t', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 12:00:00'));

        return $this->render('calendar', [
            'months' => Clock::months(),
            'year' => $year,
            'month' => $month,
            'previous' => Clock::months()[$previousMonth],
            'previousYear' => $previousYear,
            'previousMonth' => $previousMonth,
            'next' => Clock::months()[$nextMonth],
            'nextYear' => $nextYear,
            'nextMonth' => $nextMonth,
            'firstDayInMonth' => $firstDayInMonth,
            'daysInMonth' => $daysInMonth,
            'clock' => Clock::find()->where([
                'and',
                ['>=', 'clock_in', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 00:00:00')],
                ['<', 'clock_in', (int) Yii::$app->formatter->asTimestamp($nextYear . '-' . ($nextMonth < 10 ? '0' : '') . $nextMonth . '-01 00:00:00')],
                ['user_id' => Yii::$app->user->id],
            ])->orderBy(['clock_in' => SORT_ASC])->all(),
            'holidays' => Holiday::getMonthHolidays($month, $year),
            'off' => Off::find()->where([
                'and',
                ['<', 'start_at', (int) Yii::$app->formatter->asTimestamp($nextYear . '-' . ($nextMonth < 10 ? '0' : '') . $nextMonth . '-01 00:00:00')],
                ['>', 'end_at', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 00:00:00')],
                ['user_id' => Yii::$app->user->id],
            ])->orderBy(['start_at' => SORT_ASC])->all(),
        ]);
    }

    /**
     * @param string|int $id
     * @return Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $clock = Clock::find()->where([
            'id' => (int) $id,
            'user_id' => Yii::$app->user->id,
        ])->one();

        if ($clock === null) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find session of given ID.'));
        } else {
            if (!$clock->delete()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while deleting session.'));
            } else {
                Yii::$app->alert->success(Yii::t('app', 'Session has been deleted.'));
            }
        }

        return $this->goBack();
    }

    /**
     * @param string|int $id
     * @return string|Response
     * @throws \Exception
     */
    public function actionEdit($id)
    {
        $session = Clock::find()->where([
            'id' => (int) $id,
            'user_id' => Yii::$app->user->id,
        ])->one();

        if ($session === null) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find session of given ID.'));
            return $this->goBack();
        }

        $model = new ClockForm($session);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->alert->success(Yii::t('app', 'Session has been saved.'));
            return $this->goBack();
        }

        return $this->render('edit', [
            'session' => $session,
            'model' => $model,
        ]);
    }

    /**
     * @param string|int|null $month
     * @param string|int|null $year
     * @param string|int|null $day
     * @return string|Response
     * @throws \Exception
     */
    public function actionAdd($month = null, $year = null, $day = null)
    {
        if (!is_numeric($month) || $month < 1 || $month > 12) {
            $month = date('n');
        }
        if (!is_numeric($year) || $year < 2018) {
            $year = date('Y');
        }
        if (!is_numeric($day) || $day < 1 || $day > 31) {
            $day = date('j');
        }

        $model = new ClockForm(new Clock([
            'clock_in' => (new \DateTime(
                $year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day . date(' H:i:s'),
                new \DateTimeZone(Yii::$app->timeZone))
            )->getTimestamp()
        ]));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->alert->success(Yii::t('app', 'Session has been saved.'));
            return $this->goBack();
        }

        return $this->render('add', [
            'model' => $model,
        ]);
    }

    /**
     * @param string|int|null $month
     * @param string|int|null $year
     * @param string|int|null $day
     * @return string|Response
     * @throws \Exception
     */
    public function actionOffAdd($month = null, $year = null, $day = null)
    {
        if (!is_numeric($month) || $month < 1 || $month > 12) {
            $month = date('n');
        }
        if (!is_numeric($year) || $year < 2018) {
            $year = date('Y');
        }
        if (!is_numeric($day) || $day < 1 || $day > 31) {
            $day = date('j');
        }

        $model = new OffForm(new Off([
            'start_at' => (new \DateTime(
                $year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day . date(' 00:00:00'),
                new \DateTimeZone(Yii::$app->timeZone))
            )->getTimestamp(),
            'end_at' => (new \DateTime(
                $year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day . date(' 23:59:59'),
                new \DateTimeZone(Yii::$app->timeZone))
            )->getTimestamp(),
        ]));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->alert->success(Yii::t('app', 'Off-time has been saved.'));
            return $this->goBack();
        }

        return $this->render('off-add', [
            'model' => $model,
        ]);
    }

    /**
     * @param string|int $id
     * @return string|Response
     * @throws \Exception
     */
    public function actionOffEdit($id)
    {
        $off = Off::find()->where([
            'id' => (int) $id,
            'user_id' => Yii::$app->user->id,
        ])->one();

        if ($off === null) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find off-time of given ID.'));
            return $this->goBack();
        }

        $model = new OffForm($off);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->alert->success(Yii::t('app', 'Off-time has been saved.'));
            return $this->goBack();
        }

        return $this->render('off-edit', [
            'off' => $off,
            'model' => $model,
        ]);
    }

    /**
     * @param string|int $id
     * @return Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionOffDelete($id): Response
    {
        $off = Off::find()->where([
            'id' => (int) $id,
            'user_id' => Yii::$app->user->id,
        ])->one();

        if ($off === null) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find off-time of given ID.'));
        } else {
            if (!$off->delete()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while deleting off-time.'));
            } else {
                Yii::$app->alert->success(Yii::t('app', 'Off-time has been deleted.'));
            }
        }

        return $this->goBack();
    }
}
