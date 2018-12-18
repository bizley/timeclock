<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m181218_182200_off
 */
class m181218_182200_off extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%off}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'start_at' => $this->integer(11)->notNull(),
            'end_at' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex('idx-off-start_at', '{{%off}}', ['start_at']);
        $this->createIndex('idx-off-end_at', '{{%off}}', ['end_at']);

        $this->addForeignKey('fk-off-user', '{{%off}}', ['user_id'], '{{%user}}', ['id'], null, 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%off}}');
    }
}
