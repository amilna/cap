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
            [['subject', 'reference','tags','title', 'remarks', 'time'], 'safe'],
            [['total'], 'number'],
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
            'total' => $this->total,
            'type' => $this->type,
            'time' => $this->time,
            'isdel' => $this->isdel,
        ]);

        $query->andFilterWhere(['like', 'lower(subject)', strtolower($this->subject)])
            ->andFilterWhere(['like', 'lower(title)', strtolower($this->title)])
            ->andFilterWhere(['like', 'lower(reference)', strtolower($this->reference)])
            ->andFilterWhere(['like', 'lower(tags)', strtolower($this->tags)])
            ->andFilterWhere(['like', 'lower(remarks)', strtolower($this->remarks)]);

        return $dataProvider;
    }
}
