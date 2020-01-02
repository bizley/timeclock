<?php

declare(strict_types=1);

use app\models\Off;
use yii\db\Migration;

/**
 * Class m190415_094300_off
 */
class m190415_094300_off extends Migration
{
    public function up()
    {
        $this->db->createCommand()->truncateTable('{{%off}}')->execute();

        $this->alterColumn('{{%off}}', 'start_at', $this->date());
        $this->alterColumn('{{%off}}', 'end_at', $this->date());
        $this->addColumn('{{%off}}', 'type', $this->smallInteger()->defaultValue(Off::TYPE_SHORT));
        $this->addColumn('{{%off}}', 'approved', $this->tinyInteger(1)->defaultValue(0));
    }

    public function down()
    {
        return false;
    }
}
