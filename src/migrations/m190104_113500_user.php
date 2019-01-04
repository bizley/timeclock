<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m190104_113500_user
 */
class m190104_113500_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'phone', $this->string());
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'phone');
    }
}
