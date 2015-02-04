<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model amilna\cap\models\AccountCode */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Account Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-code-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            'name',            
            [
				'attribute'=>'parent',
				'value'=>($model->parent_id == null?'':$model->parent->code.' - ').$model->parent->name,
            ], 
            [
				'attribute'=>'increaseon',
				'value'=>$model->itemAlias('increaseon',$model->increaseon),
            ],            
            [
				'attribute'=>'isbalance',
				'value'=>$model->itemAlias('isbalance',$model->isbalance),
            ],
            //'isdel',
        ],
    ]) ?>

</div>
