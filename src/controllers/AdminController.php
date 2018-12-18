<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Clock;
use app\models\Holidays;
use app\models\Off;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class AdminController
 * @package app\controllers
 */
class AdminController extends Controller
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
                    'reset' => ['post'],
                    'delete' => ['post'],
                    'promote' => ['post'],
                    'demote' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (Yii::$app->user->identity->role !== User::ROLE_ADMIN) {
            Yii::$app->response->redirect(['site/index']);
            return false;
        }

        return true;
    }

    /**
     * @return string|Response
     */
    public function actionIndex()
    {
        $users = User::find()->orderBy(['name' => SORT_ASC])->all();

        return $this->render('index', [
            'users' => $users,
        ]);
    }

    /**
     * @param string|int $id
     * @return Response
     * @throws \yii\base\Exception
     */
    public function actionReset($id): Response
    {
        $user = User::findOne($id);

        if ($user === null) {
            Yii::$app->alert->danger('Nie znaleziono użytkownika o podanym ID.');
        } else {
            $user->generatePasswordResetToken();

            if (!$user->save()) {
                Yii::$app->alert->danger('Wystąpił błąd w czasie zapisywania użytkownika.');
            } else {
                $mail = Yii::$app->mailer->compose([
                    'html' => 'reset-html',
                    'text' => 'reset-text',
                ], [
                    'user' => $user->name,
                    'link' => Url::to(['site/new-password', 'token' => $user->password_reset_token], true)
                ])
                    ->setFrom('notice@semfleet.com')
                    ->setTo([$user->email => $user->name])
                    ->setSubject('Reset hasła w Company Timeclock');

                if (!$mail->send()) {
                    Yii::$app->alert->danger('Wystąpił błąd podczas wysyłąnia emaila z linkiem resetującym hasło.');
                } else {
                    Yii::$app->alert->success('Email z linkiem resetującym hasło został wysłany.');
                }
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * @param string|int $id
     * @return Response
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function actionDelete($id): Response
    {
        $user = User::findOne($id);

        if ($user === null) {
            Yii::$app->alert->danger('Nie znaleziono użytkownika o podanym ID.');
        } elseif ((int) $user->id === (int) Yii::$app->user->id) {
            Yii::$app->alert->danger('Nie możesz usunąć własnego konta.');
        } else {
            Clock::deleteAll(['user_id' => $user->id]);
            if (!$user->delete()) {
                Yii::$app->alert->danger('Wystąpił błąd podczas usuwania użytkownika.');
            } else {
                Yii::$app->alert->success('Użytkownik został usunięty.');
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * @param string|int|null $month
     * @param string|int|null $year
     * @return array
     */
    public function getMonthsAndYears($month, $year): array
    {
        if ($month === null || $month < 1 || $month > 12) {
            $month = date('n');
        }
        if ($year === null || $year < 2018) {
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
     * @param string|int|null $id
     * @return string
     */
    public function actionHistory($month = null, $year = null, $id = null): string
    {
        [$month, $year, $previousMonth, $previousYear, $nextMonth, $nextYear] = $this->getMonthsAndYears($month, $year);

        $user = null;
        if ($id !== null) {
            $user = User::findOne($id);

            if ($user === null) {
                Yii::$app->alert->danger('Nie znaleziono użytkownika o podanym ID.');
            }
        }

        Url::remember();

        $conditions = [
            'and',
            ['>=', 'clock_in', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 00:00:00')],
            ['<', 'clock_in', (int) Yii::$app->formatter->asTimestamp($nextYear . '-' . ($nextMonth < 10 ? '0' : '') . $nextMonth . '-01 00:00:00')],
        ];
        if ($user !== null) {
            $conditions[] = ['user_id' => $user->id];
        }
        $clock = Clock::find()->where($conditions)->orderBy(['clock_in' => SORT_ASC])->all();

        $conditions = [
            'and',
            ['<', 'start_at', (int) Yii::$app->formatter->asTimestamp($nextYear . '-' . ($nextMonth < 10 ? '0' : '') . $nextMonth . '-01 00:00:00')],
            ['>', 'end_at', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 00:00:00')],
        ];
        if ($user !== null) {
            $conditions[] = ['user_id' => $user->id];
        }
        $off = Off::find()->where($conditions)->orderBy(['start_at' => SORT_ASC])->all();

        return $this->render('history', [
            'months' => Clock::$months,
            'year' => $year,
            'month' => $month,
            'previous' => Clock::$months[$previousMonth],
            'previousYear' => $previousYear,
            'previousMonth' => $previousMonth,
            'next' => Clock::$months[$nextMonth],
            'nextYear' => $nextYear,
            'nextMonth' => $nextMonth,
            'clock' => $clock,
            'employee' => $user,
            'users' => User::find()->indexBy('id')->all(),
            'off' => $off,
        ]);
    }

    /**
     * @param string|int|null $month
     * @param string|int|null $year
     * @param string|int|null $id
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCalendar($month = null, $year = null, $id = null): string
    {
        [$month, $year, $previousMonth, $previousYear, $nextMonth, $nextYear] = $this->getMonthsAndYears($month, $year);

        $firstDayInMonth = (int) Yii::$app->formatter->asDate($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 12:00:00', 'e');
        $daysInMonth = (int) date('t', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 12:00:00'));

        $user = null;
        if ($id !== null) {
            $user = User::findOne($id);

            if ($user === null) {
                Yii::$app->alert->danger('Nie znaleziono użytkownika o podanym ID.');
            }
        }

        Url::remember();

        $conditions = [
            'and',
            ['>=', 'clock_in', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 00:00:00')],
            ['<', 'clock_in', (int) Yii::$app->formatter->asTimestamp($nextYear . '-' . ($nextMonth < 10 ? '0' : '') . $nextMonth . '-01 00:00:00')],
        ];
        if ($user !== null) {
            $conditions[] = ['user_id' => $user->id];
        }
        $clock = Clock::find()->where($conditions)->orderBy(['clock_in' => SORT_ASC])->all();

        $conditions = [
            'and',
            ['<', 'start_at', (int) Yii::$app->formatter->asTimestamp($nextYear . '-' . ($nextMonth < 10 ? '0' : '') . $nextMonth . '-01 00:00:00')],
            ['>', 'end_at', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 00:00:00')],
        ];
        if ($user !== null) {
            $conditions[] = ['user_id' => $user->id];
        }
        $off = Off::find()->where($conditions)->orderBy(['start_at' => SORT_ASC])->all();

        return $this->render('calendar', [
            'months' => Clock::$months,
            'year' => $year,
            'month' => $month,
            'previous' => Clock::$months[$previousMonth],
            'previousYear' => $previousYear,
            'previousMonth' => $previousMonth,
            'next' => Clock::$months[$nextMonth],
            'nextYear' => $nextYear,
            'nextMonth' => $nextMonth,
            'firstDayInMonth' => $firstDayInMonth,
            'daysInMonth' => $daysInMonth,
            'clock' => $clock,
            'employee' => $user,
            'users' => User::find()->indexBy('id')->all(),
            'holidays' => Holidays::getMonthHolidays($month, $year),
            'off' => $off,
        ]);
    }

    /**
     * @param string|int $id
     * @return Response
     * @throws \Throwable
     */
    public function actionDemote($id): Response
    {
        $user = User::findOne($id);

        if ($user === null) {
            Yii::$app->alert->danger('Nie znaleziono użytkownika o podanym ID.');
        } elseif ((int) $user->id === (int) Yii::$app->user->id) {
            Yii::$app->alert->danger('Nie możesz zdegradować własnego konta.');
        } else {
            $user->role = User::ROLE_EMPLOYEE;
            if (!$user->save()) {
                Yii::$app->alert->danger('Wystąpił błąd podczas zapisywania użytkownika.');
            } else {
                Yii::$app->alert->success('Użytkownik został zdegradowany.');
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * @param string|int $id
     * @return Response
     * @throws \Throwable
     */
    public function actionPromote($id): Response
    {
        $user = User::findOne($id);

        if ($user === null) {
            Yii::$app->alert->danger('Nie znaleziono użytkownika o podanym ID.');
        } else {
            $user->role = User::ROLE_ADMIN;
            if (!$user->save()) {
                Yii::$app->alert->danger('Wystąpił błąd podczas zapisywania użytkownika.');
            } else {
                Yii::$app->alert->success('Użytkownik został awansowany na admina.');
            }
        }

        return $this->redirect(['index']);
    }
}
