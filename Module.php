<?php

namespace humanized\location;

/**
 * Location Management Module for Yii2 - By Humanized
 * This module wraps several mechisms employed in-house dealing with routine tasks related to location management.
 * Additionally it is built to automate as much as possible, while remaining clean and modular.
 * 
 * 
 * @name Yii2 Contact Management Module Class 
 * @version 0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 */
class Module extends \yii\base\Module {

    public $tablePrefix = NULL;
    public $enableTranslations = FALSE;
    public $locationOptions = [
        'enableTranslations' => FALSE,
        'gridOptions' => [
            'enablePjax' => TRUE,
            'enablePostCodes' => TRUE,
        ],
        'api' => [
            'url' => NULL,
            'username' => NULL,
            'token' => NULL
        ]
    ];

    public function init()
    {
        parent::init();
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'humanized\location\commands';
        }
        $this->params['tablePrefix'] = $this->tablePrefix;
    }

}
