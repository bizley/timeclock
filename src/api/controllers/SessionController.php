<?php

declare(strict_types=1);

namespace app\api\controllers;

use app\api\models\Clock;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Action;
use yii\rest\ActiveController;
use yii\data\ActiveDataFilter;
use yii\rest\IndexAction;
use yii\web\NotFoundHttpException;

/**
 * Class SessionController
 * @package app\api\controllers
 */
class SessionController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = Clock::class;

    /**
     * @var string
     */
    public $updateScenario = 'update';

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
        $actions = parent::actions();

        $findModel = function ($id, Action $action) {
            /* @var $modelClass Clock */
            $modelClass = $action->modelClass;

            $model = $modelClass::findOne([
                'id' => $id,
                'user_id' => Yii::$app->user->id,
            ]);

            if ($model === null) {
                throw new NotFoundHttpException("Object not found: $id");
            }

            return $model;
        };

        $actions['view']['findModel'] = $findModel;
        $actions['update']['findModel'] = $findModel;
        $actions['delete']['findModel'] = $findModel;

        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'attributeMap' => [
                'clockIn' => 'clock_in',
                'clockOut' => 'clock_out',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at',
            ],
            'searchModel' => function () {
                return (new DynamicModel(['id', 'clockIn', 'clockOut', 'createdAt', 'updatedAt']))
                    ->addRule(['id', 'clockIn', 'clockOut', 'createdAt', 'updatedAt'], 'integer', ['min' => 1]);
            },
        ];
        $actions['index']['prepareDataProvider'] = function (IndexAction $action, $filter) {
            $requestParams = Yii::$app->getRequest()->getBodyParams();
            if (empty($requestParams)) {
                $requestParams = Yii::$app->getRequest()->getQueryParams();
            }

            /* @var $modelClass Clock */
            $modelClass = $action->modelClass;

            $query = $modelClass::find()->andWhere(['user_id' => Yii::$app->user->id]);
            if (!empty($filter)) {
                $query->andWhere($filter);
            }

            return new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'params' => $requestParams,
                    'validatePage' => false,
                ],
                'sort' => [
                    'enableMultiSort' => true,
                    'params' => $requestParams,
                    'defaultOrder' => ['clockIn' => SORT_ASC],
                    'attributes' => [
                        'id',
                        'clockIn' => [
                            'asc' => ['clock_in' => SORT_ASC],
                            'desc' => ['clock_in' => SORT_DESC],
                            'default' => SORT_ASC,
                        ],
                        'clockOut' => [
                            'asc' => ['clock_out' => SORT_ASC],
                            'desc' => ['clock_out' => SORT_DESC],
                            'default' => SORT_ASC,
                        ],
                        'createdAt' => [
                            'asc' => ['created_at' => SORT_ASC],
                            'desc' => ['created_at' => SORT_DESC],
                            'default' => SORT_ASC,
                        ],
                        'updatedAt' => [
                            'asc' => ['updated_at' => SORT_ASC],
                            'desc' => ['updated_at' => SORT_DESC],
                            'default' => SORT_ASC,
                        ],
                    ],
                ],
            ]);
        };

        return $actions;
    }
}
