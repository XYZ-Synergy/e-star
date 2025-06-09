<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="news-form-container">
<h3>Pridėti Naują Naujienų Straipsnį</h3>

<?php $form = ActiveForm::begin([
    'id' => 'create-news-ajax-form', // Unikalus ID formai
    'enableClientValidation' => false, // Kliento pusės validaciją darysime patys per jQuery
    'enableAjaxValidation' => false, // Ajax validaciją taip pat darysime patys
    'action' => ['ajax/create-news'], // Nurodome, į kokį kontrolerio veiksmą siųsti formą
    'options' => [
        'class' => 'ajax-form' // Papildoma klasė, kad būtų lengviau pasirinkti su jQuery
    ]
]); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

<div class="form-group">
<?= Html::submitButton('Paskelbti Naujieną', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

<div id="form-messages" class="mt-3"></div> </div>
