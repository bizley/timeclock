<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m190319_145100_user
 */
class m190319_145100_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'project_id', $this->integer());
        $this->addForeignKey('fk-user-project_id', '{{%user}}', 'project_id', '{{%project}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'project_id');
    }
}
