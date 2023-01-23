<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m230123_115909_hotels_tbl
 */
class m230123_115909_hotels_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('hotels', [
            'id' => Schema::TYPE_PK,
            'vendor_id' => Schema::TYPE_INTEGER, 
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'address' => Schema::TYPE_STRING,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230123_115909_hotels_tbl cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230123_115909_hotels_tbl cannot be reverted.\n";

        return false;
    }
    */
}
