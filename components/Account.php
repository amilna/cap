<?php

namespace amilna\cap\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use amilna\cap\models\AccountCode;

class Account extends Component
{
	public function getHtmlIndex($id = false, $name = false)
	{
		$module = Yii::$app->getModule('cap');											
					
		$search = $id;
		$sql = 'id = :search';
		if ($name)
		{
			$search = $name;
			$sql = 'name = :search';			
		}
		$account = AccountCode::find()->where($sql,['search'=>$search])->one();
		
		/*		
		$html = '<li>
					'.Html::a(Html::encode($account->name." ".$account->total), ['view', 'id' => $account->id]).'
					<a data-toggle="collapse" href="#account-codes-'.$account->id.'">
						 <span class="fa fa-caret-down pull-right"> V </span>
					</a>		  
					<ul id="account-codes-'.$account->id.'" class="nav collapse">';
		*/
		
		$value = 0;			
		$value = $account->saldo;			
		if ($account->increaseon == 0) {
		//	$value = $account->debet;			
		}
		else {
		//	$value = $account->credit;			
		}
		
					
		$div = pow(10,max(0,(floor((strlen($account->max."")-1)/3)))*3);
		/*$html = '<li>
					'.($account->parent_id != null?'<span class="row"><span class="col-xs-10 ">'.$account->code.' - ':'<h4><span class="col-xs-10 ">').Html::a($account->name, ['view', 'id' => $account->id]).'</span><span class="col-xs-2 pull-right">'.number_format($value/($div == 0?1:$div),2,",",".").'</span>'.($account->parent_id != null?'':'</h4>').'</span>					
					<ul id="account-codes-'.$account->id.'" class="navs list-unstyled" style="clear:right">';			
		*/			
		$html = '<li>
					'.($account->parent_id != null?$account->code.' - ':'<h4>').Html::a($account->name, ['view', 'id' => $account->id]).'<span class="pull-right">'./*" ".$account->debet."-".$account->credit."= ".*/number_format($value/($div == 0?1:$div),2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]).'</span>'.($account->parent_id != null?'':'</h4>').'
					<ul id="account-codes-'.$account->id.'" class="navs" style="clear:right">';						
		
		$children = $account->accountCodes;	  
		foreach($children as $child)
		{
			
				  $html .= Account::getHtmlIndex($child->id);
			  
		}
		
		$html .= '	</ul>														
				</li>';						
		
		return $html;		
	}
}	
