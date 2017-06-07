<?php

use yii\db\Migration;

class m160509_211405_add_flashed_to_notification extends Migration
{
    public function up()
    {
        $this->addColumn('{{%notification}}', 'flashed', $this->boolean()->notNull());
    }

    public function down()
    {
        $this->dropColumn('{{%notification}}', 'flashed');
    }
}
