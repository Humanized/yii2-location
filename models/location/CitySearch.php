<?php

namespace humanized\location\models\location;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use humanized\location\models\location\City;

/**
 * CitySearch represents the model behind the search form about `\humanized\location\models\location\City`.
 */
class CitySearch extends City
{

    public $pagination = FALSE;
    public $pageSize = 25;
    public $q = '';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid'], 'string'],
            [['uid', 'name', 'postcode', 'country_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $query = City::find();
        $query->select(['*', 'name' => 'city_translation.name', 'language' => 'city.language_id']);

        $query->leftJoin('city_translation', 'city_translation.city_id=city.id');
        //$query->andWhere(['city.language_id' => 'city_translation.language_id']);
        //   $query->joinWith('city');
        //    $query->joinWith('city.localisedIdentification');

        if (isset($this->uid)) {
            $this->pageSize = 1;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ($this->pagination ? [
                'pageSize' => $this->pageSize,
                    ] : FALSE),
            'sort' => [
                'attributes' => [
                    'name', 'postcode', 'language'
                ],
            ]
        ]);
        if (isset($this->uid)) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where(['uid' => $this->uid]);
            return $dataProvider;
        }
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }




        return $dataProvider;
    }

}
