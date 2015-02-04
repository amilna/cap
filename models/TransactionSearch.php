<?php

namespace amilna\cap\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use amilna\cap\models\Transaction;

/**
 * TransactionSearch represents the model behind the search form about `amilna\cap\models\Transaction`.
 */
class TransactionSearch extends Transaction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'isdel'], 'integer'],
            [['total','subject', 'reference','tags','title', 'remarks', 'time'], 'safe'],            
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Transaction::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,            
            'type' => $this->type,            
            'isdel' => $this->isdel,
        ]);
        
        if (!empty($this->total))
		{
			
			$total = explode(" ",$this->total);			
			if (count($total) == 2)
			{				
				$query->andFilterWhere([$total[0], "total", $total[1]]);					
			}
			elseif (count($total) > 2)
			{				
				$query->andFilterWhere(['>=', "total", $total[0]])
					->andFilterWhere(['<=', "total", $total[2]]);
			}
			else
			{
				$query->andFilterWhere(['=', "total", str_replace(["<",">","="],"",$total[0])]);	
			}	
		}	
        
        if (!empty($this->time))
		{
			
			$time = explode(" - ",$this->time);			
			if (count($time) > 1)
			{				
				$query->andFilterWhere(['>=', "concat('',time)", $time[0]])
					->andFilterWhere(['<=', "concat('',time)", $time[1]." 24:00:00"]);
			}
			else
			{
				if (substr($time[0],0,2) == "< " || substr($time[0],0,2) == "> " || substr($time[0],0,2) == "<=" || substr($time[0],0,2) == ">=") 
				{
					$query->andFilterWhere([str_replace(" ","",substr($time[0],0,2)), "concat('',time)", trim(substr($time[0],2))]);	
				}
				else
				{
					$query->andFilterWhere(['like', "concat('',time)", $time[0]]);	
				}
			}	
		}	

        $query->andFilterWhere(['like', 'lower(subject)', strtolower($this->subject)])
            ->andFilterWhere(['like', 'lower(title)', strtolower($this->title)])
            ->andFilterWhere(['like', 'lower(reference)', strtolower($this->reference)])
            ->andFilterWhere(['like', 'lower(tags)', strtolower($this->tags)])
            ->andFilterWhere(['like', 'lower(remarks)', strtolower($this->remarks)]);

        return $dataProvider;
    }
}
