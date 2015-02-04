<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use amilna\yap\GridView;

/* @var $this yii\web\View */
/* @var $searchModel amilna\cap\models\TransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Transactions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'CAP'), 'url' => ['/cap/default']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Transaction',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>


	<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,        
        'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
		'caption'=>Yii::t('app','Transaction'),
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
			'type' => GridView::TYPE_PRIMARY,
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
					//['content'=>'Transaction', 'options'=>['colspan'=>10, 'class'=>'text-center','style'=>'background-color: #fdfdfd']], 					
				],
				'options'=>['class'=>'skip-export'] // remove this row from export
			]
		],
		'floatHeader' => true,		
    
        'mergeColumns' => ['type','subject','title','tags'],
        'type'=>'firstrow',        
        
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

                   
            [				
				'attribute'=>'type',				
				'value'=>function($data){										
					return \amilna\cap\models\Transaction::itemAlias('type',$data->type);
				},
				'filterType'=>GridView::FILTER_SELECT2,				
				'filterWidgetOptions'=>[
					'data'=>\amilna\cap\models\Transaction::itemAlias('type'),
					'options' => ['placeholder' => Yii::t('app','Select a transaction type...')],
					'pluginOptions' => [
						'allowClear' => true
					],
					
				],
            ],            
            'subject',
            'title',
            'tags',
             [				
				'attribute' => 'time',
				'value' => 'time',				
				'filterType'=>GridView::FILTER_DATE_RANGE,
				'filterWidgetOptions'=>[
					'pluginOptions' => [
						'format' => 'YYYY-MM-DD HH:mm:ss',				
						'todayHighlight' => true,
						'timePicker'=>true,
						'timePickerIncrement'=>15,
						'opens'=>'left'
					],
					'pluginEvents' => [
					"apply.daterangepicker" => 'function() {									
									$(this).change();
								}',
					],			
				],
			],    
            'reference',
            'remarks:ntext',            
            [				
				'attribute' => 'total',				
				'value'=>function($data){										
					return number_format($data->total,2);
				},				
				'hAlign'=>'right',
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
            // 'amount',
            // 'type',
            // 'isdel',

            ['class' => 'kartik\grid\ActionColumn'],
        ],
        
    ]); ?>	   
    
	<div style="margin-bottom:100px;"></div>
	
</div>
