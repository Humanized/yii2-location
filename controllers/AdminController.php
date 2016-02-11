<?php

namespace humanized\location\controllers;

use humanized\location\models\location\Location;
use humanized\location\models\location\LocationSearch;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use GuzzleHttp\Client;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class AdminController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Location models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Location();
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $model = new Location(); //reset model
        }

        $searchModel = new LocationSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'model' => $model,
        ]);
    }

    /**
     * Displays a single Location model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Location model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Location model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionLoad()
    {
        if (isset($_POST['depdrop_parents'])) {
            $countryCode = $_POST['depdrop_parents'][0];
            $list = NULL;
            if (!\Yii::$app->controller->module->params['enableRemote']) {
                $list = Location::all($countryCode);
            } else {
                //Get API Results
                $client = new Client([
                    // Base URI is used with relative requests
                    'base_uri' => \Yii::$app->controller->module->params['remoteSettings']['uri'],
                    'auth' => [\Yii::$app->controller->module->params['remoteSettings']['token'], ''],
                ]);
                $list = Json::decode($client->request('GET', "places?country=$countryCode")->getBody(), true);
            }
            //Map data
            //Records are format ['id'=>$rec['id],'name'=>$rec['label']]
            $data = array_map(function($r) {
                return ['id' => $r['id'], 'name' => $r['label']];
            }, $list);
            echo Json::encode(['output' => $data, 'selected' => '']);
            return;
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function loadDefault()
    {
        
    }

    /**
     * Finds the Location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
