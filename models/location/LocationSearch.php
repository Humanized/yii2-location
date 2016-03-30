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
        $query = Location::find();
        $query->select(['*', 'uid' => 'location.uid', 'name' => 'city_translation.name', 'language' => 'city.language_id']);
        $query->innerJoin('city', 'location.city_id=city.id');
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
            $query->where(['location.uid' => $this->uid]);
            return $dataProvider;
        }
        if (!$this->validate() || !isset($this->country_id)) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere(['country_id' => $this->country_id]);
        $query->andFilterWhere(
                ['NOT IN', 'postcode', ['-1', '0']]
        );
        $query->andFilterWhere([
            'or',
            ['like', 'postcode', $this->q],
            ['like', 'city_translation.name', $this->q],
        ]);



        return $dataProvider;
    }

}
