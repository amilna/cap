<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model amilna\cap\models\Transaction */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Transaction',
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transactions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
