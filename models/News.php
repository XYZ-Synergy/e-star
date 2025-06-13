<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string|null $created_at
 */
class News extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'created_at' => 'Created At',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$insert) { // Jei atnaujinama esama įraša
                // Tikrina, ar vartotojas turi teisę redaguoti šį konkretų straipsnį
                // Pvz., jei esate straipsnio autorius ARBA administratorius
                if (\Yii::$app->user->can('updateOwnNews', ['news' => $this]) || \Yii::$app->user->can('admin')) {
                    return true;
                }
                throw new \yii\web\ForbiddenHttpException('Jums neleidžiama redaguoti šio straipsnio.');
            }
            return true;
        }
        return false;
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            // Tikrina, ar vartotojas turi teisę trinti šį konkretų straipsnį
            // Pvz., jei esate straipsnio autorius ARBA administratorius
            if (\Yii::$app->user->can('deleteOwnNews', ['news' => $this]) || \Yii::$app->user->can('admin')) {
                return true;
            }
            throw new \yii\web\ForbiddenHttpException('Jums neleidžiama trinti šio straipsnio.');
        }
        return false;
    }

    // Galite pridėti pagalbinį metodą patikrinimui
    public function canView()
    {
        // Pvz., jei straipsnis yra publikuotas, arba jei esate autorius/adminas
        return $this->is_published || \Yii::$app->user->can('admin') || ($this->user_id === \Yii::$app->user->id);
    }

}
