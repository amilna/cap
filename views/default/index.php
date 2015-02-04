<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="cap-default-index">
    <h1>Company Accounting Plugin</h1>
    <p>
       Welcome to Company Accounting Plugin, there are 3 main parts of this module:
    </p>
    <ul>
		<li><?= Html::a(Yii::t('app','Account'),'account')?></li>
		<li><?= Html::a(Yii::t('app','Transaction'),'transaction')?></li>
		<li><?= Html::a(Yii::t('app','Journal'),'journal')?></li>
    </ul>
</div>
