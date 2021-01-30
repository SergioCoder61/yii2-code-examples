<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="form-block">

<?php
if (Yii::$app->user->isGuest) {
    echo '<p>Отзывы могут оставлять только авторизованные пользователи </p>';
} else {
    ?>

<form>
    <div class="form-group" id="review-text-block">
        <label class="control-label" for="review_text">Напишите Ваш отзыв</label>
        <textarea id="reviews-review_text" class="form-control" rows="4" maxlength="1024"></textarea>
        <p class="help-block help-block-error" id="review-text-error"></p>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-primary" id="ajaxReviewButton" data-artid="<?= $article['id'] ?>">Отправить</button>
    </div>
</form>

    <?php
}
?>

    <div id="reviews-container">

<?php
if (!empty($reviews)) {
    $htmlReviews = Yii::$app->controller->renderPartial('_reviews-container', ['reviews' => $reviews]);
    echo $htmlReviews;
}
?>
    </div><!-- #reviews-container -->

</div>
