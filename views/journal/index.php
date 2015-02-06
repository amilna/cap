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
?>
<div class="journal-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Transaction',
]), ['//cap/transaction/create'], ['class' => 'btn btn-success']) ?>
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
					['content'=>'Account', 'options'=>['colspan'=>6, 'class'=>'text-center','style'=>'background-color: #fdfdfd']], 					
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
				'attribute'=>'account',				
				'value'=>function($data){										
					return ($data->account->code < 0?"":$data->account->code." - ").$data->account->name;
				},
				'filterType'=>GridView::FILTER_SELECT2,				
				'filterWidgetOptions'=>[
					'data'=>ArrayHelper::map(Transaction::accounts(),"name","name"),
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
					return number_format($data->quantity,2);
				},
				//'filterType'=>GridView::FILTER_MONEY,
				//'filterWidgetOptions'=>['options'=>['style'=>'text-align:right']],
				'hAlign'=>'right',
				'pageSummary'=>true,
				'pageSummaryFunc'=>'sum'
				
			],
            [				
				'attribute' => 'debet',
				'value'=>function($data){										
					//return $data->debet;
					return number_format($data->debet,2);
				},
				'mergeHeader'=>true,
				'headerOptions'=>['class'=>'kv-align-middle'],
				'hAlign'=>'right',
				'vAlign'=>'top',
				'pageSummary'=>function ($summary, $data, $widget) { 					
					$r = 0;
					foreach($data as $d)
					{
						$r += floatval(str_replace(",","",$d));
					}
					return number_format($r,2);
				},
				//'pageSummaryFunc'=>'sum'
				
			],
            [				
				'attribute' => 'credit',
				'value'=>function($data){										
					//return $data->credit;
					return number_format($data->credit,2);
				},				
				'mergeHeader'=>true,
				'headerOptions'=>['class'=>'kv-align-middle'],
				'hAlign'=>'right',
				'vAlign'=>'top',
				'pageSummary'=>function ($summary, $data, $widget) { 					
					$r = 0;
					foreach($data as $d)
					{
						$r += floatval(str_replace(",","",$d));
					}
					return number_format($r,2);
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
