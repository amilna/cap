<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model amilna\cap\models\AccountCode */

?>
<div class="account-code-view">

    <h1><?= Html::encode($model->name) ?></h1>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            'name',
            'parent_id',
            'increaseon',
            'isbalance',
            'isdel',
        ],
    ]) ?>

</div>
