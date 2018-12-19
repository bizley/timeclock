<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m181219_190600_user
 */
class m181219_190600_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'theme', $this->string(20)->notNull()->defaultValue('light'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'theme');
    }
}
