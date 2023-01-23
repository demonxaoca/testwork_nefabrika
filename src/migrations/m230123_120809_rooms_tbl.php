<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m230123_120809_rooms_tbl
 */
class m230123_120809_rooms_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('rooms', [
            'id' => Schema::TYPE_PK,
            'vendor_id' => Schema::TYPE_INTEGER, 
            'hotel_id'  => Schema::TYPE_INTEGER,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ]);

        $this->createTable('rooms_avl', [
            'id' => Schema::TYPE_PK,
            'room_id' => Schema::TYPE_INTEGER, 
            'date_from' => Schema::TYPE_DATE, 
            'date_to' => Schema::TYPE_DATE,
            'count' => Schema::TYPE_INTEGER,
        ]);

        $this->createTable('rooms_prices', [
            'id' => Schema::TYPE_PK,
            'room_id' => Schema::TYPE_INTEGER, 
            'tariff_id' => Schema::TYPE_INTEGER, 
            'date_from' => Schema::TYPE_DATE,
            'date_to' => Schema::TYPE_DATE,
            'guest_count' => Schema::TYPE_INTEGER,
            'value' => Schema::TYPE_INTEGER,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230123_120809_rooms_tbl cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230123_120809_rooms_tbl cannot be reverted.\n";

        return false;
    }
    */
}
