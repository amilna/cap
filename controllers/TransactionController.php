<?php

namespace amilna\cap\controllers;

use Yii;
use amilna\cap\models\Transaction;
use amilna\cap\models\TransactionSearch;
use amilna\cap\models\Journal;
use amilna\cap\models\Template;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransactionController implements the CRUD actions for Transaction model.
 */
class TransactionController extends Controller
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
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex($format= false,$arraymap= false,$term = false)
    {        
        $searchModel = new TransactionSearch();
        $req = Yii::$app->request->queryParams;
        if ($term) { $req[basename(str_replace("\\","/",get_class($searchModel)))]["term"] = $term;}        
        $dataProvider = $searchModel->search($req);				
		
		$query = $dataProvider->query;        
		if (!isset($req["sort"]))
        {
			$query->orderBy("time desc");
		}
		
		if ($format == 'json')
        {
			$model = [];
			foreach ($dataProvider->getModels() as $d)
			{
				$obj = $d->attributes;
				if ($arraymap)
				{
					$map = explode(",",$arraymap);
					if (count($map) == 1)
					{
						$obj = (isset($d[$arraymap])?$d[$arraymap]:null);
					}
					else
					{
						$obj = [];					
						foreach ($map as $a)
						{
							$k = explode(":",$a);						
							$v = (count($k) > 1?$k[1]:$k[0]);
							$obj[$k[0]] = ($v == "Obj"?json_encode($d->attributes):(isset($d->$v)?$d->$v:null));
						}
					}
				}
				
				if ($term)
				{
					if (!in_array($obj,$model))
					{
						array_push($model,$obj);
					}
				}
				else
				{	
					array_push($model,$obj);
				}
			}			
			return \yii\helpers\Json::encode($model);	
		}
		else
		{			
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}		        
    }

    /**
     * Displays a single Transaction model.
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
    
    public function actionTemplate($id = false,$term = false,$arraymap=false,$results = false)
    {                        
        $res = [];
        if ($term)
        {
			$model = Template::find()->select(["title"])->where(["like","lower(title)",strtolower($term)])->asArray()->all();			
						
			foreach ($model as $m)
			{
				if ($arraymap)
				{
					$map = explode(",",$arraymap);
					if (count($map) == 1)
					{
						$obj = (isset($m[$arraymap])?$m[$arraymap]:null);
					}
					else
					{
						$obj = [];					
						foreach ($map as $a)
						{
							$k = explode(":",$a);						
							$v = (count($k) > 1?$k[1]:$k[0]);
							$obj[$k[0]] = ($v == "Obj"?json_encode($m):(isset($m[$v])?$m[$v]:null));
						}
					}
					$res[] = $obj;
				}				
				else
				{
					$res[] = $m["title"];	
				}	
			}
					
		}
		elseif ($id)
		{
			$res = Template::find()->where("title = :t",["t"=>$id])->one();					
		}
		
		if ($results)
		{
			return \yii\helpers\Json::encode(["results"=>$res]);			
		}
		else
		{
			return \yii\helpers\Json::encode($res);			
		}	
    }
    
    public function actionDetail_form($increaseon,$usedaccounts = "")    
    {        
        $usedaccounts = $usedaccounts == ""? [] :explode(",",$usedaccounts);
                
        $list = Transaction::accounts($increaseon,$usedaccounts);
        
        return $this->renderPartial('_form_details', [
			'increaseon'=>$increaseon,
            'list' => $list,
        ]);
    }

    /**
     * Creates a new Transaction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transaction();	
        $model->time = date("Y/m/d H:i:s");
        $model->isdel = 0;	
        $model->type = 0;	
		                       		
        if (Yii::$app->request->post()) {						
			$post = Yii::$app->request->post();						
			$debet = (isset($post['Transaction']['debet'])?$post['Transaction']['debet']:[]);
			$credit = (isset($post['Transaction']['credit'])?$post['Transaction']['credit']:[]);
			
			$p = $post['Transaction'];			
			
			$istemplate = false;
			if (isset($post['Transaction']['template']))
			{
				$istemplate = true;
			}			
			
			if ($model->saveTemplate($p,$istemplate)) {				
				if ($istemplate)
				{
					return $this->redirect(['index']);	
				}
				
				if (is_array($post['Transaction']['tags']))
				{
					$post['Transaction']['tags'] = implode(",",$post['Transaction']['tags']);
				}
						
				$model->load($post);														
				
				$model->total = ($model->total == null?0:$model->total);
				$transaction = Yii::$app->db->beginTransaction();
				try {				
					if ($model->save()) {
						
						foreach (array_merge($debet,$credit) as $d)
						{					
							$j = new Journal();	
							$j->load(["Journal"=>$d]);						
							$j->remarks = (empty($j->remarks)?$model->remarks." (".$model->tags.")":$j->remarks);
							$j->transaction_id = $model->id;
							$j->isdel = 0;
							
							if ($j->amount > 0)
							{					
								$j->save();			
							}
						}												
						$transaction->commit();	
						return $this->redirect(['view', 'id' => $model->id]);
					}
					else {				
					
						$transaction->rollBack();
						$model->id = array_merge($debet,$credit);
					}										
				} catch (Exception $e) {
					$transaction->rollBack();
				}
							
				
			}
			else {				
			
				$model->id = array_merge($debet,$credit);
			}			
        } 
        
		return $this->render('create', [
			'model' => $model
		]);
	
    }

    /**
     * Updates an existing Transaction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		
		$model->tags = !empty($model->tags)?explode(",",$model->tags):[];
		
        if (Yii::$app->request->post()) {						
			$post = Yii::$app->request->post();
			$debet = (isset($post['Transaction']['debet'])?$post['Transaction']['debet']:[]);
			$credit = (isset($post['Transaction']['credit'])?$post['Transaction']['credit']:[]);
			
			$p = $post['Transaction'];									
			
			$istemplate = false;
			if (isset($post['Transaction']['template']))
			{
				$istemplate = true;
			}			
			
			if ($model->saveTemplate($p,$istemplate)) {				
				if ($istemplate)
				{
					return $this->redirect(['index']);	
				}
				
				if (is_array($post['Transaction']['tags']))
				{
					$post['Transaction']['tags'] = implode(",",$post['Transaction']['tags']);
				}
							
				$model->load($post);				
				$model->total = ($model->total == null?0:$model->total);
				$transaction = Yii::$app->db->beginTransaction();
				try {				
					if ($model->save()) {					
						
						$all = array_merge($debet,$credit);						
						$js = Journal::find()->where("transaction_id = :id",["id"=>$model->id])->all();
						foreach ($js as $j)
						{
							//$j->delete();				
							
							$ada = false;			
							foreach ($all as $key => $val) {
								if ($val['account_id'] == $j->account_id && $val['type'] == $j->type && $val['amount'] > 0) {
									$ada = true;									
								}
							}
							if (!$ada && $j->isdel == 0) {
								$j->isdel = 1;
								$j->save();								
							}
						}												
						
						foreach ($all as $d)
						{					
							$j = Journal::find()->where("transaction_id = :id AND account_id = :aid AND type = :t",["id"=>$model->id,"t"=>intval($d["type"]),"aid"=>intval($d["account_id"])])->one();					
							if (!$j)
							{
								$j = new Journal();	
							}
							$j->load(["Journal"=>$d]);	
							$j->remarks = (empty($j->remarks)?$model->remarks." (".$model->tags.")":$j->remarks);
							$j->transaction_id = $model->id;
							if ($j->amount > 0)
							{
								$j->isdel = 0;					
								$j->save();			
							}
						}												
						$transaction->commit();						
						return $this->redirect(['view', 'id' => $model->id]);			
					}
					else {
						$transaction->rollBack();	
					}														
				} catch (Exception $e) {
					$transaction->rollBack();
				}
				
			}
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Transaction model.
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
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
