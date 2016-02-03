<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\typeahead\TypeaheadBasic;

//$form = ActiveForm::begin(); //Default Active Form begin
$form = ActiveForm::begin([
            'id' => 'create-location',
            'options' => [
                'class' => 'form',
                'enctype' => 'multipart/form-data'
            ],
        ]);

/**
 * Select2 for County Selection Dropdown
 */
echo $form->field($model, 'country_id')->widget(Select2::classname(), [
    'data' => \humanized\contact\components\GUIHelper::getCountryList(),
    'options' => ['placeholder' => 'Select Country'],
    'pluginOptions' => [
        'allowClear' => true
    ],
])->label(false);

/*
 * Dependant City Typeahead
 */
echo $form->field($model, 'label')->widget(TypeaheadBasic::classname(), [
    'data' => ['' => ''],
    'options' => ['placeholder' => 'Enter Location Name'],
    'pluginOptions' => ['highlight' => true],
])->label(false);

/**
 * Dependant Postcode Typeahead 
 */
echo $form->field($model, 'postcode')->widget(TypeaheadBasic::classname(), [
    'data' => ['' => ''],
    'options' => ['placeholder' => 'Enter Postcode (Optional)'],
    'pluginOptions' => ['highlight' => true],
])->label(false);


echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
ActiveForm::end();

