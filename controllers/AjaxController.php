<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\News;

class AjaxController extends Controller
{
    /**
     * Veiksmas, kuris grąžins "Naujienų" turinį.
     * Naudojama AJAX užklausoms.
     *
     * @return string
     */
    public function actionGetNews()
    {
        // Išjungiame Yii layout'o naudojimą šiam veiksmui.
        // Tai svarbu, nes norime grąžinti tik turinį, be viso puslapio šablono.
        $this->layout = false;

        // Čia galėtumėte gauti naujienų duomenis iš duomenų bazės.
        // Pavyzdžiui:
        $news = News::find()->orderBy(['created_at' => SORT_DESC])->all();

        // Šiuo metu tiesiog grąžinsime paprastą HTML.
        return $this->render('news-content', [
            'news' => $news,
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
                return ['success' => true, 'message' => 'Straipsnis sėkmingai pridėtas!', 'newsItem' => $model->toArray()];
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
}
