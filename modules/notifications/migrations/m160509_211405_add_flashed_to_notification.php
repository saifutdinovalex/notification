<?php

use yii\db\Migration;

class m160509_211405_add_flashed_to_notification extends Migration
{
    const TABLE_NAME = '{{%ManagerNotification}}';

    public function up()
    {
        $this->addColumn(self::TABLE_NAME, 'flashed', $this->boolean()->notNull());
    }

    public function down()
    {
        $this->dropColumn(self::TABLE_NAME, 'flashed');
    }
}
