<?php

namespace humanized\location\models\location;

use yii\base\Model;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use humanized\location\models\location\Country;
use humanized\translation\models\Language;

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
        $query = Country::find();
        $currentLanguage = substr(Language::current(), 0, 2);
        $local = new Expression("'$currentLanguage'");
        $fallbackLanguage = substr(Language::fallback(), 0, 2);
        $fallback = new Expression("'$fallbackLanguage'");


        $query->leftJoin('country_translation default_label', "(`country`.`iso_2`=`default_label`.`country_id` AND $fallback=`default_label`.`language_id`)");
        $query->leftJoin('country_translation localised_label', "(`country`.`iso_2`=`localised_label`.`country_id` AND $local =`localised_label`.`language_id`)");
        $query->select = [
            'code' => 'iso_2',
            'has_postcodes' => 'has_postcodes',
            'common_name' => 'IF(localised_label.common_name IS NULL, default_label.common_name,localised_label.common_name)',
            'official_name' => 'IF(localised_label.official_name IS NULL, default_label.official_name,localised_label.official_name)',
            'common' => 'IF(localised_label.common_name IS NULL, default_label.common_name,localised_label.common_name)',
            'official' => 'IF(localised_label.official_name IS NULL, default_label.official_name,localised_label.official_name)',
        ];
        $query->groupBy('code');




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

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['iso_2' => $this->code]);
        

        return $dataProvider;
    }

}
