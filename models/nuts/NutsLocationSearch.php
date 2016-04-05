<?php

namespace humanized\location\models\nuts;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use humanized\location\models\nuts\NutsLocation;

/**
 * CitySearch represents the model behind the search form about `\humanized\location\models\location\City`.
 */
class NutsLocationSearch extends NutsLocation
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
            [['postcode', 'country_id'], 'string'],
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
        $query = NutsLocation::find();

        $query->where(['country_id'=>$this->country_id,'postcode'=>$this->postcode,]);

        $this->pageSize = 1;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ($this->pagination ? [
                'pageSize' => $this->pageSize,
                    ] : FALSE),
            'sort' => [
                'attributes' => [
                    'postcode'
                ],
            ]
        ]);



        return $dataProvider;
    }

}
