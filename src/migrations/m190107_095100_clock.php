<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m190107_095100_clock
 */
class m190107_095100_clock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%clock}}', 'note', $this->text());
    }

    public function down()
    {
        $this->dropColumn('{{%clock}}', 'note');
    }
}
