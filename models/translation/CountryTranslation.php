<?php

namespace humanized\location\models\translation;

use humanized\location\models\location\Country;
use humanized\translation\models\Language;
use Yii;

/**
 * This is the model class for table "country_translation".
 *
 * @property integer $id
 * @property string $country_id
 * @property string $language_id
 * @property string $name
 * @property string $adjectival
 * @property string $demonym
 *
 * @property Country $country
 * @property Language $language
 */
class CountryTranslation extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'language_id', 'official_name', 'common_name'], 'required'],
            [['country_id'], 'string', 'max' => 3],
            [['language_id'], 'string', 'max' => 5],
            [['official_name', 'common_name', 'adjectival', 'demonym'], 'string', 'max' => 255],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'iso_2']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'code']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'country_id' => Yii::t('app', 'Country ID'),
            'language_id' => Yii::t('app', 'Language ID'),
            'name' => Yii::t('app', 'Name'),
            'adjectival' => Yii::t('app', 'Adjectival'),
            'demonym' => Yii::t('app', 'Demonym'),
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
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['code' => 'language_id']);
    }

}
