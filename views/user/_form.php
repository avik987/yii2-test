<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(
            [
                    'options' => ['enctype' => 'multipart/form-data']
            ]

    ); ?>

    <?= $form->field($model, 'title') ?>
    <?= $form->field($model, 'bio')->textarea(['rows'=>6]) ?>
    <?= $form->field($model, 'file')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
