<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Holidays;
use app\models\LoginForm;
use app\models\NewPasswordForm;
use app\models\RegisterForm;
use app\models\ResetForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends Controller
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
                        'actions' => ['error', 'login', 'register', 'reset', 'new-password'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        return [
            'error' => ErrorAction::class,
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index', [
            'user' => Yii::$app->user->identity
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|Response
     * @throws \yii\base\Exception
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            Yii::$app->alert->success('Konto zostało zarejestrowane.');
            Yii::$app->user->login(User::findByEmail($model->email));

            return $this->redirect(['index']);
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * @return Response
     */
    public function actionLogout(): Response
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * @return string|Response
     * @throws \yii\base\Exception
     */
    public function actionReset()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new ResetForm();
        if ($model->load(Yii::$app->request->post()) && $model->reset()) {
            Yii::$app->alert->success('Link resetujący hasło został wysłany na podany adres email, o ile jest on zarejestrowany w systemie.');
            return $this->goBack();
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $token
     * @return string|Response
     * @throws \yii\base\Exception
     */
    public function actionNewPassword(string $token)
    {
        if (!User::isPasswordResetTokenValid($token)) {
            Yii::$app->alert->danger('Podano nieprawidłowy lub nieważny token resetujący.');
            return $this->redirect(['login']);
        }

        $user = User::findByPasswordResetToken($token);
        if ($user === null) {
            Yii::$app->alert->danger('Podano nieprawidłowy lub nieważny token resetujący.');
            return $this->redirect(['login']);
        }

        $model = new NewPasswordForm($user);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->alert->success('Hasło zostało zmienione.');
            return $this->redirect(['login']);
        }

        return $this->render('new-password', [
            'model' => $model,
        ]);
    }
}
