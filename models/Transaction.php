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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cap_transaction}}';
    }
	
	public static function find()
	{
		return parent::find()->where(['{{%cap_transaction}}.isdel' => 0]);
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
		//return AccountCode::find()->with('accountCodes')->where("increaseon = :increaseon",['increaseon'=>$increaseon])->orderBy("code")->all();		
		$rows = (new \yii\db\Query())
				->select(["t.id AS id", "case when (t.code < 0) then t.name else concat (t.code,' - ',t.name) end as name,t.increaseon,t.isbalance"])
				->from(AccountCode::tableName()." AS t")
				->leftJoin(AccountCode::tableName()." AS a", "t.id = a.parent_id")
				->where(($increaseon?"t.increaseon = :increaseon AND ":"")."a.id is null AND not (t.id = ANY(array".json_encode($usedAccounts)."::integer[]))",$increaseon?['increaseon'=>$increaseon]:[])
				->orderBy("name")
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
			foreach ($p[$a] as $d)
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
					array_push($tags,$t);	
				}
			}	
		}
		return $tags;
	}
	
	public function itemAlias($list,$item = false,$bykey = false)
	{
		$lists = [
			'type'=>[							
							0=>Yii::t('app','Assets buying or transfers'),
							3=>Yii::t('app','Receipt'),
							4=>Yii::t('app','Reduction'),
							1=>Yii::t('app','Revenues'),
							2=>Yii::t('app','Expenses'),
							
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
        return $this->hasMany(Journal::className(), ['transaction_id' => 'id']);
    }
}
