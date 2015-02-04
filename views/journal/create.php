<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model amilna\cap\models\Journal */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Journal',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Journals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="journal-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
