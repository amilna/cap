<?php

namespace amilna\cap\models;

use Yii;

/**
 * This is the model class for table "{{%cap_journal}}".
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $transaction_id
 * @property string $remarks
 * @property string $quantity
 * @property double $amount
 * @property integer $type
 * @property integer $isdel
 *
 * @property CapAccount $account
 * @property CapTransaction $transaction
 */
class Journal extends \yii\db\ActiveRecord
{
    public $dynTableName = '{{%cap_journal}}';    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {        
        $mod = new Journal();             
        return $mod->dynTableName;
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'transaction_id', 'remarks', 'type'], 'required'],
            [['account_id', 'transaction_id', 'type', 'isdel'], 'integer'],
            [['remarks'], 'string'],
            [['quantity', 'amount'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'account_id' => Yii::t('app', 'Account ID'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'remarks' => Yii::t('app', 'Remarks'),
            'quantity' => Yii::t('app', 'Quantity'),
            'amount' => Yii::t('app', 'Amount'),
            'type' => Yii::t('app', 'Type'),
            'isdel' => Yii::t('app', 'Isdel'),
        ];
    }
	
	public function getQty()
    {
        return (($this->type == $this->account->increaseon?1:(-1))*$this->quantity);
    }
	
	public function getDebet()
    {
        return $this->type == 0?$this->amount:0;
    }
    
    public function getCredit()
    {
        return $this->type == 1?$this->amount:0;
    }   
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(AccountCode::className(), ['id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return $this->hasOne(Transaction::className(), ['id' => 'transaction_id']);
    }
}
