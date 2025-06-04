<?php

namespace app\controllers;

use yii\web\Controller;

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
        // $news = YourNewsModel::find()->all();

        // Šiuo metu tiesiog grąžinsime paprastą HTML.
        return $this->render('news-content');
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
}
