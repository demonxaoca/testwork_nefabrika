<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hotels".
 *
 * @property int $id
 * @property int|null $vendor_id
 * @property string $name
 * @property string|null $address
 */
class Hotel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hotels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vendor_id'], 'default', 'value' => null],
            [['vendor_id'], 'integer'],
            [['name'], 'required'],
            [['name', 'address'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'address' => 'Address',
        ];
    }
}
