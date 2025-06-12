<div class="news-item" id="news-item-<?= $item->id ?>">
    <h3><?= \yii\helpers\Html::encode($item->title); ?></h3>
    <small>Paskelbta: <?= \Yii::$app->formatter->asDatetime($item->created_at); ?></small>
    <p><?= \yii\helpers\Html::encode($item->content); ?></p>

    <button class="btn btn-info btn-sm view-comments-btn" data-news-id="<?= $item->id ?>" data-url="<?= \yii\helpers\Url::to(['ajax/get-comments', 'news_id' => $item->id]); ?>">
        Peržiūrėti Komentarus
    </button>

    <button class="btn btn-primary btn-sm like-news-btn" data-news-id="<?= $item->id ?>" data-url="<?= \yii\helpers\Url::to(['ajax/like-news', 'id' => $item->id]); ?>">
        <i class="far fa-thumbs-up"></i> Patinka (<span id="likes-count-<?= $item->id ?>"><?= $item->likes_count ?></span>)
    </button>

    <div class="comments-area" id="comments-area-<?= $item->id ?>" style="display: none;"></div>
    <hr>
</div>
