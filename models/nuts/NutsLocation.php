<?php

namespace humanized\location\models\nuts;

use Yii;

use humanized\location\models\location\Country;
use humanized\location\models\location\Location;

/**
 * This is the model class for table "nuts_location".
 *
 * @property integer $id
 * @property string $nuts_code_id
 * @property string $postcode
 * @property string $country_id
 * @property integer $location_id
 *
 * @property Country $country
 * @property NutsCode $nutsCode
 * @property Location $location
 */
class NutsLocation extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nuts_location';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nuts_code_id', 'postcode', 'country_id'], 'required'],
            [['location_id'], 'integer'],
            [['nuts_code_id', 'postcode'], 'string', 'max' => 20],
            [['country_id'], 'string', 'max' => 2],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'iso_2']],
            [['nuts_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => NutsCode::className(), 'targetAttribute' => ['nuts_code_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nuts_code_id' => 'Nuts Code ID',
            'postcode' => 'Postcode',
            'country_id' => 'Country ID',
            'location_id' => 'Location ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['iso_2' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNutsCode()
    {
        return $this->hasOne(NutsCode::className(), ['id' => 'nuts_code_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

}
