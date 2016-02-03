<?php

namespace humanized\contact\components;

use humanized\contact\models\location\Country;
use humanized\contact\models\location\City;
use humanized\contact\models\location\Location;

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
