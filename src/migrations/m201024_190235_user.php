<?php

use yii\db\Migration;

/**
 * Class m201024_190235_user
 */
class m201024_190235_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'tag', $this->string());
        $this->addColumn('{{%user}}', 'image', $this->string());
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'tag');
        $this->dropColumn('{{%user}}', 'image');
    }
}
