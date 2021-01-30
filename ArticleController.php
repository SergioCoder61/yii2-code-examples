<?php

namespace app\controllers;

use app\models\Article;
use app\models\Reviews;
use app\services\ArticleService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ArticleController extends Controller
{
    public function actionIndex($id)
    {
        $article = Article::findOne(['id' => $id]);
        if ($article == null) {
            throw new NotFoundHttpException('Товар не найден');
        }

        $pathGroups = Yii::$app->catalog->getNativeOnlinePathGroups($article['group_id']);
        $articleService = new ArticleService;
        $articlePhotos = $articleService->getArticlePhotos($article['id']);
        $articleProperties = $articleService->getArticleProperties($article['id']);
        $marketingActions = Yii::$app->marketingAction->getArticleActions($article['id']);
        $storeQuantity = $articleService->getStoreQuantity($article['id']);
        $reviews = $articleService->getReviews($article['id']);
        $articleGrouping = $articleService->getArticleGrouping($article['id']);
        $articleSupplierOffers = Yii::$app->supplier->getArticleSupplierOffers([$article['id']]);
        $activeDraftDocument = Yii::$app->documentDraft->getActiveDocument();
        Yii::$app->personalOffer->addViewedArticle($id);

        return $this->render('index', [
            'article' => $article,
            'articlePhotos' => $articlePhotos,
            'articleProperties' => $articleProperties,
            'pathGroups' => $pathGroups,
            'marketingActions' => $marketingActions,
            'storeQuantity' => $storeQuantity,
            'articleSupplierOffers' => $articleSupplierOffers,
            'articleGrouping' => $articleGrouping,
            'activeDraftDocument' => $activeDraftDocument,
            'reviews' => $reviews
        ]);
    }

    public function actionReviews()
    {
        if (Yii::$app->request->isAjax) {
            $post = Yii:: $app->request->post();

            $client = Yii::$app->user->getIdentity()->client;

            $reviewText = strip_tags($post['review_text']);
            $articleID = $post['article_id'];
            $clientID = $client->Id;
            $clientName = $client->Name;
            $reviewDate = date('Y-m-d H:i:s');

            $articleService = new ArticleService;
            $res = $articleService->insertReviews($articleID, $clientID, $clientName, $reviewDate, $reviewText);
            if ($res) {
                $reviews = $articleService->getReviews($articleID);
                if (!empty($reviews)) {
                    $htmlReviews = Yii::$app->controller->renderPartial('_reviews-container', ['reviews' => $reviews]);
                    $result = [];
                    $result['htmlReviews'] = $htmlReviews;

                    return json_encode($result);
                }
            }
        }
    }
}
