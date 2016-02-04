<?php

namespace humanized\location\models\location;

use humanized\location\models\location\Country;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CountrySearch represents the model behind the search form about `\humanized\location\models\location\Country`.
 */
class CountrySearch extends Country {

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //          [['id', 'city_id'], 'integer'],
            [['code', 'official_name', 'common_name', 'has_postcodes'], 'safe'],
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
        $query = $this->_query();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'code', 'common_name', 'official_name', 'has_postcodes'
                ],
            ]
        ]);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            //          $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['iso_2' => $this->code]);
        $query->andFilterWhere(['default_label.common_name' => $this->common_name]);
        $query->andFilterWhere(['default_label.official_name' => $this->official_name]);
        return $dataProvider;
    }

}
