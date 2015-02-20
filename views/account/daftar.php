<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use amilna\cap\components\Account;

/* @var $this yii\web\View */
/* @var $searchModel amilna\cap\models\AccountCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Account Codes');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'CAP'), 'url' => ['/cap/default']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>

	ul ul, ol ul, ul ol, ol ol {		
		font-size: 16px!important;		
		font-weight: normal;
	}
	
	ul ul ul, ol ul ul, ul ol ul, ol ol ul{		
		font-size: 14px!important;				
		font-weight: normal;
		font-style: normal;		
	}
	
	ul ul ul li, ol ul ul li, ul ol ul li, ol ol ul li{				
		border-top: 1px solid #ababab;		
	}
	
	ul ul ul ul, ol ul ul ul, ul ol ul ul, ol ol ul ul{		
		font-size: 12px!important;				
		font-weight: normal;
		font-style: normal;		
	}
	
	ul ul ul ul li, ol ul ul ul li, ul ol ul ul li, ol ol ul ul li{				
		border-top: 1px solid #cdcdcd;		
	}

</style>	

<div class="account-code-index">

<h1><?= Html::encode($this->title) ?></h1>
<?php /*    
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
*/?>	
    <p>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Account Code',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <div class="row">
		<div class="col-xs-12" style="text-align:right">
			<i>
		<?php
			$module = Yii::$app->getModule('cap');												
			if (count($dataProvider->getModels()) == 0)
			{
				$div = 0;	
			}
			else
			{			
				$div = pow(10,max(0,(floor((strlen($dataProvider->getModels()[0]->max."")-1)/3)))*3);			
			}	
			echo Yii::t('app','Amount numbers are per ').($div == 0?"units":number_format($div,0,$module->currency["decimal_separator"],$module->currency["thousand_separator"]));
		?>
			</i>
		</div>
	</div>
	
	<div class="well">
		<h2><?= Yii::t('app','Balance') ?></h2>
		<div class="row">
			<div class="col-md-6">
				<ul class="nav">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemOptions' => ['class' => 'item'],
					'summary'=>Yii::t('app','List of account codes where increase on receipt or revenues'),
					'itemView' => function ($model, $key, $index, $widget) {					
						$html = '';
						if ($model->isbalance == 1 && $model->increaseon == 0) {
							$ch = [];
							$children = $model->accountCodes;
							foreach($children as $child)
							{
								$c = $child->attributes;
								array_push($ch,$c);	
							}
							
							$html = Account::getHtmlIndex($model->id);
						}
						return $html;
					},
					/*'itemView'=>'_itemIndex',*/
				]) ?>
				</ul>
			</div>
			<div class="col-md-6">
				<ul class="nav">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemOptions' => ['class' => 'item'],
					'summary'=>Yii::t('app','List of account codes where debet on reduction'),
					'itemView' => function ($model, $key, $index, $widget) {					
						$html = '';
						if ($model->isbalance == 1 && $model->increaseon == 1) {
							$ch = [];
							$children = $model->accountCodes;
							foreach($children as $child)
							{
								$c = $child->attributes;
								array_push($ch,$c);	
							}
							
							$html = Account::getHtmlIndex($model->id);
						}
						return $html;
					},
					/*'itemView'=>'_itemIndex',*/
				]) ?>
				</ul>
			</div>		
		</div>	
	</div>
	
	<h2><?= Yii::t('app','Profit/Loss') ?></h2>
		<div class="row">
			<div class="col-md-6">
				<ul class="nav">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemOptions' => ['class' => 'item'],
					'summary'=>Yii::t('app','List of account codes where increase on expenses, but has no affect on balance'),
					'itemView' => function ($model, $key, $index, $widget) {					
						$html = '';
						if ($model->isbalance == 0 && $model->increaseon == 0) {
							$ch = [];
							$children = $model->accountCodes;
							foreach($children as $child)
							{
								$c = $child->attributes;
								array_push($ch,$c);	
							}
							
							$html = Account::getHtmlIndex($model->id);
						}
						return $html;
					},
					/*'itemView'=>'_itemIndex',*/
				]) ?>
				</ul>
			</div>
			<div class="col-md-6">
				<ul class="nav">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemOptions' => ['class' => 'item'],
					'summary'=>Yii::t('app','List of account codes where increase on revenues, but has no affect on balance'),
					'itemView' => function ($model, $key, $index, $widget) {					
						$html = '';
						if ($model->isbalance == 0 && $model->increaseon == 1) {
							$ch = [];
							$children = $model->accountCodes;
							foreach($children as $child)
							{
								$c = $child->attributes;
								array_push($ch,$c);	
							}
							
							$html = Account::getHtmlIndex($model->id);
						}
						return $html;
					},
					/*'itemView'=>'_itemIndex',*/
				]) ?>
				</ul>
			</div>		
		</div>	

	
</div>
