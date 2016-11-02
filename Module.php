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
class Module extends \yii\base\Module
{

    const MASTER_MODE = 'master';
    const SLAVE_MODE = 'slave';

    public $mode = NULL;
    public $tablePrefix = NULL;
    public $enableRemote = FALSE;
    public $remoteSettings = ['uri' => NULL, 'token' => NULL];
	public $defaultLanguage = 'en';

    public function init()
    {
        parent::init();
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'humanized\location\commands';
        }
        $this->params['tablePrefix'] = $this->tablePrefix;
        if ($this->enableRemote) {
            $this->_initRemote();
        }
    }

    private function _initRemote()
    {
        if (!isset($this->mode)) {
            $this->mode = self::MASTER_MODE;
        }
        $this->params['mode'] = $this->mode;
        $this->params['enableRemote'] = TRUE;
        if (!empty($this->remoteSettings)) {
            $this->params['remoteSettings'] = $this->remoteSettings;
            return;
        }
    }

}
