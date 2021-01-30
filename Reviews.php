<?php

namespace app\models;

use DateTime;
use Yii;
use yii\db\ActiveRecord;
use app\models\BaseModel;

/**
 * Reviews is the model behind product reviews.
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $client_id
 * @property string $client_name
 * @property DateTime $review_date
 * @property string $review_text
 *
 */
class Reviews extends BaseModel
{
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['review_text'], 'required'],
            [['review_text'], 'string', 'max' => 1024],
            [['id', 'article_id', 'client_id', 'client_name', 'review_date', 'review_text'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'review_text' => 'Напишите Ваш отзыв',
        ];
    }

    public static function tableName()
    {
        return 'reviews';
    }
}
