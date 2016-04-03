<?php

namespace humanized\location\models\location;

use humanized\translation\models\Translation;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use humanized\location\components\Viajero;

/**
 * This is the model class for table "location".
 *
 * @property integer $id
 * @property string $uid
 * @property string $postcode
 * @property string $country_id
 * @property integer $city_id
 *
 * @property City $city
 * @property Country $country
 * @property NutsLocation[] $nutsLocations
 * @property Nuts[] $nuts
 */
class Location extends \yii\db\ActiveRecord
{

    public $remoteSettings = [];
    public $name;
    public $label;
    public $language;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location';
    }

    public function fields()
    {
        return [
            // field name is the same as the attribute name
            'id','uid', 'country' => 'country_id', 'name', 'postcode', 'place' => 'city'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'country_id', 'city_id'], 'required'],
            [['city_id'], 'integer'],
            [['uid'], 'string', 'max' => 23],
            [['postcode'], 'string', 'max' => 20],
            [['country_id'], 'string', 'max' => 2],
            [['city_id'], 'exist', 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['country_id'], 'exist', 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'iso_2']],
            [['postcode', 'country_id', 'city_id'], 'unique', 'targetAttribute' => ['postcode', 'country_id', 'city_id']],
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

        /*
          $language = Translation::current();
          $exp = new Expression("'$language'");
          $query->leftJoin('city', '`location`.`city_id` = `city`.`id`');
          $query->leftJoin('city_translation default_label', '(`city`.`id` = `default_label`.`city_id` AND `city`.`language_id` = `default_label`.`language_id`)');
          $query->leftJoin('city_translation localised_label', "(`city`.`id`=`localised_label`.`city_id` AND $exp =`localised_label`.`language_id`)");

          $query->select = [
          'id' => 'location.id',
          'name' => 'IF(localised_label.name IS NULL, default_label.name, localised_label.name)',
          'label' => 'CONCAT(IF(localised_label.name IS NULL, default_label.name, localised_label.name), \' (\',postcode,\')\')',
          'postcode' => 'location.postcode',
          'language' => 'city.language_id'
          ];
         *
         */

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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNutsLocations()
    {
        return $this->hasMany(NutsLocation::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNuts()
    {
        return $this->hasMany(Nuts::className(), ['code' => 'nuts_id'])->viaTable('nuts_location', ['location_id' => 'id']);
    }

    public function beforeValidate()
    {

        if (!parent::beforeValidate()) {
            return false;
        }
        //Remote Settings Empty --> Master Mode 
        if (!isset($this->uid)) {
            //echo "Generating UID";
            $this->uid = uniqid();
        }
        if (isset($this->cityName) && isset($this->cityLanguage)) {
            $params = [
                'name' => $this->cityName,
                'language_id' => strtoupper($this->cityLanguage),
            ];
            $model = City::findOne($params);
            if (!isset($model)) {
                $model = new City($params);
                $model->save();
            }
            $this->city_id = $model->id;
        }
        return true;
    }

    public static function testRemote()
    {
        
    }

    public static function findRemote($uid)
    {
        $model = self::findOne(['uid' => $uid]);
        if (!isset($model)) {
            $model = new Location(['uid' => $uid]);
            $model->syncRemote();
        }
        return $model;
    }

    public function syncRemote()
    {
        //Make Connection - Ensure that Connection Parameters exist
        if (!isset($this->uid)) {
            throw new \yii\base\InvalidConfigException("Model UID must be set for remote synchronisation");
        }

        $raw = (new Viajero())->get('places', [
                    'query' =>
                    [
                        'uid' => $this->uid
                    ]
                ])->getBody();

        $formatted = Json::decode($raw, true);
        \yii\helpers\VarDumper::dump($formatted);
        if (count($formatted == 1)) {

            $data = $formatted[0];
            if (!isset($data['place']) || !isset($data['place']['uid'])) {
                throw new \UnexpectedValueException('Location: syncRemote - Unexpected Data format');
            }

            $model = City::findRemote($data['place']['uid']);
            if (isset($model)) {
                $this->city_id = $model->id;
                $this->country_id = $data['country'];
                $this->language_id = $data['language'];
                $this->save();
            }
        }
    }

}
