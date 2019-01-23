<?php

namespace app\base;

use yii\base\Action;
use yii\helpers\Url;
use yii\web\Controller;

class BaseController extends Controller
{
    /**
     * Returns controller's ids to remember
     * @return array
     */
    public function remember(): array {
        return [];
    }

    /**
     * @param Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (\in_array($action->id, $this->remember(), true)) {
            Url::remember();
        }
        return true;
    }
}