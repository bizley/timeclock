<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m181218_181900_holiday
 */
class m181218_181900_holiday extends Migration
{
    public function up()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%holiday}}', [
            'year' => $this->smallInteger(6)->notNull(),
            'month' => $this->smallInteger(6)->notNull(),
            'day' => $this->smallInteger(6)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-holiday', '{{%holiday}}', ['year', 'month', 'day']);
    }

    public function down()
    {
        $this->dropTable('{{%holiday}}');
    }
}
