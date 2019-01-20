<?php

declare(strict_types=1);

namespace app\api\controllers;

use app\models\User;
use Yii;
use yii\base\DynamicModel;
use yii\rest\Controller;
use yii\rest\OptionsAction;

/**
 * Class DefaultController
 * @package app\api\controllers
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'options' => [
                'class' => OptionsAction::class,
                'collectionOptions' => ['POST', 'OPTIONS'],
                'resourceOptions' => ['OPTIONS'],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function verbs(): array
    {
        return [
            'index' => ['GET'],
        ];
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
      return [
        'id' => Yii::$app->id,
        'company' => Yii::$app->params['company'],
        'timestamp' => time(),
      ];
    }
}
