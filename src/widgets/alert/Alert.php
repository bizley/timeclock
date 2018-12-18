<?php

declare(strict_types=1);

namespace app\widgets\alert;

use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\di\Instance;
use yii\web\Session;

/**
 * Class Alert
 * @package keystone\common\widgets\alert
 */
class Alert extends Widget
{
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
     * @return null|string
     */
    public function run(): ?string
    {
        $flashes = $this->handler->getAllFlashes();

        if ($flashes) {
            $this->registerPlugin('alert');

            $output = [];

            foreach ($flashes as $key => $messages) {
                foreach ((array)$messages as $message) {
                    $output[] = Html::tag('div', $message
                        . Html::button(Html::tag('span', '&times;', ['aria-hidden' => 'true']), [
                            'class' => 'close',
                            'data-dismiss' => 'alert',
                            'aria-label' => 'Zamknij',
                        ]),
                        [
                            'class' => "alert alert-{$key} alert-dismissible fade in",
                            'role' => 'alert',
                        ]);
                }
            }

            if ($output) {
                return implode("\n", $output);
            }
        }
        return null;
    }
}
