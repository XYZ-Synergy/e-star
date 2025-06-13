<?php
use yii\widgets\LinkPager; // Naujas importas
?>
<div class="ajax-content">
    <h2>Paskutinės Naujienos FOO BAR Priemiestyje!</h2>

    <div id="news-list-container">
        <?php if (!empty($news)): ?>
            <?php foreach ($news as $item): ?>
                <div class="news-item" id="news-item-<?= $item->id ?>">
                    <h3><?= Html::encode($item->title); ?></h3>
                    <p><?= Html::encode($item->content); ?></p>

                    <?php if (!Yii::$app->user->isGuest): ?> <button class="btn btn-info btn-sm view-comments-btn" data-news-id="<?= $item->id ?>" data-url="<?= \yii\helpers\Url::to(['ajax/get-comments', 'news_id' => $item->id]); ?>">
                            Peržiūrėti Komentarus
                        </button>
                        <button class="btn btn-primary btn-sm like-news-btn" data-news-id="<?= $item->id ?>" data-url="<?= \yii\helpers\Url::to(['ajax/like-news', 'id' => $item->id]); ?>">
                            <i class="far fa-thumbs-up"></i> Patinka (<span id="likes-count-<?= $item->id ?>"><?= $item->likes_count ?></span>)
                        </button>

                        <?php if (Yii::$app->user->can('updateNews', ['news' => $item]) || Yii::$app->user->can('admin')): ?>
                            <?= Html::a('Redaguoti', ['news/update', 'id' => $item->id], ['class' => 'btn btn-warning btn-sm']); ?>
                        <?php endif; ?>

                        <?php if (Yii::$app->user->can('deleteNews', ['news' => $item]) || Yii::$app->user->can('admin')): ?>
                            <?= Html::a('Trinti', ['news/delete', 'id' => $item->id], [
                                'class' => 'btn btn-danger btn-sm',
                                'data' => [
                                    'confirm' => 'Ar tikrai norite ištrinti šį straipsnį?',
                                    'method' => 'post',
                                ],
                            ]); ?>
                        <?php endif; ?>

                    <?php else: ?>
                        <p><small>Prisijunkite, kad galėtumėte komentuoti ir spausti "Patinka".</small></p>
                    <?php endif; ?>

                    <div class="comments-area" id="comments-area-<?= $item->id ?>" style="display: none;"></div>
                    <hr>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p id="no-news-message">Šiuo metu naujienų nėra.</p>
        <?php endif; ?>
    </div>

    <?php
    // Puslapiavimo mygtukai
    echo LinkPager::widget([
        'pagination' => $pages,
        'options' => [
            'class' => 'pagination justify-content-center ajax-pagination', // Pridėta klasė
        ],
        'linkContainerOptions' => ['class' => 'page-item'],
        'linkOptions' => ['class' => 'page-link'],
        'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
    ]);
    ?>
</div>
