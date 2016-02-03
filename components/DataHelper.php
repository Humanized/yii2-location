<?php

namespace humanized\location\components;

use humanized\location\models\location\Country;
use humanized\location\models\location\City;
use humanized\location\models\location\Location;

/**
 * A collection of static helper functions to implement the user management 
 */
class DataHelper {

    public static function getCountryList($params)
    {
        return Country::find()->asArray()->all();
    }

    public static function getCityList($params)
    {
        return City::find()->asArray()->all();
    }

}
