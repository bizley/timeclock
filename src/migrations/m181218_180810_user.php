<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m181218_180810_user
 */
class m181218_180810_user extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255)->notNull()->unique(),
            'name' => $this->string(255)->notNull(),
            'auth_key' => $this->string(45)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(45)->unique(),
            'role' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex('idx-user-name', '{{%user}}', ['name']);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
