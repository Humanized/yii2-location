<?php

namespace humanized\contact\components;

use humanized\contact\components\DataHelper;

/**
 * A collection of static helper functions to implement the user management 
 */
class GUIHelper {

    public static function getCountryList(array $params = [])
    {
        return \yii\helpers\ArrayHelper::map(DataHelper::getCountryList($params), 'iso_2', 'name');
    }

    public static function getCityList(array $params=[])
    {
        return \yii\helpers\ArrayHelper::map(DataHelper::getCityList($params), 'id', 'name');
    }

    public static function getMenuItems()
    {
        $output = [];

        $output[] = ['label' => 'Locations', 'url' => ['/contact/location/index']];
        $output[] = ['label' => 'Countries', 'url' => ['/contact/country/index']];
        //     if (NULL !== \Yii::$app->user->getId()) {
        //  $output[] = ['label' => 'My Profile', 'url' => ['/user/account', 'id' => \Yii::$app->user->getId()]];
        //$output[] = ['label' => 'Account Settings', 'url' => ['/user/admin/settings', 'id' => \Yii::$app->user->getId()]];
        //$output[] = ['label' => 'Generate Token', 'url' => ['/user/admin/request-token', 'id' => \Yii::$app->user->getId()]];
        //     }
        return $output;
    }

}
