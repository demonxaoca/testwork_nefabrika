<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tariffs".
 *
 * @property int $id
 * @property int|null $vendor_id
 * @property int|null $hotel_id
 * @property string $name
 * @property bool|null $hasBreakfast
 */
class Tariff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariffs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vendor_id', 'hotel_id'], 'default', 'value' => null],
            [['vendor_id', 'hotel_id'], 'integer'],
            [['name'], 'required'],
            [['hasBreakfast'], 'boolean'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vendor_id' => 'Vendor ID',
            'hotel_id' => 'Hotel ID',
            'name' => 'Name',
            'hasBreakfast' => 'Has Breakfast',
        ];
    }
}
