<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="cap-default-index">
    
    <div class="jumbotron">
		<h2>Company Accounting Plugin</h2>
        <h1>Congratulations!</h1>
        

        <p class="lead">You have successfully installed Company Accounting Plugin for your Yii-powered application.</p>

        <p><?= Html::a(Yii::t('app','Get start to create an account'),['//cap/account/create'],["class"=>"btn btn-lg btn-success"])?></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Account</h2>

                <p>Formal record that represents, in words, money or other unit of measurement, certain resources, claims to such resources, transactions or other events that result in changes to those resources and claims.</p>

                <p><?= Html::a(Yii::t('app','Go to Account'),['//cap/account'],["class"=>"btn btn-primary"])?></p>
            </div>
            <div class="col-lg-4">
                <h2>Transaction</h2>

                <p>The act of transacting, especially a business agreement or exchange; event or condition recognized by an entry in the book ACCOUNT.</p>

                <p><?= Html::a(Yii::t('app','Go to Transaction'),['//cap/transaction'],["class"=>"btn btn-primary"])?></p>
            </div>
            <div class="col-lg-4">
                <h2>Journal</h2>

                <p>Any book containing original entries of daily financial transactions.</p>

                <p><?= Html::a(Yii::t('app','Go to Journal'),['//cap/journal'],["class"=>"btn btn-primary"])?></p>
            </div>
        </div>

    </div>
</div>
