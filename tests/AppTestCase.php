<?php

declare(strict_types=1);

namespace tests;

use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * Class AppTestCase
 * @package tests
 */
abstract class AppTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    public static $appClass = Application::class;

    /**
     * @return array additional mocked app config
     */
    public static function config(): array
    {
        return [];
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public static function setUpBeforeClass(): void
    {
        new Application(ArrayHelper::merge(
            [
                'id' => 'timeclock-test',
                'aliases' => [
                    '@bower' => '@vendor/bower-asset',
                    '@npm' => '@vendor/npm-asset',
                ],
                'basePath' => __DIR__ . '/../src',
                'runtimePath' => __DIR__ . '/runtime',
                'vendorPath' => __DIR__ . '/../vendor',
                'timezone' => 'UTC',
                'components' => [
                    'assetManager' => [
                        'basePath' => __DIR__ . '/runtime/assets',
                    ],
                    'urlManager' => [
                        'showScriptName' => true,
                    ],
                    'request' => [
                        'enableCookieValidation' => false,
                        'scriptFile' => __DIR__ . '/index.php',
                        'scriptUrl' => '/index.php',
                    ],
                ],
                'params' => [
                    'company' => 'Company Name',
                    'email' => 'email@company.com',
                    'allowedDomains' => ['@company.com', '@company.net'],
                ],
            ],
            static::config()
        ));
    }

    public static function tearDownAfterClass(): void
    {
        \Yii::$app = null;
    }

    /**
     * Asserting two strings equality ignoring line endings.
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    protected function assertEqualsWithoutLineEndings(string $expected, string $actual, string $message = ''): void
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        self::assertEquals($expected, $actual, $message);
    }
}
