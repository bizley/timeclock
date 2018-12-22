<?php

declare(strict_types=1);

namespace app\api\controllers;

use app\api\models\Clock;
use app\api\models\Off;
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
 * Class OffTimeController
 * @package app\api\controllers
 */
class OffTimeController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = Off::class;

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
            /* @var $modelClass Off */
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
                'startAt' => 'start_at',
                'endAt' => 'end_at',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at',
            ],
            'searchModel' => function () {
                return (new DynamicModel(['id', 'startAt', 'endAt', 'note', 'createdAt', 'updatedAt']))
                    ->addRule(['id', 'startAt', 'endAt', 'createdAt', 'updatedAt'], 'integer', ['min' => 1])
                    ->addRule(['note'], 'string');
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
                ],
                'sort' => [
                    'enableMultiSort' => true,
                    'params' => $requestParams,
                    'defaultOrder' => ['start_at' => SORT_ASC],
                    'attributes' => [
                        'id',
                        'note',
                        'startAt' => [
                            'asc' => ['start_at' => SORT_ASC],
                            'desc' => ['start_at' => SORT_DESC],
                            'default' => SORT_ASC,
                        ],
                        'endAt' => [
                            'asc' => ['end_at' => SORT_ASC],
                            'desc' => ['end_at' => SORT_DESC],
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
