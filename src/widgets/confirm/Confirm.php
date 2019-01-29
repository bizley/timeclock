<?php

declare(strict_types=1);

namespace app\widgets\confirm;

use app\widgets\fontawesome\FA;
use Yii;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;
use yii\di\Instance;
use yii\web\Session;

/**
 * Class Confirm
 * @package app\widgets\confirm
 */
class Confirm extends Widget
{
    public const CONFIRM_SESSION_KEY = 'ConfirmationModalToRender';

    /**
     * @var string|array|Session
     */
    public $handler = 'session';

    /**
     * Sets default alerts handler.
     * @throws \yii\base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->handler = Instance::ensure($this->handler, Session::class);
    }

    /**
     * @param string $question
     * @param bool $postMethod
     * @param bool $justQuestion
     * @return string
     */
    public static function ask(string $question, bool $postMethod = true, bool $justQuestion = false): string
    {
        (new static())->handler->set(self::CONFIRM_SESSION_KEY, 1);

        if ($justQuestion) {
            return $question;
        }

        return 'data-confirm="' . str_replace('"', '\"', $question) . '"' . ($postMethod ? ' data-method="post"' : '');
    }

    /**
     * @return null|string
     */
    public function run(): ?string
    {
        if ($this->handler->has(self::CONFIRM_SESSION_KEY)) {

            $this->handler->remove(self::CONFIRM_SESSION_KEY);

            BootstrapPluginAsset::register($this->view);

            $this->view->registerJs(<<<JS
yii.confirm = function (message, ok, cancel) {
    $("#confirmationModal .modal-body").html(message);
    let modal = $("#confirmationModal");
    modal.modal();
    modal.on("hidden.bs.modal", function () {
        !cancel || cancel();
    });
    $("#confirmationOk").click(function () {
        !ok || ok();
    });
};
JS
            );

            return Html::tag(
                'div',
                Html::tag(
                    'div',
                    Html::tag(
                        'div',
                        Html::tag(
                            'div',
                            Html::tag(
                                'h4',
                                Yii::t('app', 'Confirmation required'),
                                [
                                    'class' => 'modal-title',
                                    'id' => 'confirmationModalLabel',
                                ])
                                . Html::button(
                                    Html::tag('span', '&times;', ['aria-hidden' => 'true']),
                                    [
                                        'class' => 'close',
                                        'data-dismiss' => 'modal',
                                        'aria-label' => Yii::t('app', 'Close')
                                    ]
                                ),
                            ['class' => 'modal-header']
                        )
                        . Html::tag('div', '', ['class' => 'modal-body'])
                        . Html::tag(
                            'div',
                            Html::button(
                                FA::icon('times') . ' ' . Yii::t('app', 'Cancel'),
                                [
                                    'id' => 'confirmationCancel',
                                    'class' => 'btn btn-outline-secondary',
                                    'data-dismiss' => 'modal',
                                ]
                            )
                            . Html::button(
                                FA::icon('check-circle') . ' ' . Yii::t('app', 'Confirm'),
                                [
                                    'id' => 'confirmationOk',
                                    'class' => 'btn btn-success',
                                    'data-dismiss' => 'modal',
                                    'data-pjax' => '0',
                                ]
                            ),
                            ['class' => 'modal-footer']
                        ),
                        ['class' => 'modal-content']
                    ),
                    [
                        'class' => 'modal-dialog modal-dialog-centered',
                        'role' => 'document',
                    ]
                ),
                [
                    'class' => 'modal fade',
                    'id' => 'confirmationModal',
                    'tabindex' => '-1',
                    'role' => 'dialog',
                    'aria-labelledby' => 'confirmationModalLabel',
                ]
            );
        }

        return null;
    }
}
