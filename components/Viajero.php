<?php

namespace humanized\location\components;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Viajero extends \GuzzleHttp\Client
{

    public function __construct(array $config = array())
    {
        $config = \Yii::$app->params['viajero'];
        if (!isset($config)) {
            throw new \yii\base\InvalidConfigException("Accessible params array missing index viajero");
        }
        if (!isset($config['remoteUri'])) {
            throw new \yii\base\InvalidConfigException("Viajero remote configuration missing the remoteUri parameter");
        }
        if (!isset($config['remoteAccessToken'])) {
            throw new \yii\base\InvalidConfigException("Viajero remote configuration missing the remoteAccessToken parameter");
        }
        parent::__construct([
            // Base URI is used with relative requests
            'base_uri' => $config['remoteUri'],
            'auth' => [$config['remoteAccessToken'], ''],
        ]);
    }

 
}
