<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m190102_090400_user
 */
class m190102_090400_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'pin_hash', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'pin_hash');
    }
}
