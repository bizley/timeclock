<?php

declare(strict_types=1);

namespace app\base;

use yii\base\Action;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

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

    /**
     * Overloaded goBack() function with extra stayOnPage param.
     * If stayOnPage is false it's calling parent goBack(), when stayOnPage is true it redirects to URL that is stored in
     * session with key 'rememberedUrl'.
     * @param null|string $defaultUrl
     * @param bool $stayOnPage
     * @return Response
     * @since 2.1.0
     */
    public function goBack($defaultUrl = null, bool $stayOnPage = false): Response
    {
        if (!$stayOnPage) {
            return parent::goBack($defaultUrl);
        }

        return $this->redirect(\Yii::$app->getSession()->get('rememberedUrl', $defaultUrl));
    }
}
