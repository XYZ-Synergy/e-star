<?php
use yii\widgets\LinkPager; // Naujas importas
?>
<div class="ajax-content">
    <h2>Paskutinės Naujienos FOO BAR Priemiestyje!</h2>

    <div id="news-list-container">
        <?php if (!empty($news)): ?>
            <?php foreach ($news as $item): ?>
                <div class="news-item" id="news-item-<?= $item->id ?>">
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
