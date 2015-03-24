<?php
namespace amilna\cap\models;

use creocoder\nestedsets\NestedSetsQueryBehavior;

class AccountCodeQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}

?>
