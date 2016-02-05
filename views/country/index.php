<?php

use yii\helpers\Html;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel humanized\contact\models\country\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Locations';
$this->params['breadcrumbs'][] = $this->title;



?>
<div class="row country-index">




    <div class="col-md-4">
        <div class="well">
            <blockquote><span class="glyphicon glyphicon-globe"></span> Location Management</blockquote>
            <?= $this->render('_aside') ?>
        </div>



    </div>
    <div class="col-md-8">



        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'code',
                'common_name',
                'official_name',
                'has_postcodes:boolean'
            //['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>
    </div>


</div>

