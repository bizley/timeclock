<?php

declare(strict_types=1);

namespace tests\widgets;

use app\base\Alert as AlertComponent;
use app\widgets\alert\Alert;
use tests\AppTestCase;

/**
 * Class AlertWidgetTest
 * @package tests\widgets
 */
class AlertWidgetTest extends AppTestCase
{
    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testOneAlert(): void
    {
        $alert = new AlertComponent();
        $alert->danger('test-one');

        $out = Alert::widget();

        self::assertEquals('<div class="alert alert-danger alert-dismissible fade show" role="alert">test-one<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>', $out);
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testMultipleAlerts(): void
    {
        $alert = new AlertComponent();
        $alert->danger('test-one');
        $alert->success('test-two');
        $alert->info('test-three');

        $out = Alert::widget();

        $this->assertEqualsWithoutLineEndings(<<<HTML
<div class="alert alert-danger alert-dismissible fade show" role="alert">test-one<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
<div class="alert alert-success alert-dismissible fade show" role="alert">test-two<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
<div class="alert alert-info alert-dismissible fade show" role="alert">test-three<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
HTML
            , $out);
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testNoAlert(): void
    {
        $out = Alert::widget();

        self::assertEmpty($out);
    }
}
