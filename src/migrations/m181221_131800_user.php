<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Class m181221_131800_user
 */
class m181221_131800_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'api_key', $this->string(20)->unique());
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'api_key');
    }
}
