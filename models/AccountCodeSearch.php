<?php

namespace amilna\cap\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use amilna\cap\models\AccountCode;

/**
 * AccountCodeSearch represents the model behind the search form about `amilna\cap\models\AccountCode`.
 */
class AccountCodeSearch extends AccountCode
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'code', 'parent_id', 'isdel'], 'integer'],
            [['name'], 'safe'],
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
        $query = AccountCode::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'code' => $this->code,
            'parent_id' => $this->parent_id,            
            'increaseon' => $this->increaseon,
            'isbalance' => $this->isbalance,
            'isdel' => $this->isdel,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);        

        return $dataProvider;
    }
}
