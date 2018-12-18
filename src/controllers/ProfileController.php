<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ProfileForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class ProfileController
 * @package app\controllers
 */
class ProfileController extends Controller
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
        ];
    }

    /**
     * @return string|Response
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $model = new ProfileForm();

        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->alert->success(Yii::t('app', 'Profile has been updated.'));
            return $this->refresh();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
