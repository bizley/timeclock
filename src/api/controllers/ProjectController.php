<?php

declare(strict_types=1);

namespace app\api\controllers;

use app\api\models\Project;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Action;
use yii\rest\ActiveController;
use yii\rest\IndexAction;
use yii\web\NotFoundHttpException;

/**
 * Class ProjectController
 * @package app\api\controllers
 */
class ProjectController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = Project::class;

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

        unset($actions['create'], $actions['update'], $actions['delete']);

        $actions['options']['collectionOptions'] = ['GET', 'HEAD', 'OPTIONS'];
        $actions['options']['resourceOptions'] = ['GET', 'HEAD', 'OPTIONS'];

        $findModel = static function ($id, Action $action) {
            /* @var $modelClass Project */
            $modelClass = $action->modelClass;

            $model = $modelClass::find()
                ->where(
                    [
                        'and',
                        new Expression('JSON_CONTAINS(`assignees`, :user)'),
                        ['<>', 'status', Project::STATUS_DELETED],
                        ['id' => $id],
                    ]
                )
                ->params([':user' => (string)Yii::$app->user->id])
                ->limit(1)
                ->one();

            if ($model === null) {
                throw new NotFoundHttpException("Object not found: $id");
            }

            return $model;
        };

        $actions['view']['findModel'] = $findModel;

        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => static function () {
                return (new DynamicModel(['id', 'name', 'color']))
                    ->addRule(['id'], 'integer', ['min' => 1])
                    ->addRule(['name', 'color'], 'string');
            },
        ];
        $actions['index']['prepareDataProvider'] = static function (IndexAction $action, $filter) {
            $requestParams = Yii::$app->getRequest()->getBodyParams();
            if (empty($requestParams)) {
                $requestParams = Yii::$app->getRequest()->getQueryParams();
            }

            /* @var $modelClass Project */
            $modelClass = $action->modelClass;

            $query = $modelClass::find()
                ->where(
                    [
                        'and',
                        new Expression('JSON_CONTAINS(`assignees`, :user)'),
                        ['<>', 'status', Project::STATUS_DELETED],
                    ]
                )
                ->params([':user' => (string)Yii::$app->user->id]);
            if (!empty($filter)) {
                $query->andWhere($filter);
            }

            return new ActiveDataProvider(
                [
                    'query' => $query,
                    'pagination' => [
                        'params' => $requestParams,
                        'validatePage' => false,
                    ],
                    'sort' => [
                        'enableMultiSort' => true,
                        'params' => $requestParams,
                        'defaultOrder' => ['name' => SORT_ASC],
                    ],
                ]
            );
        };

        return $actions;
    }
}
