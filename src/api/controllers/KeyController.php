<?php

declare(strict_types=1);

namespace app\api\controllers;

use app\models\User;
use Yii;
use yii\base\DynamicModel;
use yii\rest\Controller;
use yii\rest\OptionsAction;

use function substr;

/**
 * Class KeyController
 * @package app\api\controllers
 */
class KeyController extends Controller
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
            'index' => ['POST'],
        ];
    }

    /**
     * @return DynamicModel|array
     */
    public function actionIndex()
    {
        $form = (new DynamicModel(['pin']))
            ->addRule('pin', 'required')
            ->addRule('pin', 'number');

        $form->load(Yii::$app->request->post(), '');

        if (!$form->validate()) {
            return $form;
        }

        $id = substr($form->pin, 0, -3); // last 3 digits is actual PIN, rest is user ID
        $user = User::findOne(['id' => $id, 'status' => User::STATUS_ACTIVE]);

        if ($user === null || $user->pin_hash === null || !$user->validatePin($form->pin)) {
            $form->addError('pin', Yii::t('app', 'Invalid PIN.'));

            return $form;
        }

        Yii::$app->getResponse()->setStatusCode(200);

        return [
            'userId' => $user->id,
            'apiKey' => $user->api_key,
        ];
    }
}
