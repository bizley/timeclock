<?php

use yii\db\Migration;

/**
 * Class m201023_204816_terminal
 */
class m201023_204816_terminal extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%terminal}}', [
            'id' => $this->primaryKey(),
            'api_key' => $this->string(20)->unique(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%terminal}}');
    }
}
