<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model amilna\cap\models\Transaction */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Transaction',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transactions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="transaction-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
