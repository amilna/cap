<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model amilna\cap\models\AccountCode */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('app','Account Code'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Account Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-code-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
