<?php
foreach ($reviews as $review) {
    $time = strtotime($review['review_date']);
    $dateForView = date('d.m.Y', $time);
    ?>
    <div class="review-block">
        <h4 class="review-name"><?= $review['client_name'] ?><span class="review-date"><?= $dateForView ?></span></h4>
        <div class="review-text">   
            <?= $review['review_text'] ?>
        </div>
    </div>
    <a href="#" class="review-button" onclick="expandReviewBlock(this); return false;">Показать полностью</a>
    <?php
}
?>
