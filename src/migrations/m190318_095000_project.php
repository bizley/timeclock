<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m190318_095000_project
 */
class m190318_095000_project extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%project}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique(),
            'color' => $this->string(10)->notNull(),
            'assignees' => $this->json(),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex('idx-project-name', '{{%project}}', ['name']);
    }

    public function down()
    {
        $this->dropTable('{{%project}}');
    }
}
