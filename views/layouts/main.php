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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    // Funkcija, kuri parodo įkrovimo indikatorių
    function showLoadingIndicator(targetElementId) {
        // Galite čia įdėti GIF paveikslėlį, pvz.:
        $('#' + targetElementId).html('<img src="./images/loading.gif" alt="Kraunasi..." style="width: 50px; height: 50px;">');
        // $('#' + targetElementId).html('<p style="text-align: center; color: #666;"><i class="fas fa-spinner fa-spin"></i> Kraunasi turinys...</p>');
        // Pastaba: ikonėlei reikės Font Awesome bibliotekos įkėlimo jūsų pagrindiniame layout'e.
    }
    // Funkcija, kuri įkelia turinį per AJAX
    function loadContent(url) {
        showLoadingIndicator('dynamic-content-area'); // Rodyti įkrovimo indikatorių
        $.ajax({
            url: url, // URL, į kurį siunčiame užklausą
            type: 'GET', // HTTP metodas (GET arba POST)
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
                // beforeSend: function() {
                //     $('#form-messages').html('<p>Siunčiama...</p>');
                // },
                success: function(response) {
                    if (response.success) {
                        $('#form-messages').html('<p style="color: green;">' + response.message + '</p>');
                        // Galite išvalyti formą po sėkmingo pateikimo
                        $form[0].reset();

                        // *********** NAUJA DALIS ČIA (papildoma animacija) ***********
                        // Galite paslėpti pranešimą po kelių sekundžių
                        setTimeout(function() {
                            $('#form-messages').html('');
                        }, 3000); // Paslėpti po 3 sekundžių
                        // *************************************************************

                        // *********** NAUJA DALIS ČIA (soft atnaujinimas) ***********
                        if (response.newsItemHtml) {
                            // Patikriname, ar šiuo metu rodomas naujienų sąrašas
                            if ($('#news-list-container').length) {
                                // Įterpiame naują elementą sąrašo viršuje
                                $('#news-list-container').prepend(response.newsItemHtml);

                                // Pašaliname "Šiuo metu naujienų nėra" pranešimą, jei jis yra
                                $('#no-news-message').remove();

                                // Galite pridėti trumpą animaciją naujai pridėtam elementui
                                $('#news-list-container').find('.news-item-newly-added').hide().fadeIn(800, function() {
                                    $(this).removeClass('news-item-newly-added'); // Pašalinti klasę, kad animacija nebepasikartotų
                                });
                            } else {
                                // Jei naujienų sąrašas nebuvo rodomas, tiesiog jį įkeliame
                                var newsUrl = $('.ajax-link[data-url="<?= Url::to(['ajax/get-news']); ?>"]').data('url');
                                loadContent(newsUrl);
                            }
                        }
                        // *************************************************************
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

    // NAUJA: Funkcija komentarų formos pateikimo tvarkymui
    function attachCommentFormSubmitHandler() {
        $('#dynamic-content-area').off('submit', '.ajax-comment-form'); // Atjungti esamus handlerius
        $('#dynamic-content-area').on('submit', '.ajax-comment-form', function(e) {
            e.preventDefault();

            var $form = $(this);
            var url = $form.attr('action');
            var formData = $form.serialize();
            var newsId = $form.find('input[name="Comment[news_id]"]').val(); // Gaminame news_id

            var $messagesDiv = $('#comment-form-messages-' + newsId);
            $messagesDiv.html('');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    $messagesDiv.html('<p>Siunčiama...</p>');
                },
                success: function(response) {
                    if (response.success) {
                        $messagesDiv.html('<p style="color: green;">' + response.message + '</p>');
                        $form[0].reset();

                        // Įterpti naują komentarą sąrašo viršuje
                        $('#comments-container').append(response.commentItemHtml); // Komentarus paprastai dedame į apačią

                        // Atnaujinti komentarų skaičių
                        var $commentCountSpan = $('#comment-count');
                        $commentCountSpan.text(parseInt($commentCountSpan.text()) + 1);

                        $('#no-comments-message').remove(); // Pašalinti "Komentarų dar nėra"

                        setTimeout(function() {
                            $messagesDiv.html('');
                        }, 3000);

                    } else {
                        $messagesDiv.html('<p style="color: red;">' + response.message + '</p>');
                        // Čia galite pridėti validavimo klaidų rodymą, kaip ir naujienų formoje
                        // pvz.: if (response.errors) { ... }
                    }
                },
                error: function(xhr, status, error) {
                    $messagesDiv.html('<p style="color: red;">Klaida siunčiant komentarą: ' + error + '</p>');
                    console.error("AJAX Comment Form Error:", status, error, xhr.responseText);
                }
            });
        });
    }

    // NAUJA: Įvykių tvarkyklė "Peržiūrėti Komentarus" mygtukams
    $('#dynamic-content-area').on('click', '.view-comments-btn', function() {
        var $button = $(this);
        var newsId = $button.data('news-id');
        var commentsUrl = $button.data('url');
        var $commentsArea = $('#comments-area-' + newsId);

        if ($commentsArea.is(':visible')) {
            $commentsArea.slideUp(function() {
                $commentsArea.html(''); // Išvalyti turinį, kai paslepiame
                $button.text('Peržiūrėti Komentarus');
            });
        } else {
            $commentsArea.html('<p style="text-align: center; color: #666;"><i class="fas fa-spinner fa-spin"></i> Kraunasi komentarai...</p>');
            $commentsArea.slideDown(); // Rodome kol kraunasi
            $.ajax({
                url: commentsUrl,
                type: 'GET',
                success: function(response) {
                    $commentsArea.html(response);
                    $button.text('Slėpti Komentarus');
                    attachCommentFormSubmitHandler(); // Prisegti handlerius naujai įkeltai komentarų formai
                },
                error: function(xhr, status, error) {
                    $commentsArea.html('<p style="color: red;">Nepavyko įkelti komentarų: ' + error + '</p>');
                    console.error("AJAX Comments Error:", status, error, xhr);
                }
            });
        }
    });

    // NAUJA: Įvykių tvarkyklė "Patinka" mygtukams
    $('#dynamic-content-area').on('click', '.like-news-btn', function() {
        var $button = $(this);
        var newsId = $button.data('news-id');
        var likeUrl = $button.data('url');

        $button.prop('disabled', true); // Išjungti mygtuką, kol vyksta užklausa

        $.ajax({
            url: likeUrl,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#likes-count-' + newsId).text(response.likesCount); // Atnaujinti skaičių
                } else {
                    console.error('Klaida spaudžiant Patinka:', response.message);
                }
                $button.prop('disabled', false); // Įjungti mygtuką atgal
            },
            error: function(xhr, status, error) {
                console.error("AJAX Like Error:", status, error, xhr.responseText);
                $button.prop('disabled', false); // Įjungti mygtuką atgal
            }
        });
    });

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
