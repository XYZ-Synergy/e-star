<?php

/** @var yii\web\View $this */

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
                <li>
                    <a class="nav-link ajax-link" href="#" data-url="<?= Url::to(['ajax/create-news']); ?>">Pridėti Naujieną</a>
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
                attachFormSubmitHandler();
            },
            error: function(xhr, status, error) {
                // Jei įvyko klaida, parodome pranešimą
                $('#dynamic-content-area').html('<p style="color: red;">Nepavyko įkelti turinio: ' + error + '</p>');
                console.error("AJAX Error:", status, error, xhr);
            }
        });
    }

    // Nauja funkcija formos pateikimo tvarkymui
    function attachFormSubmitHandler() {
        // Nustatome įvykio tvarkyklę AJAX formai, naudojant delegavimą
        // Delegavimas yra svarbus, nes forma bus dinamiškai įkelta
        $('#dynamic-content-area').off('submit', '.ajax-form'); // Pašaliname ankstesnius handlerius, kad nepasikartotų
        $('#dynamic-content-area').on('submit', '.ajax-form', function(e) {
            e.preventDefault(); // Sustabdome numatytąjį formos pateikimą

            var $form = $(this);
            var url = $form.attr('action'); // Gauname URL iš formos 'action' atributo
            var formData = $form.serialize(); // Serelizuojame formos duomenis į stringą

            // Išvalome ankstesnius klaidų pranešimus
            $form.find('.help-block').text('');
            $form.find('.has-error').removeClass('has-error');
            $('#form-messages').html('');


            $.ajax({
                url: url,
                type: 'POST', // Naudojame POST, kad išsiųstume formos duomenis
                data: formData, // Siunčiame formos duomenis
                dataType: 'json', // Tikimės JSON atsakymo iš serverio
                beforeSend: function() {
                    $('#form-messages').html('<p>Siunčiama...</p>');
                },
                success: function(response) {
                    if (response.success) {
                        $('#form-messages').html('<p style="color: green;">' + response.message + '</p>');
                        // Galite išvalyti formą po sėkmingo pateikimo
                        $form[0].reset();

                        // *********** NAUJA DALIS ČIA ***********
                        // Automatiškai iš naujo įkeliame naujienų sąrašą po sėkmingo pridėjimo
                        // Gauname URL iš "Naujienų" meniu punkto 'data-url' atributo
                        var newsUrl = $('.ajax-link[data-url="<?= Url::to(['ajax/get-news']); ?>"]').data('url');
                        loadContent(newsUrl); // Iškviečiame turinio įkėlimo funkciją
                        // ***************************************
                    } else {
                        // Parodome bendrą klaidos pranešimą
                        $('#form-messages').html('<p style="color: red;">' + response.message + '</p>');

                        // Parodome specifines validavimo klaidas prie laukelių
                        if (response.errors) {
                            $.each(response.errors, function(attribute, errors) {
                                var $input = $form.find('#news-' + attribute.toLowerCase()); // Randa laukelį pagal ID (pvz., #news-title)
                            if ($input.length) {
                                $input.closest('.form-group').addClass('has-error');
                                $input.siblings('.help-block').text(errors.join(', '));
                            }
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('#form-messages').html('<p style="color: red;">Klaida siunčiant duomenis: ' + error + '</p>');
                    console.error("AJAX Form Error:", status, error, xhr.responseText);
                }
            });
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
