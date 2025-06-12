<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\News;
use app\models\Comment; // Naujas importas
use app\models\Notification;
use yii\web\Response;
use yii\filters\VerbFilter; // Reikalinga POST užklausoms
use yii\data\Pagination; // Naujas importas

class AjaxController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create-news' => ['POST', 'GET'],
                    'create-comment' => ['POST'], // Nurodome, kad tik POST metodas leidžiamas
                    'like-news' => ['POST'], // Leisti tik POST užklausas
                    'mark-notification-as-read' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Veiksmas, kuris grąžins "Naujienų" turinį.
     * Naudojama AJAX užklausoms.
     *
     * @param int $page Dabartinis puslapio numeris
     * @return string
     */
    public function actionGetNews($page = 1)
    {
        // Išjungiame Yii layout'o naudojimą šiam veiksmui.
        // Tai svarbu, nes norime grąžinti tik turinį, be viso puslapio šablono.
        $this->layout = false;

        // Čia galėtumėte gauti naujienų duomenis iš duomenų bazės.
        // Pavyzdžiui:
        $query = News::find()->orderBy(['created_at' => SORT_DESC]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 5]); // 5 straipsniai puslapyje

        $news = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Šiuo metu tiesiog grąžinsime paprastą HTML.
        return $this->render('news-content', [
            'news' => $news,
            'pages' => $pages, // Perduodame Pagination objektą į vaizdą
        ]);
    }

    /**
     * Veiksmas, kuris grąžins "Apie mus" turinį.
     * Naudojama AJAX užklausoms.
     *
     * @return string
     */
    public function actionGetAbout()
    {
        $this->layout = false;
        return $this->render('about-content');
    }

    // Galite pridėti daugiau panašių veiksmų (pvz., actionGetProducts, actionGetEvents ir t.t.)
    /**
     * Veiksmas naujo naujienų straipsnio pridėjimui per AJAX.
     * Grąžina JSON atsakymą.
     *
     * @return array
     */
    public function actionCreateNews()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON; // Nustatome atsakymo formatą į JSON

        $model = new News(); // Sukuriame naują News modelio instanciją

        // Tikriname, ar forma buvo pateikta (POST užklausa) ir ar duomenys gali būti įkelti į modelį
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                // Jei išsaugota sėkmingai, grąžiname sėkmės pranešimą ir naujai sukurtą straipsnį
                $itemHtml = $this->renderPartial('single-news-item', ['item' => $model]);

                return ['success' => true, 'message' => 'Straipsnis sėkmingai pridėtas!', 'newsItemHtml' => $itemHtml];
            } else {
                // Jei išsaugojimo metu įvyko klaida, grąžiname klaidų pranešimus
                return ['success' => false, 'message' => 'Nepavyko išsaugoti straipsnio.', 'errors' => $model->getErrors()];
            }
        }

        // Jei tai GET užklausa (formos atvaizdavimui), grąžiname tik formos HTML
        // Nustatome layout'ą į false, nes grąžiname tik formos dalį
        $this->layout = false;
        return $this->render('create-news-form', [
            'model' => $model,
        ]);
    }

    /**
     * Veiksmas, kuris grąžina komentarus konkrečiam naujienų straipsniui.
     * @param int $news_id Naujienų straipsnio ID
     * @return string
     */
    public function actionGetComments($news_id)
    {
        $this->layout = false;
        $comments = Comment::find()
            ->where(['news_id' => $news_id])
            ->orderBy(['created_at' => SORT_ASC]) // Komentarus rodoma nuo seniausių
            ->all();

        return $this->render('comments-list', [
            'comments' => $comments,
            'news_id' => $news_id // Perduodame news_id, kad formoje žinotume, kuriam straipsniui pridėti
        ]);
    }

    /**
     * Veiksmas naujo komentaro pridėjimui per AJAX.
     * @return array JSON atsakymas
     */
    public function actionCreateComment()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Comment();
        // Jei turite vartotojų sistemą ir vartotojas yra prisijungęs:
        // $model->user_id = \Yii::$app->user->id;

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                // Sėkmės atveju, taip pat atvaizduojame naujai sukurto komentaro HTML
                $commentHtml = $this->renderPartial('_single_comment_item', ['item' => $model]);

                return ['success' => true, 'message' => 'Komentaras sėkmingai pridėtas!', 'commentItemHtml' => $commentHtml];
            } else {
                return ['success' => false, 'message' => 'Nepavyko išsaugoti komentaro.', 'errors' => $model->getErrors()];
            }
        }
        // Šis else blokas dažniausiai nebus pasiektas, nes forma siunčiama per AJAX POST
        return ['success' => false, 'message' => 'Neteisinga užklausa.'];
    }

    /**
     * Veiksmas, skirtas pridėti "patinka" naujienų straipsniui.
     * @param int $id Naujienų straipsnio ID
     * @return array JSON atsakymas
     */
    public function actionLikeNews($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $news = News::findOne($id);
        if ($news) {
            // Realiame projekte, čia reikėtų patikrinti, ar vartotojas jau spaudė "patinka"
            // ir išvengti daugkartinių paspaudimų. Tam reikėtų atskiros lentelės.

            $news->updateCounters(['likes_count' => 1]); // Padidina likes_count 1 vienetu
            return ['success' => true, 'likesCount' => $news->likes_count];
        }

        return ['success' => false, 'message' => 'Straipsnis nerastas.'];
    }

    /**
     * Veiksmas, grąžinantis neperskaitytus pranešimus.
     * @return array JSON atsakymas
     */
    public function actionGetNotifications()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        // Reikia, kad vartotojas būtų prisijungęs:
        // if (\Yii::$app->user->isGuest) {
        //     return ['success' => false, 'message' => 'Neprisijungęs vartotojas.'];
        // }

        $userId = 1; // Pakeiskite į \Yii::$app->user->id realioje sistemoje
        $notifications = Notification::find()
            // ->where(['user_id' => $userId, 'is_read' => false])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $notificationData = [];
        foreach ($notifications as $notification) {
            $notificationData[] = [
                'id' => $notification->id,
                'message' => $notification->message,
                'created_at' => \Yii::$app->formatter->asRelativeTime($notification->created_at), // Atvaizdavimas "prieš 5 minutes"
                // Galite pridėti nuorodą, susijusią su pranešimu
            ];
        }

        return ['success' => true, 'notifications' => $notificationData, 'count' => count($notificationData)];
    }

    /**
     * Veiksmas, žymintis pranešimą kaip perskaitytą.
     * @param int $id Pranešimo ID
     * @return array JSON atsakymas
     */
    public function actionMarkNotificationAsRead($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $notification = Notification::findOne($id);

        // Reikia, kad vartotojas būtų prisijungęs ir pranešimas priklausytų jam
        // if ($notification && $notification->user_id == \Yii::$app->user->id) {

        if ($notification) { // Pavyzdžiui be vartotojo
            $notification->is_read = true;
            if ($notification->save()) {
                return ['success' => true];
            }
        }
        return ['success' => false, 'message' => 'Pranešimas nerastas arba neturi teisių.'];
    }
}
