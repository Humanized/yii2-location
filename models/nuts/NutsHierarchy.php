<?php

namespace humanized\location\models\nuts;

use humanized\location\models\location\Country;
use Yii;

/**
 * This is the model class for table "nuts_hierarchy".
 *
 * @property string $parent_id
 * @property string $child_id
 * @property integer $is_offspring
 * @property integer $depth
 *
 * @property NutsCode $child
 * @property NutsCode $parent
 */
class NutsHierarchy extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nuts_hierarchy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'child_id', 'is_offspring', 'depth'], 'required'],
            [['is_offspring', 'depth'], 'integer'],
            [['parent_id', 'child_id'], 'string', 'max' => 20],
            [['child_id'], 'exist', 'skipOnError' => true, 'targetClass' => NutsCode::className(), 'targetAttribute' => ['child_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => NutsCode::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parent_id' => 'Parent ID',
            'child_id' => 'Child ID',
            'is_offspring' => 'Is Offspring',
            'depth' => 'Depth',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChild()
    {
        return $this->hasOne(NutsCode::className(), ['id' => 'child_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(NutsCode::className(), ['id' => 'parent_id']);
    }

}
