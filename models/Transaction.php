<?php

namespace amilna\cap\models;

use Yii;
use amilna\cap\models\AccountCode;
use amilna\cap\models\Template;
use amilna\cap\models\TransactionSearch;

/**
 * This is the model class for table "{{%cap_transaction}}".
 *
 * @property integer $id
 * @property string $subject
 * @property string $title
 * @property string $remarks
 * @property double $total
 * @property integer $type
 * @property string $time
 * @property integer $isdel
 *
 * @property CapJournal[] $capJournals
 */
class Transaction extends \yii\db\ActiveRecord
{
    public $dynTableName = '{{%cap_transaction}}';    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {        
        $mod = new Transaction();        
        return $mod->dynTableName;
    }		
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'title', 'reference','remarks', 'type'], 'required'],
            [['remarks'], 'string'],
            [['total'], 'number'],
            [['type', 'isdel'], 'integer'],
            [['reference'], 'unique'],
            [['time','tags'], 'safe'],
            [['subject', 'title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subject' => Yii::t('app', 'Subject'),
            'title' => Yii::t('app', 'Title'),
            'remarks' => Yii::t('app', 'Remarks'),
            'reference' => Yii::t('app', 'Reference'),
            'tags' => Yii::t('app', 'Tags'),
            'total' => Yii::t('app', 'Total'),
            'type' => Yii::t('app', 'Type'),
            'time' => Yii::t('app', 'Time'),
            'isdel' => Yii::t('app', 'Isdel'),
        ];
    }

    public function	accounts($increaseon = false,$usedAccounts = [])
    {
		
		/*
		$rows = (new \yii\db\Query())
				->select(["t.id AS id", "case when (t.code < 0) then t.name else concat (t.code,' - ',t.name) end as name,t.increaseon,t.isbalance,t.exchangable"])
				->from(AccountCode::tableName()." AS t")
				->leftJoin(AccountCode::tableName()." AS a", "t.id = a.parent_id")
				->where(($increaseon?"t.increaseon = :increaseon AND ":"")."a.id is null AND not (t.id = ANY(array".json_encode($usedAccounts)."::integer[]))",$increaseon?['increaseon'=>$increaseon]:[])
				->orderBy("name")
				->all();
		*/
		
				
		$rows = AccountCodeSearch::find()
				->select(["".AccountCode::tableName().".id AS id", "case when (".AccountCode::tableName().".code < 0) then ".AccountCode::tableName().".name else concat (".AccountCode::tableName().".code,' - ',".AccountCode::tableName().".name) end as name,".AccountCode::tableName().".increaseon,".AccountCode::tableName().".isbalance,".AccountCode::tableName().".exchangable"])				
				->leftJoin(AccountCode::tableName()." AS a", AccountCode::tableName().".id = a.parent_id")				
				->andWhere(($increaseon?"".AccountCode::tableName().".increaseon = :increaseon AND ":"")."a.id is null AND not (".AccountCode::tableName().".id = ANY(array".json_encode($usedAccounts)."::integer[]))",$increaseon?['increaseon'=>$increaseon]:[])				
				->orderBy("name")
				->asArray()
				->all();								
		
		return $rows;		
	}	
	
	public function saveTemplate($p,$istemplate)
	{
		$t = [];
		$t['title'] = $p['title'];
		$t['tags'] = $p['tags'];
		$t['type'] = $p['type'];
		$t['subject'] = $p['subject'];
		$t['remarks'] = $p['remarks'];
		$t['total'] = $p['total'];
		$t['journals'] = [];		
		
		foreach (['debet','credit'] as $a)
		{
			if (isset($p[$a])) {
				foreach ($p[$a] as $d)
				{
					if (!empty($d['account_id']) && $d['account_id'] > 0 && $d['amount'] > 0)
					{
						$v = [];
						$v['account_id'] = $d['account_id'];
						$v['type'] = $d['type'];
						$v['remarks'] = $d['remarks'];
						$v['quantity'] = $d['quantity'];
						$v['amount'] = $d['amount'];				
						$v['ratio'] = $d['amount']/$t['total'];
						array_push($t['journals'],$v);
					}
				}
			}				
		}
		
		$template = Template::find()->where("title = :t",["t"=>$t['title']])->one();
		
		if (!$istemplate && $template)
		{
			return true;
		}
		else
		{	
			if (!$template)
			{
				$template = new Template();
			}				
			$template->title = $t['title'];
			$template->json = json_encode($t);
			
			return $template->save();	
		}
	}
	
	public function getTags()
	{
		$models = Transaction::find()->all();
		$tags = [];
		foreach ($models as $m)
		{
			$ts = explode(",",$m->tags);
			foreach ($ts as $t)
			{	
				if (!in_array($t,$tags))
				{
					$tags[$t] = $t;
				}
			}	
		}
		return $tags;
	}
	
	public function getCashFlow()
	{
		$v = 0;
		foreach ($this->journals as $j)
		{			
			if ($j->account->exchangable && $j->isdel == 0) {
				if ($j->account->increaseon == 0 && $j->type == 0)
				{
					$v += $j->amount;	
				}
				else
				{
					$v -= $j->amount;
				}
			}			
		}
		
		if ($v != 0) {
			$v = $v>0?1:(-1);		
		}
		
		//return $this->total*($this->itemAlias('cashFlow',$this->type));
		return $this->total*($v);
	}
	
	public function itemAlias($list,$item = false,$bykey = false)
	{
		$lists = [
			'type'=>[							
						0=>Yii::t('app','General'),
						1=>Yii::t('app','Transfers'),
						2=>Yii::t('app','Receipt'),
						3=>Yii::t('app','Assets selling'),													
						4=>Yii::t('app','Assets buying'),							
						5=>Yii::t('app','Expenses'),
						6=>Yii::t('app','Payment'),							
						//7=>Yii::t('app','Revenues'),							
					],			
			'cashFlow'=>[							
						1=>0,
						2=>1,
						3=>1,													
						4=>-1,							
						5=>-1,
						6=>-1,							
						//7=>1,							
					],		
		];				
		
		if (isset($lists[$list]))
		{					
			if ($bykey)
			{				
				$nlist = [];
				foreach ($lists[$list] as $k=>$i)
				{
					$nlist[$i] = $k;
				}
				$list = $nlist;				
			}
			else
			{
				$list = $lists[$list];
			}
							
			if ($item !== false)
			{			
				return	(isset($list[$item])?$list[$item]:false);
			}
			else
			{
				return $list;	
			}			
		}
		else
		{
			return false;	
		}
	}
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJournals()
    {
        return $this->hasMany(Journal::className(), ['transaction_id' => 'id'])->where("isdel=0");
    }
    
    public function toHex($string)
	{		
		$hex = '';
		for ($i=0; $i<strlen($string); $i++){
			$ord = ord($string[$i]);
			$hexCode = dechex($ord);
			$hex .= '%'.substr('0'.$hexCode, -2);
		}
		return $hex;
	}
    
    public function toMoney($value = false)
    {
		if ($value)
		{
			$module = Yii::$app->getModule('cap');
			$value = $module->currency["symbol"].number_format($value,2,$module->currency["decimal_separator"],$module->currency["thousand_separator"]);
		}
		return $value;
	}
}
