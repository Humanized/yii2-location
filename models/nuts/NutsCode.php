<?php

namespace humanized\location\models\nuts;

use Yii;

/**
 * This is the model class for table "nuts_code".
 *
 * @property string $id
 * @property string $name
 * @property string $country_id
 *
 * @property Country $country
 * @property NutsHierarchy[] $nutsHierarchies
 * @property NutsHierarchy[] $nutsHierarchies0
 * @property NutsCode[] $parents
 * @property NutsCode[] $children
 * @property NutsLocation[] $nutsLocations
 */
class NutsCode extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nuts_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 255],
            [['country_id'], 'string', 'max' => 2],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'iso_2']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'country_id' => 'Country ID',
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
    public function getNutsHierarchies()
    {
        return $this->hasMany(NutsHierarchy::className(), ['child_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNutsHierarchies0()
    {
        return $this->hasMany(NutsHierarchy::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(NutsCode::className(), ['id' => 'parent_id'])->viaTable('nuts_hierarchy', ['child_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(NutsCode::className(), ['id' => 'child_id'])->viaTable('nuts_hierarchy', ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNutsLocations()
    {
        return $this->hasMany(NutsLocation::className(), ['nuts_code_id' => 'id']);
    }

}
