<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m181219_202100_off
 */
class m181219_202100_off extends Migration
{
    public function up()
    {
        $this->addColumn('{{%off}}', 'note', $this->text());
    }

    public function down()
    {
        $this->dropColumn('{{%off}}', 'note');
    }
}
