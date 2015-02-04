<?php

use yii\helpers\Html;
use kartik\money\MaskMoney;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model amilna\cap\models\Transaction */
/* @var $form yii\widgets\ActiveForm */
?>


<div id="<?= $increaseon ?>_:N" class="row detail">	
	<div class="col-xs-7">								
<?/*= Html::dropDownList("Transaction[".$increaseon."][:N][account]",false,ArrayHelper::map($list,"id","name"),['class'=>'form-control']) */?>					
<?= Select2::widget([
		'name'=>"Transaction[".$increaseon."][:N][account]",		
		'data' => ArrayHelper::map($list,"id","name"),				
		'options' => ['placeholder' => Yii::t('app','Select an account...')],
		'pluginOptions' => [
			'allowClear' => true
		],
		'pluginEvents' => [
			"change" => 'function() { 														
							var url = "'.Yii::$app->urlManager->createUrl('//cap/transaction/detail_form').'?increaseon=0";
							$.ajax(url, {
								success: function (xhr) {
									//console.log(xhr);
									var n = $(".debet .detail").length;
									xhr = xhr.replace("w0",":N").replace(":N",n);
									$(".debet").append(xhr);
								},
								error: function (xhr) {
																		
								}
							});
							var url = "'.Yii::$app->urlManager->createUrl('//cap/transaction/detail_form').'?increaseon=1";
							$.ajax(url, {
								success: function (xhr) {
									console.log(xhr);									
								},
								error: function (xhr) {
								
								}
							});
						}',
			/*"select2-open" => 'function() { log("open"); }',
			"select2-opening" => 'function() { log("select2-opening"); }',
			"select2-close" => 'function() { log("close"); }',
			"select2-highlight" => 'function() { log("highlight"); }',
			"select2-removing" => 'function() { log("removing"); }',
			"select2-removed" => 'function() { log("removed"); }',
			"select2-loaded" => 'function() { log("loaded"); }',
			"select2-focus" => 'function() { log("focus"); }',
			"select2-blur" => 'function() { log("blur"); }',*/
		],
	]);?>
	</div>
	<div class="col-xs-5">  					
		<div class="input-group">
			<div class="input-group-addon del-detail">Rp</div>
<?php
	echo MaskMoney::widget([
			'name'=>"Transaction[".$increaseon."][:N][subtotal]",
			'pluginOptions' => [
				'prefix' => '',
				'suffix' => '',
				'thousands' => '.',
				'decimal' => ',',
				'precision' => 0, 
				'allowNegative' => false
			]
		]);					
?>			
		</div>	
	</div>
</div>
			
