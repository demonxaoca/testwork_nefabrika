<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rooms_prices".
 *
 * @property int $id
 * @property int|null $room_id
 * @property int|null $tariff_id
 * @property string|null $date_from
 * @property string|null $date_to
 * @property int|null $guest_count
 * @property int|null $value
 */
class RoomPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rooms_prices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['room_id', 'tariff_id', 'guest_count', 'value'], 'default', 'value' => null],
            [['room_id', 'tariff_id', 'guest_count', 'value'], 'integer'],
            [['date_from', 'date_to'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_id' => 'Room ID',
            'tariff_id' => 'Tariff ID',
            'date_from' => 'Date From',
            'date_to' => 'Date To',
            'guest_count' => 'Guest Count',
            'value' => 'Value',
        ];
    }
}
