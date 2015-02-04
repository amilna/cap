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
            [['remarks','time','title','subject','tags','transactionRemarks','account'], 'safe'],
            [['quantity', 'amount'], 'number'],
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
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'type' => $this->type,
            'isdel' => $this->isdel,
        ]);
		
		if (!empty($this->time))
		{
			
			$time = explode(" - ",$this->time);			
			if (count($time) > 1)
			{				
				$query->andFilterWhere(['>=', "concat('',{{%cap_transaction}}.time)", $time[0]])
					->andFilterWhere(['<=', "concat('',{{%cap_transaction}}.time)", $time[1]." 24:00:00"]);
			}
			else
			{
				$query->andFilterWhere(['like', "concat('',{{%cap_transaction}}.time)", $time[0]]);	
			}	
		}	
			
        $query->andFilterWhere(['like', 'lower({{%cap_journal}}.remarks)', strtolower($this->remarks)])			
			->andFilterWhere(['like', 'lower({{%cap_transaction}}.title)', strtolower($this->title)])
			->andFilterWhere(['like', 'lower({{%cap_transaction}}.subject)', strtolower($this->subject)])
			->andFilterWhere(['like', 'lower({{%cap_transaction}}.tags)', strtolower($this->tags)])
			->andFilterWhere(['like', 'lower({{%cap_transaction}}.remarks)', strtolower($this->transactionRemarks)])
			->andFilterWhere(['like', "lower(concat({{%cap_account}}.code,' - ',{{%cap_account}}.name))", strtolower($this->account)]);

        return $dataProvider;
    }
}
