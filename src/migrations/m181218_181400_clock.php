<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m181218_181400_clock
 */
class m181218_181400_clock extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%clock}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'clock_in' => $this->integer(11)->notNull(),
            'clock_out' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex('idx-clock-clock_in', '{{%clock}}', ['clock_in']);
        $this->createIndex('idx-clock-clock_out', '{{%clock}}', ['clock_out']);

        $this->addForeignKey('fk-clock-user', '{{%clock}}', ['user_id'], '{{%user}}', ['id'], null, 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%clock}}');
    }
}
