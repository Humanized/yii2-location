<?php

namespace humanized\location\components;

use humanized\location\models\location\Country;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * LanguagePicker Widget for Yii2 - By Humanized
 * 
 * This widget allows the user to set the application using the configuration provided by the Module. 
 * 
 * @name Yii2 LanguagePicker Widget Class
 * @version 0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-translation
 */
class LocationSelector extends Widget
{

    public $form = NULL;
    public $model = NULL;
    public $enableBootstrap = FALSE;
    public $countryTemplate = '{flag} {label} ({code})';
    public $cityTemplate = '{}{}';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $out = '';
        $countryDropdown = Select2::widget([
                    'name' => 'country',
                    'value' => '',
                    'data' => Country::dropdown(),
                    'options' => [ 'label' => 'yaya', 'placeholder' => 'Select Country ...', 'id' => 'country-selection']
        ]);

        $module = \humanized\location\Module::getInstance();
        \yii\helpers\VarDumper::dump($module);
        $locationDropdown = $this->form->field($this->model, 'location_id')->label(false)->widget(DepDrop::classname(), [
            'type' => DepDrop::TYPE_SELECT2, //For Select2
            'options' => ['id' => 'location-selection'],
            'select2Options' => [
                'pluginOptions' => [
                    'allowClear' => TRUE,
                    'minimumInputLength' => 3,
                ]
            ],
            'pluginOptions' => [
                'depends' => ['country-selection'],
                'placeholder' => 'Select Location',
                'url' => Url::to(['/location/admin/load'])
            ]
        ]);

        $this->_printField($countryDropdown, $out);
        $this->_printField($locationDropdown, $out);
        return $out;
    }

    private function _printField($field, &$out)
    {
        $out .= $this->enableBootstrap ? '<div class="col-md-6">' : '';
        $out .= $field;
        $out .= $this->enableBootstrap ? '</div>' : '';
    }

}
