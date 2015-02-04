<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model amilna\cap\models\AccountCode */
/* @var $form yii\widgets\ActiveForm */

$listParent = []+ArrayHelper::map(($model->isNewRecord?$model->parents():$model->parents($model->id)), 'id', 'name');
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
							var url = "'.Yii::$app->urlManager->createUrl('//cap/account/view').'?format=json&id="+id;							
							$.ajax(url, {
								success: function (xhr) {
									xhr = JSON.parse(xhr);									
									$("#accountcode-increaseon").val(xhr.increaseon);
									$("#accountcode-isbalance").prop("checked",xhr.isbalance);
								},
								error: function (xhr) {
									$("#accountcode-increaseon").val("");
									$("#accountcode-isbalance").prop("checked",false);
								}
							});
						}',			
		],
	]);?>   
		</div>
		<div class="col-sm-5">
	<?= $form->field($model, 'increaseon')->dropDownList($model->itemAlias('increaseon')) ?>
		</div>
		<div class="col-sm-2">
	<?= $form->field($model, 'isbalance')->checkBox(['label' => $model->attributeLabels()['isbalance']]) ?>		
		</div>
	</div>	

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
