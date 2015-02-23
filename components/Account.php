<?php

namespace amilna\cap\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use amilna\cap\models\AccountCodeSearch;

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
		$account = AccountCodeSearch::find()->where($sql,['search'=>$search])->one();				
		
		$value = 0;			
		$value = $account->sisa*($account->increaseon == 0?1:-1);					
					
		$div = pow(10,max(0,(floor((strlen($account->max."")-1)/3)))*3);		
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
