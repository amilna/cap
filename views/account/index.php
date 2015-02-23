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
	
	<?php
		$level = 2;		
		$html0 = "";
		$html1 = "";
		$html2 = "";
		$html3 = "";
		$xtml = "";
		foreach ($dataProvider->getModels() as $account)
		{											
			$value = $account->saldo*($account->increaseon == 0?1:-1);
			$html = "";
			if ($account->id_level > $level)
			{																
				
				for ($i = 0;$i < $account->id_level-$level;$i++)
				{
					$html .= '<ul style="list-style-type: none;padding: 0px;margin-left: 20px;clear:right"><li>';	
				}
				
				$html .= ($account->id_level != 2?$account->code.' - ':'<h4>').Html::a($account->name, ['view', 'id' => $account->id]).'<span class="pull-right">'./*" ".$account->debet."-".$account->credit."= ".*/number_format($value/($div == 0?1:$div),2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]).'</span>'.($account->id_level != 2?'':'</h4>').' <small>('.$account->id_left.'-'.$account->id_right.'-'.$account->id_level.')</small>';
			
			}
			elseif ($account->id_level < $level)
			{
				for ($i = 0;$i < $level-$account->id_level;$i++)
				{					
					$html .= '</li></ul>';	
				}
				
				$html .= ($account->id_level != 2?$account->code.' - ':'<h4>').Html::a($account->name, ['view', 'id' => $account->id]).'<span class="pull-right">'./*" ".$account->debet."-".$account->credit."= ".*/number_format($value/($div == 0?1:$div),2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]).'</span>'.($account->id_level != 2?'':'</h4>').' <small>('.$account->id_left.'-'.$account->id_right.'-'.$account->id_level.')</small>';
							
			}
			else
			{
				$html .= '<li>'.($account->id_level != 2?$account->code.' - ':'<h4>').Html::a($account->name, ['view', 'id' => $account->id]).'<span class="pull-right">'./*" ".$account->debet."-".$account->credit."= ".*/number_format($value/($div == 0?1:$div),2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]).'</span>'.($account->id_level != 2?'':'</h4>').' <small>('.$account->id_left.'-'.$account->id_right.'-'.$account->id_level.')</small></li>';
			}						
						
			if ($account->isbalance && $account->increaseon == 0)
			{
				$html0 .= $html;	
			}
			elseif ($account->isbalance && $account->increaseon == 1)
			{
				$html1 .= $html;	
			}
			elseif (!$account->isbalance && $account->increaseon == 0)
			{
				$html2 .= $html;	
			}
			elseif (!$account->isbalance && $account->increaseon == 1)
			{
				$html3 .= $html;	
			}					
						
			$xtml .= $html;				
			$level = $account->id_level; 
		}	
	?>
	
	<div class="well">
		<h2><?= Yii::t('app','Balance') ?></h2>
		<div class="row">
			<div class="col-md-6">
				<ul class="nav">
					<?= $html0 ?>
				</ul>
			</div>
			<div class="col-md-6">
				<ul class="nav">
					<?= $html1 ?>
				</ul>
			</div>		
		</div>	
	</div>
	
	<h2><?= Yii::t('app','Profit/Loss') ?></h2>
		<div class="row">
			<div class="col-md-6">
				<ul class="nav">
					<?= $html2 ?>
				</ul>
			</div>
			<div class="col-md-6">
				<ul class="nav">
					<?= $html3 ?>
				</ul>
			</div>		
		</div>	
	
	<?php /*
	<ul class="nav">
		<?= $xtml ?>
	</ul>
	*/
	?> 
	
</div>
