<?php

declare(strict_types=1);

namespace tests\widgets;

use app\widgets\fontawesome\FA;
use tests\AppTestCase;

/**
 * Class FATest
 * @package tests\widgets
 */
class FATest extends AppTestCase
{
    public function testRenderDefault(): void
    {
        $output = FA::icon('test');
        $this->assertEquals('<i class="fas fa-test"></i>', $output);
    }

    public function testRenderSolid(): void
    {
        $output = FA::icon('test', ['style' => FA::SOLID]);
        $this->assertEquals('<i class="fas fa-test"></i>', $output);
    }

    public function testRenderRegular(): void
    {
        $output = FA::icon('test', ['style' => FA::REGULAR]);
        $this->assertEquals('<i class="far fa-test"></i>', $output);
    }

    public function testRenderBrand(): void
    {
        $output = FA::icon('test', ['style' => FA::BRANDS]);
        $this->assertEquals('<i class="fab fa-test"></i>', $output);
    }
}
