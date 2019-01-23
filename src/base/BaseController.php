<?php

declare(strict_types=1);

namespace app\base;

use yii\base\Action;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Class BaseController
 * @package app\base
 */
class BaseController extends Controller
{

    /**
     * Returns controller's ids to remember
     * @return array
     */
    public function remember(): array
    {
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

        if (in_array($action->id, $this->remember(), true)) {
            $previous = Url::previous('rememberedUrl');
            if ($previous !== Url::to()) {
                Url::remember($previous);
                Url::remember('', 'rememberedUrl');
            }
        }

        return true;
    }
}
