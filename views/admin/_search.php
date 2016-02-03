<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use humanized\contact\models\location\Country;

/* @var $this yii\web\View */
/* @var $model humanized\contact\models\location\CitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="city-search">

    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
    ]);
    ?>
    <div class="row">
        <div class="col-sm-3">
            <?php echo $form->field($model, 'country_id')->label(false)->dropDownList(ArrayHelper::map(Country::available(), 'iso_2', 'iso_3'), ['onChange' => 'this.form.submit()']); ?>
        </div>

    </div>
    <?php ActiveForm::end(); ?>

</div>
