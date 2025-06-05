<div class="ajax-content">
    <h2>Paskutinės Naujienos FOO BAR Priemiestyje!</h2>

    <?php if (!empty($news)): ?>
        <?php foreach ($news as $item): ?>
            <div class="news-item">
                <h3><?= \yii\helpers\Html::encode($item->title); ?></h3>
                <small>Paskelbta: <?= \Yii::$app->formatter->asDatetime($item->created_at); ?></small>
                <p><?= \yii\helpers\Html::encode($item->content); ?></p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Šiuo metu naujienų nėra.</p>
    <?php endif; ?>
</div>
