<?php
use yii\helpers\Html;
?>
<div class="comment-item" data-id="<?= $item->id ?>">
    <p>
        <strong>
            <?php // Jei turite user modelį ir ryšį:
            // echo Html::encode($item->user ? $item->user->username : 'Anonimas');
            echo 'Anonimas'; // Šiuo metu
            ?>
        </strong>
        <small class="text-muted"> (<?= Yii::$app->formatter->asDatetime($item->created_at); ?>)</small>
    </p>
    <p><?= Html::encode($item->content); ?></p>
</div>
<hr style="margin-top: 5px; margin-bottom: 5px;">
