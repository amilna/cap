<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model amilna\cap\models\AccountCode */
/* @var $form yii\widgets\ActiveForm */

//$listParent = []+ArrayHelper::map(($model->isNewRecord?$model->parents():$model->parents($model->id)), 'id', 'name');
//$listParent = ArrayHelper::map($model->search()->find()->andWhere($model->isNewRecord?"":"id != :id",["id"=>$model->id])->all(), 'id', 'name');
$listParent = ArrayHelper::map($model->search()->find()
			->select(["id","concat( code ,'-', name ) as name"])
			->andWhere($model->isNewRecord?"":"id != :id",$model->isNewRecord?[]:["id"=>$model->id])
			->all(), 'id', 'name');
?>

<div class="account-code-form">

    <?php $form = ActiveForm::begin(); ?>
	
	<div class="row">
		<div class="col-sm-2">
    <?= $form->field($model, 'code')->textInput() ?>
		</div>
		<div class="col-sm-10">
    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>            
		</div>
    </div>
    
    <div class="row">
		<div class="col-sm-5">
    <?/*= $form->field($model, 'parent_id')->dropDownList($listParent,['maxlength' => 255])*/?>    
    <?= $form->field($model, 'parent_id')->widget(Select2::classname(), [
		'model'=>$model,
		'attribute'=>'parent_id',
		'data' => $listParent,				
		'options' => ['placeholder' => Yii::t('app','Select a account parent...')],
		'pluginOptions' => [
			'allowClear' => true
		],
		'pluginEvents' => [
			"change" => 'function() { 							
							var id = $("#accountcode-parent_id").val();
							if (id != "")
							{
								var url = "'.Yii::$app->urlManager->createUrl('//cap/account/view').'?format=json&id="+id;							
								$.ajax(url, {
									success: function (xhr) {
										xhr = JSON.parse(xhr);
										
										$("#accountcode-increaseon").val(xhr.increaseon);
										$("#accountcode-isbalance").prop("checked",xhr.isbalance);
										$("#accountcode-isbalance").bootstrapSwitch("state",xhr.isbalance);
										$("#accountcode-exchangable").prop("checked",xhr.exchangable);
										$("#accountcode-exchangable").bootstrapSwitch("state",xhr.exchangable);
										
										var select2_x = {"allowClear":false,"width":"resolve"};													
										jQuery.when(jQuery("#accountcode-increaseon").select2(select2_x)).done(initSelect2Loading("accountcode-increaseon"));
										jQuery("#accountcode-increaseon").on("select2-open", function(){
											initSelect2DropStyle("#accountcode-increaseon");				
										});
										
									},
									error: function (xhr) {
										$("#accountcode-increaseon").val("");
										$("#accountcode-isbalance").prop("checked",false);								
										$("#accountcode-isbalance").bootstrapSwitch("state",false);
										$("#accountcode-exchangable").prop("checked",false);
										$("#accountcode-exchangable").bootstrapSwitch("state",false);
										
										var select2_x = {"allowClear":false,"width":"resolve"};													
										jQuery.when(jQuery("#accountcode-increaseon").select2(select2_x)).done(initSelect2Loading("accountcode-increaseon"));
										jQuery("#accountcode-increaseon").on("select2-open", function(){
											initSelect2DropStyle("#accountcode-increaseon");				
										});
																		
									}
								});
							}
							else
							{
								$("#accountcode-increaseon").val("");								
								$("#accountcode-isbalance").prop("checked",false);								
								$("#accountcode-isbalance").bootstrapSwitch("state",false);
								$("#accountcode-exchangable").prop("checked",false);
								$("#accountcode-exchangable").bootstrapSwitch("state",false);
								
								var select2_x = {"allowClear":false,"width":"resolve"};													
								jQuery.when(jQuery("#accountcode-increaseon").select2(select2_x)).done(initSelect2Loading("accountcode-increaseon"));
								jQuery("#accountcode-increaseon").on("select2-open", function(){
									initSelect2DropStyle("#accountcode-increaseon");				
								});																
							}
						}',			
		],
	]);?>   
		</div>
		<div class="col-sm-3">	
	<?= $form->field($model, 'increaseon')->widget(Select2::classname(), [			
			'data' => $model->itemAlias('increaseon'),				
			'options' => ['placeholder' => Yii::t('app','Select a increase on type...')],
			'pluginOptions' => [
				'allowClear' => false
			],			
		]);
	?>
		</div>
		<div class="col-sm-2">
	
	<?= $form->field($model, 'isbalance')->widget(SwitchInput::classname(), [			
			'type' => SwitchInput::CHECKBOX,				
		]);
	?>
	
		</div>
		<div class="col-sm-2">
	
	<?= $form->field($model, 'exchangable')->widget(SwitchInput::classname(), [			
			'type' => SwitchInput::CHECKBOX,				
		]);
	?>
	
		</div>
	</div>	

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
