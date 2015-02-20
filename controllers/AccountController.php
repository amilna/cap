<?php

namespace amilna\cap\controllers;

use Yii;
use amilna\cap\models\AccountCode;
use amilna\cap\models\AccountCodeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AccountController implements the CRUD actions for AccountCode model.
 */
class AccountController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all AccountCode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountCodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);        
        
        $query = $dataProvider->query;                      
        $query->andwhere('id_left > 1')
			//->orderBy('id_right desc');
			->orderBy('id_left');
			
		$dataProvider->pagination = false;	
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    
    public function actionDaftar()
    {
        $searchModel = new AccountCodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);        
        
        $query = $dataProvider->query;        
        $query->andwhere('parent_id is null');                        
			
		$dataProvider->pagination = false;	
        
        return $this->render('daftar', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccountCode model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id,$format= false)
    {                       
        $model = $this->findModel($id);                                
        
        if ($format == 'json')
        {
			return \yii\helpers\Json::encode($model);	
		}
		else
		{
			return $this->render('view', [
				'model' => $model,
			]);
		}
    }
    
    public function actionGet_csv()
    {
        $file = Yii::$app->getBasePath().'/coa.csv';        
        
        if(!file_exists($file) || !is_readable($file))
		{
			die('file "'.$file.'" tidak ada!');
		}

		$header = NULL;
		$data = array();
		if (($handle = fopen($file, 'r')) !== FALSE)
		{
			while (($row = fgetcsv($handle)) !== FALSE)
			{
				if(!$header)
					$header = $row;
				else
					$data[] = array_combine($header, $row);
			}
			fclose($handle);
		}	
		
		foreach ($data as $d)
		{
			$model = AccountCode::findOne(["code"=>$d["NOPER"]]);	
			if (!$model)
			{
				$model = new AccountCode();
				$model->code = $d["NOPER"]."";
				$model->name = $d["NAPER"]."";
				$model->isdel = 0;
				$parent_id = null;
				$parent = AccountCode::findOne(["code"=>$d["CODE"]]);
				if ($parent)
				{
					$parent_id = $parent->id;	
				}
				$model->parent_id = $parent_id;
				$model->increaseon = (in_array(substr($model->code,0,1),["1","5"])?0:1);
				$model->isbalance = (in_array(substr($model->code,0,1),["4","5"])?0:1);				
				$model->exchangable = (in_array(substr($model->code,0,1),["1"])?1:0);				
				$model->save();				
			}
		}
		
		return $this->redirect(['index']);					
    }

    /**
     * Creates a new AccountCode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AccountCode();				
        
        /*
        $get = Yii::$app->request->get();
        if (isset($get['attributes']))
        {			
			$attr = $get['attributes'];			
			if (isset($attr["error"])) {
				$model->addErrors($attr["error"]);
			}
			$model->attributes = $attr;	
		}
		*/ 		
				
        if (Yii::$app->request->post()) {						
			$model->load(Yii::$app->request->post());						
			if ($model->save()) {				
				return $this->redirect(['view', 'id' => $model->id]);
			}
        }
        
		return $this->render('create', [
			'model' => $model
		]);
        
    }

    /**
     * Updates an existing AccountCode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);								
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
        		        
    }

    /**
     * Deletes an existing AccountCode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        //$model->delete();
        $model->isdel = 1;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AccountCode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccountCode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccountCode::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
