<?php

namespace amilna\cap\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;
//use amilna\cap\models\Journal;

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
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                //'treeAttribute' => 'tree',
                'leftAttribute' => 'id_left',
                'rightAttribute' => 'id_right',
                'depthAttribute' => 'id_level',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
	
    public static function find()
    {
        return new AccountCodeQuery(get_called_class());
    }
    
    public $dynTableName = '{{%cap_account}}';    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {        
        $mod = new AccountCode();               
        return $mod->dynTableName;
    }					
	 	
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name', 'increaseon'], 'required'],
            [['code', 'parent_id', 'increaseon', 'id_left','id_right','id_level','isdel'], 'integer'],
            [['code'], 'unique'],
            [['isbalance','exchangable'], 'boolean'],
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
            'exchangable' => Yii::t('app', 'Is Exchangable?'),
            'isdel' => Yii::t('app', 'Isdel'),
        ];
    }
    
    public function getMax()
    {                        		
		$res = $this->db->createCommand("SELECT 
					sum(case when type = 0 then amount else 0 end) as max 
					FROM ".Journal::tableName()." 
					WHERE isdel = :isdel")
					->bindValues(["isdel"=>0])->queryScalar();		
        
        return ($res == null?0:$res);        
    }

    public function getSisa()
    {                        		
		
		$res = $this->db->createCommand("SELECT 
					sum(case when type = 0 then amount else amount*(-1) end) as saldo,sum(case when type = 0 then amount else 0 end) as debet,sum(case when type = 1 then amount else 0 end) as credit 
					FROM ".Journal::tableName()." 
					WHERE account_id = :aid AND isdel = :isdel")
					->bindValues(["isdel"=>0,"aid"=>$this->id])->queryScalar();						
		
		$tot = 0;
		foreach ($this->accountCodes as $a)
		{
			$tot += $a->saldo;	
		}
				
		$res += $tot;
        
        return ($res == null?0:$res);        
    }
    
    public function getSaldo()
    {                        				        
        return $this->db->createCommand("SELECT 
					sum(case when j.type = 0 then amount else j.amount*(-1) end) as saldo 
					FROM ".Journal::tableName()." as j
					LEFT JOIN ".$this->tableName()." as a on j.account_id = a.id
					LEFT JOIN ".Transaction::tableName()." as t on j.transaction_id = t.id
					WHERE (a.id_left >= :lid AND a.id_right <= :rid) AND j.isdel = 0 AND t.isdel = 0")
					->bindValues(["lid"=>$this->id_left,"rid"=>$this->id_right])->queryScalar();                
    }
    
    
    public function search()
    {
		return new AccountCodeSearch();
	}        
	
	public function itemAlias($list,$item = false,$bykey = false)
	{
		$lists = [
			'increaseon'=>[
							0=>Yii::t('app','Debet'),
							1=>Yii::t('app','Credit'),
						],
			'isbalance'=>[
							0=>Yii::t('app','Profit/Loss'),
							1=>Yii::t('app','Balance'),
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
	
	public function afterSave($insert, $changedAttributes)
    {								 					
		if ($this->isdel == 1)
		{
			$this->afterDelete();
			
			$res = $this->db->createCommand("UPDATE 
				".$this->tableName()."
				SET parent_id = ".$this->parent_id."
				WHERE parent_id = ".$this->id."")->execute();						
			
			$res = $this->db->createCommand("UPDATE 
				".$this->tableName()."
				SET (id_left,id_right,id_level,parent_id) = (-1,0,0,null)
				WHERE id = ".$this->id."")->execute();									
		}
				
		parent::afterSave($insert, $changedAttributes);
	}
	
	public function re_arrange()
	{		
		$this->id_left = -1;
		$this->id_right = 1;
		$this->id_level = 0;
		
		$this->save();
		
		if ($this->parent_id == null)
		{
			if (!$this->isRoot())
			{
				$this->makeRoot();				
			}
			
		}
		else
		{
			$parent = $this->findOne($this->parent_id);
			$this->prependTo($parent);													
		}
						
		$childs = $this->AccountCodes;		
		$res = true;			
		foreach ($childs as $m)
		{										
			$res = $m->re_arrange();			
		}						
		
		return ($res == false?false:$res);
	}		 
	

}
