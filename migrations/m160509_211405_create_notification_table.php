<?php

use yii\db\Migration;

class m160509_211405_create_notification_table extends Migration
{
    public function up()
    {
        $this->createTable('notification', [
            'id' => $this->primaryKey(),
            'key' => $this->string()->notNull(),
            'key_id' => $this->integer(),
            'type' => $this->string()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'seen' => $this->boolean()->notNull(),
            'flashed' => $this->boolean()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('notification');
    }
}
