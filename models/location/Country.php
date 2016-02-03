<?php

namespace humanized\location\models\location;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property string $iso_2
 * @property string $iso_3
 * @property integer $iso_numerical
 * @property Location[] $locations
 */
class Country extends \yii\db\ActiveRecord {

    public $label;
    public $code;
    public $common_name;
    public $official_name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iso_2', 'iso_3', 'iso_numerical', 'has_postcodes'], 'required'],
            [['iso_numerical'], 'integer'],
            [['has_postcodes'], 'integer', 'max' => 1],
            [['iso_2'], 'string', 'max' => 2],
            [['iso_3'], 'string', 'max' => 3],
                //[['name', 'adjectival', 'demonym'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iso_2' => 'Iso 2',
            'iso_3' => 'Iso 3',
            'iso_numerical' => 'Iso Numerical',
            'name' => 'Country',
            'adjectival' => 'Adjectival',
            'demonym' => 'Demonym',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['country_id' => 'iso_2']);
    }

    public static function available()
    {
        return self::find()->all();
    }

    public static function dropdown()
    {
        
    }

    public static function enabled()
    {
        
    }

}
