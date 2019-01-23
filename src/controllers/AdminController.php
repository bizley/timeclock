<?php

declare(strict_types=1);

namespace app\controllers;

use app\base\BaseController;
use app\models\Clock;
use app\models\Holiday;
use app\models\Off;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Class AdminController
 * @package app\controllers
 */
class AdminController extends BaseController
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
     * {@inheritdoc}
     */
    public function remember(): array
    {
        return array_merge(parent::remember(), [
            'index',
            'history',
            'calendar',
        ]);
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
            Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
        } else {
            $user->generatePasswordResetToken();

            if (!$user->save()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
            } else {
                $mail = Yii::$app->mailer->compose([
                    'html' => 'reset-html',
                    'text' => 'reset-text',
                ], [
                    'user' => $user->name,
                    'link' => Url::to(['site/new-password', 'token' => $user->password_reset_token], true)
                ])
                    ->setFrom(Yii::$app->params['email'])
                    ->setTo([$user->email => $user->name])
                    ->setSubject(Yii::t('app', 'Password reset at {company} Timeclock system', ['company' => Yii::$app->params['company']]));

                if (!$mail->send()) {
                    Yii::$app->alert->danger(Yii::t('app', 'There was an error while sending password reset link email.'));
                } else {
                    Yii::$app->alert->success(Yii::t('app', 'Password reset link email has been sent.'));
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
            Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
        } elseif ((int) $user->id === (int) Yii::$app->user->id) {
            Yii::$app->alert->danger(Yii::t('app', 'You can not delete your own account.'));
        } else {
            Clock::deleteAll(['user_id' => $user->id]);
            if (!$user->delete()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while deleting user.'));
            } else {
                Yii::$app->alert->success(Yii::t('app', 'User has been deleted.'));
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
                Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
            }
        }

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
            'months' => Clock::months(),
            'year' => $year,
            'month' => $month,
            'previous' => Clock::months()[$previousMonth],
            'previousYear' => $previousYear,
            'previousMonth' => $previousMonth,
            'next' => Clock::months()[$nextMonth],
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
     * @throws \Exception
     */
    public function actionCalendar($month = null, $year = null, $id = null): string
    {
        [$month, $year, $previousMonth, $previousYear, $nextMonth, $nextYear] = $this->getMonthsAndYears($month, $year);

        $firstDayInMonth = date('N', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 12:00:00'));
        $daysInMonth = (int) date('t', (int) Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-01 12:00:00'));

        $user = null;
        if ($id !== null) {
            $user = User::findOne($id);

            if ($user === null) {
                Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
            }
        }

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

        $users = User::find()->indexBy('id')->all();

        $entries = [];
        foreach ($clock as $session) {
            $day = Yii::$app->formatter->asDate($session->clock_in, 'd');
            if (!array_key_exists($day, $entries)) {
                $entries[$day] = [];
            }

            if (!array_key_exists($session->user_id, $entries[$day])) {
                $entries[$day][$session->user_id] = $users[$session->user_id]->initials;
            }
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $stamp = (new \DateTime(
                $year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day . ' 12:00:00',
                new \DateTimeZone(Yii::$app->timeZone))
            )->getTimestamp();
            foreach ($off as $dayOff) {
                if ($stamp > $dayOff->start_at && $stamp < $dayOff->end_at) {
                    if (!array_key_exists($day, $entries)) {
                        $entries[$day] = [];
                    }

                    if (!array_key_exists($dayOff->user_id, $entries[$day])) {
                        $entries[$day][$dayOff->user_id] = $users[$dayOff->user_id]->initials;
                    }
                }
            }
        }

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
            'employee' => $user,
            'users' => $users,
            'holidays' => Holiday::getMonthHolidays($month, $year),
            'entries' => $entries,
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
            Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
        } elseif ((int) $user->id === (int) Yii::$app->user->id) {
            Yii::$app->alert->danger(Yii::t('app', 'You can not demote your own account.'));
        } else {
            $user->role = User::ROLE_EMPLOYEE;
            if (!$user->save()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
            } else {
                Yii::$app->alert->success(Yii::t('app', 'User has been demoted.'));
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
            Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
        } else {
            $user->role = User::ROLE_ADMIN;
            if (!$user->save()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
            } else {
                Yii::$app->alert->success(Yii::t('app', 'User has been promoted to admin.'));
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * @param int|string $day
     * @param int|string $month
     * @param int|string $year
     * @param int|string $employee
     * @return string|null
     */
    public function actionDay($day, $month, $year, $employee): ?string
    {
        if (!Yii::$app->request->isAjax) {
            return null;
        }

        if (!is_numeric($month) || $month < 1 || $month > 12) {
            $month = date('n');
        }
        if (!is_numeric($year) || $year < 2018) {
            $year = date('Y');
        }
        if (!is_numeric($day) || $day < 1 || $day > 31) {
            $day = date('j');
        }
        if (!is_numeric($employee)) {
            $employee = 0;
        }

        $date = $year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day;

        return $this->renderAjax('day', [
            'day' => $day,
            'month' => Clock::months()[$month],
            'year' => $year,
            'employee' => (int) $employee,
            'users' => User::find()->indexBy('id')->all(),
            'clock' => Clock::find()->where([
                'and',
                ['>=', 'clock_in', (int) Yii::$app->formatter->asTimestamp($date . ' 00:00:00')],
                ['<', 'clock_in', (int) Yii::$app->formatter->asTimestamp($date . ' 23:59:59')],
            ])->orderBy(['clock_in' => SORT_ASC])->all(),
            'off' => Off::find()->where([
                'and',
                ['<=', 'start_at', (int) Yii::$app->formatter->asTimestamp($date . ' 23:59:59')],
                ['>=', 'end_at', (int) Yii::$app->formatter->asTimestamp($date . ' 00:00:00')],
            ])->orderBy(['start_at' => SORT_ASC])->all(),
        ]);
    }
}
