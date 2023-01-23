<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rooms_avl".
 *
 * @property int $id
 * @property int|null $room_id
 * @property string|null $date_from
 * @property string|null $date_to
 * @property int|null $count
 */
class RoomAvl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rooms_avl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['room_id', 'count'], 'default', 'value' => null],
            [['room_id', 'count'], 'integer'],
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
            'date_from' => 'Date From',
            'date_to' => 'Date To',
            'count' => 'Count',
        ];
    }
}
