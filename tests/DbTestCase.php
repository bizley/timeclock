<?php

declare(strict_types=1);

namespace tests;

use app\base\Alert;
use app\models\User;
use yii\db\Connection;
use yii\db\Exception;

/**
 * Class CommonDbTestCase
 * @package fronttests
 */
abstract class DbTestCase extends AppTestCase
{
    /**
     * @var Connection
     */
    protected static $db;

    /**
     * @var array [table => [row1 columns => values], [row2 columns => values], ...]
     */
    public $fixtures = [];

    /**
     * @throws Exception
     */
    public function fixturesUp(): void
    {
        foreach ($this->fixtures as $table => $data) {
            foreach ($data as $row) {
                static::$db->createCommand()->insert($table, $row)->execute();
            }
        }
    }

    /**
     * @throws Exception
     */
    public function fixturesDown(): void
    {
        static::$db->createCommand('PRAGMA foreign_keys = OFF;')->execute();
        foreach ($this->fixtures as $table => $data) {
            static::$db->createCommand()->truncateTable($table)->execute();
        }
        static::$db->createCommand('PRAGMA foreign_keys = ON;')->execute();
    }

    /**
     * @return Connection
     * @throws Exception
     */
    public static function getConnection(): Connection
    {
        if (static::$db === null) {
            $db = new Connection();
            $db->dsn = 'sqlite::memory:';

            if (!$db->isActive) {
                $db->open();
            }

            static::$db = $db;
        }

        return static::$db;
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->fixturesUp();
    }

    /**
     * @throws Exception
     */
    protected function tearDown(): void
    {
        $this->fixturesDown();
    }

    /**
     * @return array additional mocked app config
     * @throws \yii\db\Exception
     */
    public static function config(): array
    {
        return [
            'components' => [
                'db' => static::getConnection(),
                'alert' => Alert::class,
                'user' => [
                    'identityClass' => User::class,
                ],
            ],
        ];
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $lines = explode(';', file_get_contents(__DIR__ . '/structure.sql'));

        if (!static::$db->isActive) {
            static::$db->open();
        }

        foreach ($lines as $line) {
            if (trim($line) !== '') {
                static::$db->pdo->exec($line);
            }
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (static::$db) {
            static::$db->close();
        }

        parent::tearDownAfterClass();
    }
}
