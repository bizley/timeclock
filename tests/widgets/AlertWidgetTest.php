<?php


declare(strict_types=1);

namespace tests\widgets;

use app\base\Alert as AlertComponent;
use app\widgets\alert\Alert;
use tests\AppTestCase;
use yii\helpers\Html;

/**
 * Class AlertWidgetTest
 * @package tests\widgets
 */
class AlertWidgetTest extends AppTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testOneAlert(): void
    {
        $alert = new AlertComponent();
        $alert->danger('test-one');

        $out = Alert::widget();

        $this->assertEquals(<<<HTML
<div class="alert alert-danger alert-dismissible fade in" role="alert">test-one<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
HTML
        , $out);
    }

    /**
     * @runInSeparateProcess
     */
    public function testMultipleAlerts(): void
    {
        $alert = new AlertComponent();
        $alert->danger('test-one');
        $alert->success('test-two');
        $alert->info('test-three');

        $expected = '';

        $expected .= Html::beginTag('div',['class'=>'alert alert-danger alert-dismissible fade in','role'=>'alert']);
        $expected .= "test-one";
        $expected .= Html::beginTag('button',['type'=>'button','class'=>'close','data-dismiss'=>'alert','aria-label'=>'Close']);
        $expected .= Html::tag('span','&times;',['aria-hidden'=>'true']);
        $expected .= Html::endTag('button');
        $expected .= Html::endTag('div');
        $expected .= "\n";
        $expected .= Html::beginTag('div',['class'=>'alert alert-success alert-dismissible fade in','role'=>'alert']);
        $expected .= "test-two";
        $expected .= Html::beginTag('button',['type'=>'button','class'=>'close','data-dismiss'=>'alert','aria-label'=>'Close']);
        $expected .= Html::tag('span','&times;',['aria-hidden'=>'true']);
        $expected .= Html::endTag('button');
        $expected .= Html::endTag('div');
        $expected .= "\n";
        $expected .= Html::beginTag('div',['class'=>'alert alert-info alert-dismissible fade in','role'=>'alert']);
        $expected .= "test-three";
        $expected .= Html::beginTag('button',['type'=>'button','class'=>'close','data-dismiss'=>'alert','aria-label'=>'Close']);
        $expected .= Html::tag('span','&times;',['aria-hidden'=>'true']);
        $expected .= Html::endTag('button');
        $expected .= Html::endTag('div');

        $out = Alert::widget();

        $this->assertEquals($expected, $out);
    }

    /**
     * @runInSeparateProcess
     */
    public function testNoAlert(): void
    {
        $out = Alert::widget();

        $this->assertEmpty($out);
    }
}
