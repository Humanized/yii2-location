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

    public $pagination = FALSE;
    public $pageSize = 25;
    public $q = '';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id'], 'integer'],
            [['uid'], 'string'],
            [['q', 'uid', 'name', 'postcode', 'country_id'], 'safe'],
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
        $query = Location::find();

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
            $query->where(['location.uid' => $this->uid]);
            return $dataProvider;
        }
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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
