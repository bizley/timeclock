<?php

declare(strict_types=1);

namespace tests\base;

use app\base\Alert;
use tests\AppTestCase;
use Yii;

/**
 * Class AlertTest
 * @package tests\base
 */
class AlertTest extends AppTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testDanger(): void
    {
        $alert = new Alert();
        $alert->danger('test-danger');

        self::assertEquals(['test-danger'], Yii::$app->session->getFlash('danger'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testSuccess(): void
    {
        $alert = new Alert();
        $alert->success('test-success');

        self::assertEquals(['test-success'], Yii::$app->session->getFlash('success'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testInfo(): void
    {
        $alert = new Alert();
        $alert->info('test-info');

        self::assertEquals(['test-info'], Yii::$app->session->getFlash('info'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testWarning(): void
    {
        $alert = new Alert();
        $alert->warning('test-warning');

        self::assertEquals(['test-warning'], Yii::$app->session->getFlash('warning'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testError(): void
    {
        $alert = new Alert();
        $alert->error('test-error');

        self::assertEquals(['test-error'], Yii::$app->session->getFlash('danger'));
    }
}
