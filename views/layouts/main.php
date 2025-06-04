<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php
use yii\helpers\Url; // Įtraukiame Url pagalbinę klasę
?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a class="nav-link ajax-link active" href="#" data-url="<?= Url::to(['ajax/get-news']); ?>">Naujienos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ajax-link" href="#" data-url="<?= Url::to(['ajax/get-about']); ?>">Apie mus</a>
                </li>
                </ul>
        </div>
        <div class="col-md-9">
            <div id="dynamic-content-area">
                <h2>Sveiki atvykę į "E-Star" projektą!</h2>
                <p>Pasirinkite meniu punktą kairėje, kad pamatytumėte dinamiškai įkeliamą turinį.</p>
            </div>
        </div>
    </div>
</div>
<script>
<?php $this->beginBlock('scripts'); ?>
$(document).ready(function() {
    // Funkcija, kuri įkelia turinį per AJAX
    function loadContent(url) {
        $.ajax({
            url: url, // URL, į kurį siunčiame užklausą
            type: 'GET', // HTTP metodas (GET arba POST)
        beforeSend: function() {
            // Prieš siunčiant užklausą, galite parodyti įkėlimo animaciją
            $('#dynamic-content-area').html('<p>Kraunasi turinys...</p>');
        },
        success: function(response) {
            // Užklausai pavykus, įterpiame gautą atsakymą į nurodytą vietą
            $('#dynamic-content-area').html(response);
        },
        error: function(xhr, status, error) {
            // Jei įvyko klaida, parodome pranešimą
            $('#dynamic-content-area').html('<p style="color: red;">Nepavyko įkelti turinio: ' + error + '</p>');
            console.error("AJAX Error:", status, error, xhr);
        }
        });
    }

    // Įvykių tvarkyklė meniu punktams
    $('.ajax-link').on('click', function(e) {
        e.preventDefault(); // Sustabdome numatytąją nuorodos elgseną (neleidžiame puslapiui persikrauti)

    // Pašaliname "active" klasę nuo visų nuorodų
    $('.ajax-link').removeClass('active');
    // Pridedame "active" klasę prie paspaustos nuorodos
    $(this).addClass('active');

    // Gauname URL iš 'data-url' atributo
    var urlToLoad = $(this).data('url');
    // Iškviečiame turinio įkėlimo funkciją
    loadContent(urlToLoad);
    });

    // Papildomai, galite automatiškai įkelti naujienas puslapiui užsikrovus
    $('#dynamic-content-area').html('<p>Kraunasi pradinis turinys...</p>');
    loadContent($('.ajax-link.active').data('url'));
});
<?php $this->endBlock(); ?>
</script>

<?php $this->registerJs($this->blocks['scripts']); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
