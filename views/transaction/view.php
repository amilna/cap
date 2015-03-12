<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\jui\AutoComplete;
use kartik\money\MaskMoney;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model amilna\cap\models\Transaction */
/* @var $form yii\widgets\ActiveForm */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transactions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$module = Yii::$app->getModule('cap');

$company = '<strong>'.$module->company["name"].'</strong><br>
		'.$module->company["address"].'<br>
		'.Yii::t("app","Phones").': '.$module->company["phone"].'<br/>
		'.Yii::t("app","Email").': <a href="mailto:'.$model->toHex($module->company["email"]).'">'.str_replace("@"," [AT] ",$module->company["email"]).'</a>';

$subject = '<strong>'.Html::encode($model->subject).'</strong>';
?>

<h1><small><?= Html::encode($this->title) ?></small></h1>

<section class="invoice">
<!-- title row -->
  <div class="row">
	<div class="col-xs-12">
	  <h2 class="page-header">
		<i class="fa fa-globe"></i> <?= $module->company["name"]?>
		<small class="pull-right"><?= Html::encode(date('r',strtotime($model->time))) ?></small>
	  </h2>
	</div><!-- /.col -->
  </div>
  <!-- info row -->
  <div class="row invoice-info">
	<div class="col-sm-4 invoice-col">
	  <?= Yii::t("app","From")?>
	  <address>
		<?= $model->type >= 4?$company:$subject ?>	
	  </address>
	</div><!-- /.col -->
	<div class="col-sm-4 invoice-col">
	  <?= Yii::t("app","To")?>
	  <address>
		<?= $model->type < 4?$company:$subject ?>
	  </address>
	</div><!-- /.col -->
	<div class="col-sm-4 invoice-col">
	  <b><?= Yii::t("app","Reference") ?>: </b> <?= Html::encode($model->reference) ?><br/>
	  <br/>
	  <b><?= Yii::t("app","Type") ?>: </b> <?= Html::encode($model->itemAlias("type",$model->type)) ?><br/>
	  <b><?= Yii::t("app","Title") ?>: </b> <?= Html::encode($model->title) ?><br/>
	  <b><?= Yii::t("app","Tags") ?>: </b> <?= Html::encode($model->tags) ?><br/>	  
	</div><!-- /.col -->
  </div><!-- /.row -->

  <!-- Table row -->
  <div class="row">
	<div class="col-xs-12 table-responsive">
	  <table class="table table-striped">
		<thead>
		  <tr>
			<th><?=Yii::t("app","Code")?></th>
			<th><?=Yii::t("app","Account")?></th>
			<th><?=Yii::t("app","Remarks")?></th>
			<th style="text-align:right"><?= Yii::t("app","Quantity")?></th>
			<th style="text-align:right"><?=Yii::t("app","Debet")?></th>
			<th style="text-align:right"><?=Yii::t("app","Credit")?></th>			
		  </tr>
		</thead>
		<tbody>
		<?php
			foreach ($model->journals as $j)
			{
				$html = '<tr>
							<td>'.Html::encode($j->account->code).'</td>
							<td>'.Html::encode($j->account->name).'</td>
							<td>'.Html::encode($j->remarks).'</td>
							<td style="text-align:right">'.Html::encode(number_format($j->quantity,2)).'</td>
							<td style="text-align:right">'.Html::encode($model->toMoney($j->debet)).'</td>
							<td style="text-align:right">'.Html::encode($model->toMoney($j->credit)).'</td>
						  </tr>';
						  
				echo $html;		  			
			}
		?>				
		</tbody>
	  </table>
	</div><!-- /.col -->
  </div><!-- /.row -->


  <div class="row">
	<!-- accepted payments column -->
	<div class="col-xs-6">
	  <p class="lead"><?=Yii::t("app","Remarks")?></p>	  
	  <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
		<?= Html::encode($model->remarks) ?>
	  </p>
	</div><!-- /.col -->
	<div class="col-xs-6">	  
	  <div class="table-responsive">
		<table class="table">		  
		  <tr>
			<th style="text-align:right">Total:</th>
			<td style="text-align:right"><?= $model->toMoney($model->total)?></td>
		  </tr>
		</table>
	  </div>
	</div><!-- /.col -->
  </div><!-- /.row -->

  <!-- this row will not appear when printing 
  <div class="row no-print">
	<div class="col-xs-12">
	  <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
	  <button class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit Payment</button>
	  <button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button>
	</div>
  </div>
  -->
</section><!-- /.content -->
