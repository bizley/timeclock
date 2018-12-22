<?php

declare(strict_types=1);

namespace app\api\controllers;

use app\api\models\Clock;
use app\api\models\Holiday;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\rest\IndexAction;

/**
 * Class HolidayController
 * @package app\api\controllers
 */
class HolidayController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = Holiday::class;

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

        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => function () {
                return (new DynamicModel(['year', 'month', 'day']))
                    ->addRule(['year', 'month', 'day'], 'integer', ['min' => 1]);
            },
        ];
        $actions['index']['prepareDataProvider'] = function (IndexAction $action, $filter) {
            $requestParams = Yii::$app->getRequest()->getBodyParams();
            if (empty($requestParams)) {
                $requestParams = Yii::$app->getRequest()->getQueryParams();
            }

            /* @var $modelClass Clock */
            $modelClass = $action->modelClass;

            $query = $modelClass::find();
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
                    'defaultOrder' => [
                        'year' => SORT_ASC,
                        'month' => SORT_ASC,
                        'day' => SORT_ASC,
                    ],
                ],
            ]);
        };

        return $actions;
    }

    /**
     * @return array
     */
    protected function verbs(): array
    {
        $verbs = parent::verbs();

        $verbs['fetch'] = ['POST'];

        return $verbs;
    }

    /**
     * @return DynamicModel|null
     */
    public function actionFetch(): ?DynamicModel
    {
        $form = (new DynamicModel(['year']))
            ->addRule('year', 'filter', ['filter' => 'intval'])
            ->addRule('year', 'integer', ['min' => 1]);

        $form->load(Yii::$app->request->post(), '');

        if (!$form->validate()) {
            return $form;
        }

        if (!Holiday::isYearPopulated($form->year)) {
            Holiday::populateYear($form->year);
        }

        Yii::$app->getResponse()->setStatusCode(204);

        return null;
    }
}
