<?php

namespace app\services;

use app\models\Article;
use app\models\Reviews;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ArticleService 
{
    public function getArticlePhotos($articleID)
    {
        $sql = '
        SELECT width, height, name, sort_index, view_index 
        FROM article_photos ap
        WHERE ap.article_id = :article_id
        ORDER BY ap.sort_index';

        return Yii::$app->getDb()->createCommand($sql, ['article_id' => $articleID])->queryAll();
    }

    public function getArticleProperties($articleID)
    {
        $sql = '
        SELECT pg.name group_name, p.name property_name, tp.property_template_id, tp.property_id, 
                p.type_id property_type_id, u.name property_unit_name, p.kind_id property_kind_id, 
                ap.value property_value, av.value property_valid_value  
        FROM articles a
        JOIN article_property_templates_to_groups tg ON tg.article_group_id = a.group_id
        JOIN article_properties_to_templates tp ON tp.property_template_id = tg.property_template_id
        JOIN article_properties p ON p.id = tp.property_id
        JOIN article_property_groups pg ON pg.id = tp.property_group_id
        JOIN articles_to_properties ap ON ap.property_id = p.id AND ap.article_id = a.id 
        LEFT JOIN article_property_values av ON av.id = ap.value_id 
        LEFT JOIN article_property_units u ON u.id = p.unit_id
        WHERE a.id = :article_id AND (ap.value <> \'\' OR av.value IS NOT NULL) AND p.is_hidden <> 1 
        ORDER BY tp.property_sort_index, pg.name, tp.property_sort_index, p.name';

        return Yii::$app->getDb()->createCommand($sql, ['article_id' => $articleID])->queryAll();
    }

    public function getStoreQuantity($articleID)
    {
        $sql = '
        SELECT SUM(remains - reserved) quantity
        FROM article_remains
        WHERE article_id = :article_id';

        return intval(Yii::$app->getDb()->createCommand($sql, ['article_id' => $articleID])->queryScalar());
    }

    public function getReviews($articleID)
    {
        $sql = '
        SELECT client_name, review_date, review_text 
        FROM reviews 
        WHERE article_id = :article_id  
        ORDER BY review_date DESC';

        return Yii::$app->getDb()->createCommand($sql, ['article_id' => $articleID])->queryAll();
    }

    public function getArticleGrouping($articleID)
    {
        $sql = '
        SELECT ag.article_id, ag.value, p.name
        FROM article_grouping a 
        JOIN article_grouping ag ON ag.group_id = a.group_id AND ag.property_id = a.property_id
        JOIN article_properties p ON p.id = ag.property_id
        WHERE a.article_id = :article_id
        ORDER BY p.NAME, ag.value';

        return Yii::$app->getDb()->createCommand($sql, ['article_id' => $articleID])->queryAll();
    }

    public function insertReviews($articleID, $clientID, $clientName, $reviewDate, $reviewText)
    {
        $sql = '
        INSERT INTO reviews (id, article_id, client_id, client_name, review_date, review_text)  
        VALUES(NULL, :article_id, :client_id, :client_name, :review_date, :review_text)';
        $res = Yii::$app->getDb()->createCommand($sql)
            ->bindValue(':article_id', $articleID)
            ->bindValue(':client_id', $clientID)
            ->bindValue(':client_name', $clientName)
            ->bindValue(':review_date', $reviewDate)
            ->bindValue(':review_text', $reviewText)
            ->execute();
        return $res;
    }
}
