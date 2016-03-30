<?php

namespace humanized\location\models\location;

use humanized\location\models\translation\CityTranslation;
use humanized\translation\models\Language;
use humanized\location\components\Viajero;
use yii\helpers\Json;
use Yii;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property string $uid
 * @property string $language_id
 *
 * @property Language $language
 * @property CityTranslation[] $cityTranslations
 * @property Language[] $languages
 * @property Location[] $locations
 */
class City extends \yii\db\ActiveRecord
{

    public $local_name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_id', 'uid'], 'required'],
            [['uid'], 'string', 'max' => 23],
            [['language_id'], 'string', 'max' => 5],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'code']],
            [['uid'], 'unique', 'targetAttribute' => ['uid']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', 'Uid'),
            'language_id' => Yii::t('app', 'Language ID'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {

        echo 'TRUE';
        $search = ['language_id' => $this->language_id, 'city_id' => $this->id];
        $model = CityTranslation::findOne($search);
        if (!isset($model)) {
            $model = new CityTranslation($search);
        }
        //Sync from Master
        $model->name = $this->local_name;

        echo 'ASSHOLE';
        if (!$model->save()) {
            echo 'yaay';
            var_dump($model->errors);
            $this->delete();
            return false;
        }
        return parent::afterSave($insert, $search);
    }

    public static function findRemote($uid)
    {
        $model = self::findOne(['uid' => $uid]);
        if (!isset($model)) {
            $model = new City(['uid' => $uid]);
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

        $raw = (new Viajero())->get('cities', [
                    'query' =>
                    [
                        'uid' => $this->uid
                    ]
                ])->getBody();

        $formatted = Json::decode($raw, true);
        if (count($formatted == 1)) {
            $data = $formatted[0];
            $this->local_name = $data['name'];
            $this->language_id = $data['language'];
            if (!$this->save()) {
                \yii\helpers\VarDumper::dump($this->errors);
            }
        }
    }

    public function fields()
    {
        return [
// field name is the same as the attribute name
            'uid', 'language' => 'language_id', 'name' => function($model) {
                $localTranslation = CityTranslation::find()->joinWith('city')->where(['uid' => $model->uid, 'city_translation.language_id' => $model->language_id])->one();
                return $localTranslation->name;
            }
                ];
            }

            public function extraFields()
            {
                return ['translations' => 'cityTranslations'];
            }

            /**
             * @return \yii\db\ActiveQuery
             */
            public function getLanguage()
            {
                return $this->hasOne(Language::className(), ['code' => 'language_id']);
            }

            /**
             * @return \yii\db\ActiveQuery
             */
            public function getCityTranslations()
            {
                return $this->hasMany(CityTranslation::className(), ['city_id' => 'id']);
            }

            /**
             * @return \yii\db\ActiveQuery
             */
            public function getName()
            {
                return $this->hasOne(CityTranslation::className(), ['city_id' => 'id']);
            }

            /**
             * @return \yii\db\ActiveQuery
             */
            public function getLocalisedIdentification()
            {
                return $this->hasOne(CityTranslation::className(), ['city_id' => 'id'])->onCondition(['`city_translation`.`language_id`' => '`city`.`language_id`']);
            }

            /**
             * @return \yii\db\ActiveQuery
             */
            public function getLanguages()
            {
                return $this->hasMany(Language::className(), ['code' => 'language_id'])->viaTable('city_translation', ['city_id' => 'id']);
            }

            /**
             * @return \yii\db\ActiveQuery
             */
            public function getLocations()
            {
                return $this->hasMany(Location::className(), ['city_id' => 'id']);
            }

            public function beforeValidate()
            {
                if (!parent::beforeValidate()) {
                    return false;
                }

                //Remote Settings Empty --> Master Mode 
                if ($this->isNewRecord && !isset($this->uid)) {
                    $this->uid = uniqid();
                }
                return true;
            }

        }
        