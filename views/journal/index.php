<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
//use yii\grid\GridView;
//use kartik\grid\GridView;
use amilna\yap\GridView;
use amilna\cap\models\Transaction;
//use dosamigos\grid\GroupGridView;

/* @var $this yii\web\View */
/* @var $searchModel amilna\cap\models\JournalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Journals');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'CAP'), 'url' => ['/cap/default']];
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->pagination = [
                'pageSize' => 10,
            ];

$module = Yii::$app->getModule('cap');									            
$tra = new Transaction();            
?>
<div class="journal-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?/*= Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('app','Transaction'),
]), ['//cap/transaction/create'], ['class' => 'btn btn-success'])*/ ?>
    </p>
		
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,        
        'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false		
		'caption'=>Yii::t('app','Journal'),
		'headerRowOptions'=>['class'=>'kartik-sheet-style','style'=>'background-color: #fdfdfd'],
		'filterRowOptions'=>['class'=>'kartik-sheet-style skip-export','style'=>'background-color: #fdfdfd'],
		'pjax' => false, // pjax is set to always true for this demo
		'bordered' => true,
		'striped' => true,
		'condensed' => true,
		'responsive' => true,
		'hover' => true,
		'showPageSummary' => true,
		'pageSummaryRowOptions'=>['class'=>'kv-page-summary','style'=>'background-color: #fdfdfd'],
		
		'panel' => [
			'type' => GridView::TYPE_DEFAULT,
			'heading' => false,
		],
		'toolbar' => [
			['content'=>
				//Html::a('<i class="glyphicon glyphicon-plus"></i>',['//cap/transaction/create'], ['type'=>'button', 'title'=>Yii::t('app', 'Add Book'), 'class'=>'btn btn-success']) . ' '.
				Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>Yii::t('app', 'Reset Grid')])
			],
			'{export}',
			'{toggleData}'
		],
		'beforeHeader'=>[
			[
				'columns'=>[
					['content'=>'Transaction', 'options'=>['colspan'=>6, 'class'=>'text-center','style'=>'background-color: #fdfdfd']], 
					['content'=>'Account', 'options'=>['colspan'=>3, 'class'=>'text-center','style'=>'background-color: #fdfdfd']], 					
					['content'=>'<div class="row-fluid" style="width:260px"><div class="col-xs-6" style="padding:0 2px 0 2px;">Debet</div><div class="col-xs-6" style="padding:0 2px 0 2px;">Credit</div></div>', 'options'=>['colspan'=>1, 'class'=>'text-centers','style'=>'background-color: #fdfdfd;text-align:right;']], 					
				],
				'options'=>['class'=>'skip-export'] // remove this row from export
			]
		],
		'floatHeader' => true,		
    
        'mergeColumns' => ['time','title','subject','tags','transactionRemarks'],
        'type'=>'firstrow',        
        
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [				
				'attribute' => 'time',
				'value' => 'transaction.time',				
				'filterType'=>GridView::FILTER_DATE_RANGE,
				'filterWidgetOptions'=>[
					'pluginOptions' => [
						'format' => 'YYYY-MM-DD HH:mm:ss',				
						'todayHighlight' => true,
						'timePicker'=>true,
						'timePickerIncrement'=>15,
					],
					'pluginEvents' => [
					"apply.daterangepicker" => 'function() {									
									$(this).change();
								}',
					],			
				],
			],
            [
				'attribute' => 'title',
				'value' => 'transaction.title',
				
				
			],
            [
				'attribute' => 'subject',
				'value' => 'transaction.subject'
			],
            [
				'attribute' => 'tags',
				'value' => 'transaction.tags'
			],
            [
				'attribute' => 'transactionRemarks',
				'value' => 'transaction.remarks'
			],
            [				
				'attribute'=>'accountName',				
				'value'=>function($data){															
					return ($data->account->code < 0?"":$data->account->code." - ").$data->account->name;					
				},
				'filterType'=>GridView::FILTER_SELECT2,				
				'filterWidgetOptions'=>[
					'data'=>ArrayHelper::map($tra->accounts(),"name","name"),
					'options' => ['placeholder' => Yii::t('app','Select an account...')],
					'pluginOptions' => [
						'allowClear' => true
					],
					
				],
            ],
            'remarks:ntext',
            [				
				'attribute' => 'quantity',
				'value'=>function($data){	
					$module = Yii::$app->getModule('cap');									
					return number_format($data->qty,2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]);
				},				
				'hAlign'=>'right',
				'pageSummary'=>function ($summary, $data, $widget) { 					
					$module = Yii::$app->getModule('cap');
					$r = 0;
					foreach($data as $d)
					{
						$r += floatval(str_replace($module->currency["thousand_separator"],"",$d));
					}
					return number_format($r,2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]);
				},
				//'pageSummary'=>true,
				//'pageSummaryFunc'=>'sum'
				
			],
			/*
            [				
				'attribute' => 'debet',
				'value'=>function($data){										
					$module = Yii::$app->getModule('cap');									
					return number_format($data->debet,2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]);
				},
				'mergeHeader'=>true,
				'headerOptions'=>['class'=>'kv-align-middle'],
				'hAlign'=>'right',
				'vAlign'=>'top',
				'pageSummary'=>function ($summary, $data, $widget) { 					
					$module = Yii::$app->getModule('cap');
					$r = 0;
					foreach($data as $d)
					{
						$r += floatval(str_replace($module->currency["thousand_separator"],"",$d));
					}
					return number_format($r,2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]);
				},
				'pageSummaryFunc'=>'sum'
				
			],*/
            [				
				'attribute' => 'amount',				
				'format'=>'html',
				'value'=>function($data) use ($module){																				
					$html = '<div class="col-xs-6" style="padding:0 2px 0 2px;" >'.number_format($data->debet,2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]).'</div>';
					$html .= '<div class="col-xs-6" style="padding:0 2px 0 2px;" >'.number_format($data->credit,2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]).'</div>';
					return '<div class="row-fluid" style="width:260px">'.$html.'</div>';					
				},				
				//'mergeHeader'=>true,
				'headerOptions'=>['class'=>'kv-align-bottom','style'=>'text-align:center;'],
				'hAlign'=>'right',
				'vAlign'=>'top',
				'pageSummary'=>function ($summary, $data, $widget) { 					
					$module = Yii::$app->getModule('cap');
					$db = 0;
					$cr = 0;
					foreach($data as $d)
					{
						$ds = explode('</div>',$d);						
						$db += floatval(str_replace($module->currency["thousand_separator"],"",(substr($ds[0],strrpos($ds[0]," >")+2))));
						$cr += floatval(str_replace($module->currency["thousand_separator"],"",(substr($ds[1],strrpos($ds[1]," >")+2))));												
					}
					
					$html = '<div class="col-xs-6" style="padding:0 2px 0 2px;" >'.number_format($db,2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]).'</div>';
					$html .= '<div class="col-xs-6" style="padding:0 2px 0 2px;" >'.number_format($cr,2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]).'</div>';
					return '<div class="row-fluid" style="width:260px">'.$html.'</div>';					
				},
				
			],
            // 'amount',
            // 'type',
            // 'isdel',

            //['class' => 'kartik\grid\ActionColumn'],
        ],
        
    ]); ?>
	
	<div style="margin-bottom:100px;"></div>
	
</div>
