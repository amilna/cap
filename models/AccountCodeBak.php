<?php

namespace amilna\cap\models;

use Yii;
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
class AccountCodeBak extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cap_account}}';
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


    public function beforeSave($insert)
    {
		if (parent::beforeSave($insert)) {	
			/*							
			$code = $this->db->createCommand("SELECT count(1)
					FROM ".AccountCode::tableName()."
					WHERE code = :code
					")->bindValues([":code"=>$this->code])->queryScalar();			
			
			if ($code > 0)
			{																				
				$al = $this->validate(["code"]);				
				$attr = $this->attributes;
				$attr["error"]["code"] = Yii::t("app",'Code "'.$attr["code"].'" has already been taken.');
				Yii::$app->controller->redirect(['//cap/account/create', 
					'attributes' => $attr
				]);	
				return false;			
			}
			*/
			
			$jml = $this->db->createCommand("SELECT count(1)
					FROM ".AccountCode::tableName()."
					WHERE isdel = 0
					")->queryScalar();
					
			if ($jml == 0)
			{
				$res = $this->db->createCommand("INSERT 
					INTO ".AccountCode::tableName()."
					(code,name,increaseon,id_left,id_right,id_level)
					VALUES (0,'".Yii::t("app","Base Account")."',0,1,2,1)
					")->execute();
			}			
			return true;
			
		} else {
			return false;
		}
	}
   
    public function afterSave($insert, $changedAttributes)
    {									
		$run = false;
		if ($insert || ($this->isdel == 1)) 
		{
			$run = true;
		}
		else
		{					
			$isk = false;
			foreach ($changedAttributes as $k=>$v)
			{
				if ($k == "parent_id")
				{
					$isk = true;	
				}	
			}
			
			if ($isk)
			{
				
				if ($this->parent_id != $changedAttributes["parent_id"])
				{
					$run = true;						
				}
			}
		}				
				
		if ($run)
		{		
			$jml = $this->db->createCommand("SELECT count(1)
					FROM ".AccountCode::tableName()."
					WHERE isdel = 0
					")->queryScalar();
			
			$pId = $this->parent_id;
			if ($pId == null)
			{					
				if ($jml > 1)
				{				
					$parent = $this->find()->where("parent_id is null AND id != :id",[":id"=>$this->id])->one();
					$pId = $parent->id;
					$res = $this->db->createCommand("UPDATE 
						".AccountCode::tableName()." SET
						parent_id = ".$pId."
						WHERE id = ".$this->id)->execute();							
				}
				else
				{
					$parent = $this->find()->where("parent_id is null")->one();
				}	
			}
			else
			{
				$parent = $this->findOne(["id"=>$pId]);	
			}
			
			$pLeft = $parent->id_left;
			$pRight = $parent->id_right;
			$pLevel = $parent->id_level;
			
			$left = ($this->id_left == null?-1:$this->id_left);					
			$right = ($this->id_right == null?0:$this->id_right);
			$level = ($this->id_level == null?0:$this->id_level);
			
			$opLeft = false;		
			$opRight = false;
			$opLevel = false;
			if (isset($changedAttributes["parent_id"]))
			{
				$oldparent = $this->findOne(["id"=>$changedAttributes["parent_id"]]);
				$opId = $oldparent->id;
				$opLeft = $oldparent->id_left;
				$opRight = $oldparent->id_right;
				$opLevel = $oldparent->id_level;				
			}				
			
			if ($this->isdel == 1 || !$opLeft)
			{
				$op = ($this->isdel == 1?"-":"+");				
			}
			else
			{		
				$op = ( ($pLeft - $left) < 0?"+":"-");
			}							
			
			$lmin = $left>0?min($pLeft,$left):$pLeft;			
			$lmax = $opLeft && $left>0 && $this->isdel == 1?max($pLeft,$left):false;					
			
			$rmin = $lmin;
			$rmax = $lmax;										
			
			$rLeft = 'id_left > '.$lmin.($lmax?' AND id_left <= '.$lmax:'');
			$rRight = 'id_right >= '.$rmin.($rmax?' AND id_right < '.$rmax:'');			
			$data = 'id_left >= '.$left.' AND id_left < '.$right;		
			
			if ($this->isdel == 1)
			{			
				$vRest = "2";
				$vChild = "1";				
				$vLevel = "1";	
			}
			else
			{										
				$vRest = "(".($right-$left+1).")";				
				$vChild = "(".$left.")+(".$pLeft.(($pLeft - $left) > 0 && $left > 0?$op.$vRest:"")."+1)";
				$vLevel = "(".$level.")+".($pLevel+1);
			}
			
			die("UPDATE 
					".AccountCode::tableName()." SET
					(id_left,id_right,id_level)
					= (
						case when ".$rLeft." and (".$data.") is not true then id_left".$op.$vRest." else												
							case when ".$data." then id_left-".$vChild." else													
								id_left						
							end	
						end,		
						case when ".$rRight." and (".$data.") is not true then id_right".$op.$vRest." else					
							case when ".$data." then id_right-".$vChild." else								
								id_right						
							end	
						end,					
						case when ".$data." then id_level-".$vLevel." else
							id_level							
						end
					)		
					WHERE isdel = 0");	
			
			$res = $this->db->createCommand("UPDATE 
					".AccountCode::tableName()." SET
					(id_left,id_right,id_level)
					= (
						case when ".$rLeft." and (".$data.") is not true then id_left".$op.$vRest." else												
							case when ".$data." then id_left-".$vChild." else													
								id_left						
							end	
						end,		
						case when ".$rRight." and (".$data.") is not true then id_right".$op.$vRest." else					
							case when ".$data." then id_right-".$vChild." else								
								id_right						
							end	
						end,					
						case when ".$data." then id_level-".$vLevel." else
							id_level							
						end
					)		
					WHERE isdel = 0")->execute();
			
			if ($this->isdel == 1)
			{
				$res = $this->db->createCommand("UPDATE 
					".AccountCode::tableName()." SET
					(id_left,id_right,id_level)
					= ((-1),0,0)		
					WHERE id = ".$this->id)->execute();							
			}
		}		
		parent::afterSave($insert, $changedAttributes);
	}

}
