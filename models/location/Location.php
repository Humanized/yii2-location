<?php

namespace humanized\location\models\location;

use humanized\translation\models\Language;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "location".
 *
 * @property integer $id
 * @property string $postcode
 * @property string $country_id
 * @property integer $city_id
 *
 * @property City $city
 * @property Country $country
 */
class Location extends \yii\db\ActiveRecord {

    public $label;
    public $language;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'city_id'], 'required'],
            [['city_id'], 'integer'],
            [['postcode'], 'string', 'max' => 20],
            [['country_id'], 'string', 'max' => 2],
            [['city_id'], 'exist', 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['country_id'], 'exist', 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'iso_2']],
            [['postcode', 'country_id', 'city_id'], 'unique', 'targetAttribute' => ['postcode', 'country_id', 'city_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'postcode' => 'Postcode',
            'country_id' => 'Country ID',
            'city_id' => 'City',
            'city' => 'City',
        ];
    }

    public static function all($country_id)
    {
        $searchModel = (new Location(['country_id' => $country_id]))->_queryCountry();
        return $searchModel->asArray()->all();
    }

    public static function dropdown($country_id)
    {
        $data = self::all($country_id);
        return ArrayHelper::map($data, 'id', 'label');
    }

    /**
     * 
     */
    protected function _queryCountry()
    {
        $query = Location::find();
        $language = Language::current();
        $exp = new Expression("'$language'");
        $query->leftJoin('city', '`location`.`city_id`=`city`.`id`');
        $query->leftJoin('city_translation default_label', '(`city`.`id`=`default_label`.`city_id` AND `city`.`language_id`=`default_label`.`language_id`)');
        $query->leftJoin('city_translation localised_label', "(`city`.`id`=`localised_label`.`city_id` AND $exp =`localised_label`.`language_id`)");

        $seperator = new Expression("' - '");
        $query->select = [
            'id' => 'location.id',
            'label' => 'CONCAT(IF(localised_label.name IS NULL, default_label.name, localised_label.name),\' (\',postcode,\')\')',
            'postcode' => 'location.postcode',
            'language' => 'city.language_id'
        ];

        $query->andWhere(['country_id' => $this->country_id]);
        return $query;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['iso_2' => 'country_id']);
    }

    public function beforeValidate()
    {

        if (isset($this->cityName) && isset($this->cityLanguage)) {

            $params = [
                'name' => $this->cityName,
                'language_id

        

         ' => strtoupper($this->cityLanguage),
            ];
            $model = City::findOne($params);
            if (!isset($model)) {
                $model = new City($params);
                $model->save();
            }
            $this->city_id = $model->id;
        }

        return parent::beforeValidate();
    }

    public function save($runValidation = true, $attributeNames = NULL)
    {

        return parent::save();
    }

}
