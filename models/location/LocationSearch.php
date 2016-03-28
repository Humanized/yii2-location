<?php

namespace humanized\location\models\location;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use humanized\location\models\location\Location;

/**
 * LocationSearch represents the model behind the search form about `\humanized\location\models\location\Location`.
 */
class LocationSearch extends Location
{

    public $q = '';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id'], 'integer'],
            [['q', 'name', 'postcode', 'country_id'], 'safe'],
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
        $query = $this->_queryCountry();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'pagination' => false,
            'sort' => [
                'attributes' => [
                    'name', 'postcode', 'language'
                ],
            ]
        ]);



        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            //    $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(
                ['NOT IN', 'postcode', ['-1', '0']]
        );


        $query->andFilterWhere([
            'or',
            ['like', 'postcode', $this->q],
            ['like', 'localised_label.name', $this->q],
            ['like', 'default_label.name', $this->q],
        ]);



        return $dataProvider;
    }

}
