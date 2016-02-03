<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel humanized\contact\models\location\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Locations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row location-index">




    <div class="col-md-4">
        <div class="well">
            <blockquote><span class="glyphicon glyphicon-globe"></span> Location Management</blockquote>
            <?= $this->render('_aside') ?>
        </div>

        <div class="well">
            <blockquote><span class="glyphicon glyphicon-plus"></span> Create New Location</blockquote>
            <?= $this->render('_create', ['model' => $model]) ?>
        </div>       

    </div>
    <div class="col-md-8">

        <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'postcode',
                'label',
                'language'
            //['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>
    </div>


</div>

