<?php

declare(strict_types=1);

namespace app\api\controllers;

use app\api\models\Profile;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\rest\OptionsAction;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class ProfileController
 * @package app\api\controllers
 */
class ProfileController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = Profile::class;

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'options' => [
                'class' => OptionsAction::class,
                'collectionOptions' => ['OPTIONS'],
                'resourceOptions' => ['GET', 'HEAD', 'PUT', 'PATCH', 'OPTIONS'],
            ],
        ];
    }

    /**
     * @return Profile
     * @throws NotFoundHttpException
     */
    public function actionView(): Profile
    {
        $model = Profile::findOne(Yii::$app->user->id);

        if ($model === null) {
            throw new NotFoundHttpException('User not found');
        }

        return $model;
    }

    /**
     * @return Profile
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate(): Profile
    {
        $model = Profile::findOne(Yii::$app->user->id);

        if ($model === null) {
            throw new NotFoundHttpException('User not found');
        }

        $model->scenario = 'update';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the user for unknown reason.');
        }

        return $model;
    }
}
