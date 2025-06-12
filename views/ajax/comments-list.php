<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Comment;
?>

<div class="comments-section">
    <h4>Komentarai (<span id="comment-count"><?= count($comments); ?></span>)</h4>
    <div id="comments-container">
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <?= $this->render('_single_comment_item', ['item' => $comment]); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p id="no-comments-message">Komentarų dar nėra.</p>
        <?php endif; ?>
    </div>

    <hr>
    <h5>Pridėti Komentarą</h5>
    <?php $form = ActiveForm::begin([
        'id' => 'create-comment-ajax-form-' . $news_id, // Unikalus ID kiekvienai formai
        'action' => ['ajax/create-comment'],
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => [
            'class' => 'ajax-comment-form'
        ]
    ]); ?>

    <?= Html::hiddenInput('Comment[news_id]', $news_id); // Paslėptas laukas straipsnio ID ?>

    <?= $form->field(new Comment(), 'content')->textarea(['rows' => 3])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Siųsti Komentarą', ['class' => 'btn btn-success btn-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <div id="comment-form-messages-<?= $news_id ?>" class="mt-2"></div>
</div>
