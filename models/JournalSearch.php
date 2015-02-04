<?php

namespace amilna\cap\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use amilna\cap\models\Journal;

/**
 * JournalSearch represents the model behind the search form about `amilna\cap\models\Journal`.
 */
class JournalSearch extends Journal
{    
    public $time;
    public $title;
    public $subject;
    public $tags;
    public $transactionRemarks;
    public $account;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'account_id', 'transaction_id', 'type', 'isdel'], 'integer'],
            [['quantity','remarks','time','title','subject','tags','transactionRemarks','account'], 'safe'],
            [[ 'amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
	
	public function attributeLabels()
    {
        return [            
            'code' => Yii::t('app', 'Account'),            
        ];
    }
	
	private function queryString($fields)
	{		
		$params = [];
		foreach ($fields as $afield)
		{
			$field = $afield[0];
			$tab = isset($afield[1])?$afield[1]:false;			
			if (!empty($this->$field))
			{				
				array_push($params,["like", "lower(".($tab?$tab.".":"").$field.")", strtolower($this->$field)]);
			}
		}	
		return $params;
	}	
	
	private function queryNumber($fields)
	{		
		$params = [];
		foreach ($fields as $afield)
		{
			$field = $afield[0];
			$tab = isset($afield[1])?$afield[1]:false;			
			if (!empty($this->$field))
			{				
				$number = explode(" ",$this->$field);			
				if (count($number) == 2)
				{									
					array_push($params,[$number[0], ($tab?$tab.".":"").$field, $number[1]]);	
				}
				elseif (count($number) > 2)
				{															
					array_push($params,['>=', ($tab?$tab.".":"").$field, $number[0]]);		
					array_push($params,['<=', ($tab?$tab.".":"").$field, $number[0]]);		
				}
				else
				{					
					array_push($params,['=', ($tab?$tab.".":"").$field, str_replace(["<",">","="],"",$number[0])]);		
				}									
			}
		}	
		return $params;
	}
	
	private function queryTime($fields)
	{		
		$params = [];
		foreach ($fields as $afield)
		{
			$field = $afield[0];
			$tab = isset($afield[1])?$afield[1]:false;			
			if (!empty($this->$field))
			{				
				$time = explode(" - ",$this->$field);			
				if (count($time) > 1)
				{								
					array_push($params,['>=', "concat('',".($tab?$tab.".":"").$field.")", $time[0]]);	
					array_push($params,['<=', "concat('',".($tab?$tab.".":"").$field.")", $time[1]." 24:00:00"]);
				}
				else
				{
					if (substr($time[0],0,2) == "< " || substr($time[0],0,2) == "> " || substr($time[0],0,2) == "<=" || substr($time[0],0,2) == ">=") 
					{					
						array_push($params,[str_replace(" ","",substr($time[0],0,2)), "concat('',".($tab?$tab.".":"").$field.")", trim(substr($time[0],2))]);
					}
					else
					{					
						array_push($params,['like', "concat('',".($tab?$tab.".":"").$field.")", $time[0]]);
					}
				}	
			}
		}	
		return $params;
	}
	
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Journal::find();
				
		$query->joinWith(['transaction','account']);
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes['time'] = [			
			'asc' => ['{{%cap_transaction}}.time' => SORT_ASC],
			'desc' => ['{{%cap_transaction}}.time' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['title'] = [			
			'asc' => ['{{%cap_transaction}}.title' => SORT_ASC],
			'desc' => ['{{%cap_transaction}}.title' => SORT_DESC],
		];
        
        $dataProvider->sort->attributes['subject'] = [			
			'asc' => ['{{%cap_transaction}}.subject' => SORT_ASC],
			'desc' => ['{{%cap_transaction}}.subject' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['tags'] = [			
			'asc' => ['{{%cap_transaction}}.tags' => SORT_ASC],
			'desc' => ['{{%cap_transaction}}.tags' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['transactionRemarks'] = [			
			'asc' => ['{{%cap_transaction}}.remarks' => SORT_ASC],
			'desc' => ['{{%cap_transaction}}.remarks' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['account'] = [			
			'asc' => ['{{%cap_account}}.code' => SORT_ASC],
			'desc' => ['{{%cap_account}}.code' => SORT_DESC],
		];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'account_id' => $this->account_id,
            'transaction_id' => $this->transaction_id,
            //'quantity' => $this->quantity,
            //'amount' => $this->amount,
            'type' => $this->type,
            'isdel' => $this->isdel,
        ]);				
				
		$params = self::queryNumber([['quantity']]);		
		foreach ($params as	$p)
		{
			$query->andFilterWhere($p);
		}		
		
		$params = self::queryTime([['time','{{%cap_transaction}}']]);				
		foreach ($params as	$p)
		{		
			$query->andFilterWhere($p);
		}
		
		$params = self::queryString([
			['remarks','{{%cap_journal}}'],
			['title','{{%cap_transaction}}'],
			['subject','{{%cap_transaction}}'],
			['tags','{{%cap_transaction}}'],
			['remarks','{{%cap_transaction}}'],			
		]);						
		foreach ($params as	$p)
		{		
			$query->andFilterWhere($p);
		}	
			
        $query->andFilterWhere(['like', 'lower({{%cap_transaction}}.remarks)', strtolower($this->transactionRemarks)])
			->andFilterWhere(['like', "lower(concat({{%cap_account}}.code,' - ',{{%cap_account}}.name))", strtolower($this->account)]);

        return $dataProvider;
    }
}
