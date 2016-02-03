<?php

namespace humanized\location\models\location;

use humanized\translation\models\Language;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

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
        $searchModel = (new Country())->_query();

        return $searchModel->asArray()->all();
    }

    public static function dropdown()
    {
        $data = self::available();
        return ArrayHelper::map($data, 'code', 'label');
    }

    public static function enabled()
    {
        
    }

    protected function _query()
    {
        $query = Country::find();
        $currentLanguage = substr(Language::current(), 0, 2);
        $local = new Expression("'$currentLanguage'");
        $fallbackLanguage = substr(Language::fallback(), 0, 2);
        $fallback = new Expression("'$fallbackLanguage'");

        $query->leftJoin('country_translation default_label', "(`country`.`iso_2`=`default_label`.`country_id` AND $fallback=`default_label`.`language_id`)");
        $query->leftJoin('country_translation localised_label', "(`country`.`iso_2`=`localised_label`.`country_id` AND $local =`localised_label`.`language_id`)");
        $query->select = [
            'code' => 'iso_2',
            'label'=>'CONCAT(IF(localised_label.common_name IS NULL, default_label.common_name,localised_label.common_name),\' (\',iso_2,\')\')',
            'has_postcodes' => 'has_postcodes',
            'common_name' => 'IF(localised_label.common_name IS NULL, default_label.common_name,localised_label.common_name)',
            'official_name' => 'IF(localised_label.official_name IS NULL, default_label.official_name,localised_label.official_name)',
            'common' => 'IF(localised_label.common_name IS NULL, default_label.common_name,localised_label.common_name)',
            'official' => 'IF(localised_label.official_name IS NULL, default_label.official_name,localised_label.official_name)',
        ];
        $query->groupBy('code');
        return $query;
    }

}
