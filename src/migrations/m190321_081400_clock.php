<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m190321_081400_clock
 */
class m190321_081400_clock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%clock}}', 'project_id', $this->integer());
        $this->addForeignKey('fk-clock-project_id', '{{%clock}}', 'project_id', '{{%project}}', 'id', 'NO ACTION', 'CASCADE');
    }

    public function down()
    {
        $this->dropColumn('{{%clock}}', 'project_id');
    }
}
