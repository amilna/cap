<?php

namespace amilna\cap\models;

use Yii;
use amilna\cap\models\Journal;

/**
 * This is the model class for table "{{%cap_account}}".
 *
 * @property integer $id
 * @property integer $code
 * @property string $name
 * @property integer $parent_id
 * @property integer $increaseon
 * @property boolean $isbalance
 * @property integer $isdel
 *
 * @property CapJournal[] $capJournals
 * @property AccountCode $parent
 * @property AccountCode[] $accountCodes
 */
class AccountCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cap_account}}';
    }
	
	public static function find()
	{
		return parent::find()->where(['{{%cap_account}}.isdel' => 0]);
	}
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name', 'increaseon'], 'required'],
            [['code', 'parent_id', 'increaseon', 'isdel'], 'integer'],
            [['code'], 'unique'],
            [['isbalance'], 'boolean'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'parent_id' => Yii::t('app', 'Base Account'),
            'increaseon' => Yii::t('app', 'Increase On'),
            'isbalance' => Yii::t('app', 'Is Balance?'),
            'isdel' => Yii::t('app', 'Isdel'),
        ];
    }
    
    public function defaultScope()
    {      		
        return array(
            'condition'=>"parent_id is null",
        );
    }
    
    public function getMax()
    {                
        $query =  new \yii\db\Query;
        $query->select("sum(case when type = 0 then amount else 0 end) as max")
				->from(Journal::tableName())
				->where("isdel = 0");
				
		$row = $query->one();		
		$res = $row["max"];
        
        return ($res == null?0:$res);        
    }
    
    public function getSaldo()
    {                
        $query =  new \yii\db\Query;
        $query->select("sum(case when type = 0 then amount else amount*(-1) end) as saldo,sum(case when type = 0 then amount else 0 end) as debet,sum(case when type = 1 then amount else 0 end) as credit")
				->from(Journal::tableName())
				->where("account_id = ".$this->id." AND isdel = 0");
		
		$tot = 0;
		foreach ($this->accountCodes as $a)
		{
			$tot += $a->saldo;	
		}
		
		$row = $query->one();
		//$res = $row["debet"]-$row["credit"]+$tot;		
		$res = $row["saldo"]+$tot;
        
        return ($res == null?0:$res);        
    }
    
    public function getCredit()
    {                
        $query =  new \yii\db\Query;
        $query->select("sum(amount)")
				->from(Journal::tableName())
				->where("account_id = ".$this->id." AND type = 1 AND isdel = 0");
		
		$tot = 0;
		foreach ($this->accountCodes as $a)
		{
			$tot += $a->credit;	
		}
		
		$res = $query->scalar()+$tot;		
        
        return ($res == null?0:$res);        
    }
    
    public function getDebet()
    {                
        $query =  new \yii\db\Query;
        $query->select("sum(amount)")
				->from(Journal::tableName())
				->where("account_id = ".$this->id." AND type = 0 AND isdel = 0");
		
		$tot = 0;
		foreach ($this->accountCodes as $a)
		{
			$tot += $a->debet;	
		}
		
		$res = $query->scalar()+$tot;		
        
        return ($res == null?0:$res);        
    }
    
    public function getTotal()
    {                
        $query =  new \yii\db\Query;
        $query->select("sum(amount)")
				->from(Journal::tableName())
				->where("account_id = ".$this->id." AND isdel = 0");						
		
		$tot = 0;
		foreach ($this->accountCodes as $a)
		{
			$tot += $a->total;	
		}
		
		$res = $query->scalar()+$tot;		
        
        return ($res == null?0:$res);        
    }
    
    public function getChildTotal()
    {                                        
        $query =  new \yii\db\Query;
        $query->select("sum(amount)")
				->from(Journal::tableName()." as j")
				->leftJoin(AccountCode::tableName()." as a","a.id = j.account_id")
				->leftJoin(AccountCode::tableName()." as c","a.parent_id = c.id")
				->where("c.id = ".$this->id." AND isdel = 0");
				
		$res = $query->scalar();		
        
        return ($res == null?0:$res);        
    }
    
    public function	parents($id = false)
    {
		return AccountCode::findBySql("SELECT id,case when code < 0 then name else concat(code,' - ',name) end as name FROM ".AccountCode::tableName().($id?" WHERE id != :id":"")." order by name",($id?['id'=>$id]:[]))->all();		
	}	
	
	public function itemAlias($list,$item = false,$bykey = false)
	{
		$lists = [
			'increaseon'=>[
							0=>Yii::t('app','Debet'),
							1=>Yii::t('app','Credit'),
						],
			'isbalance'=>[
							false=>Yii::t('app','Profit/Loss'),
							true=>Yii::t('app','Balance'),
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
        return $this->hasMany(Journal::className(), ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(AccountCode::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountCodes()
    {
        return $this->hasMany(AccountCode::className(), ['parent_id' => 'id']);
    }
}
