<?php
use yii\helpers\Html;
?>
<div class="news-item-newly-added" data-id="<?= $item->id ?>">
    <h3><?= Html::encode($item->title); ?></h3>
    <small>Paskelbta: <?= Yii::$app->formatter->asDatetime($item->created_at); ?></small>
    <p><?= Html::encode($item->content); ?></p>
    <hr>
</div>
