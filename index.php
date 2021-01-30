<?php

/* @var $this yii\web\View */

use ultimate\helpers\FormatHelper;
use app\services\CartService;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\Tabs;
use app\assets\MagnificPopupAsset;

MagnificPopupAsset::register($this);

$this->title = $article['name'];

$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => ['/catalog/index']];
foreach ($pathGroups as $pathGroup) {
    $this->params['breadcrumbs'][] = ['label' => $pathGroup['name'], 'url' => ['/catalog/group', 'id' => $pathGroup['id']]];
}
// $this->params['breadcrumbs'][] = $this->title;

$articleImageUrl = Yii::getAlias('@articleImageUrl');
$articleImage100Url = Yii::getAlias('@articleImage100Url');

$buttonMessage = '';
if (!empty($activeDraftDocument)) {
    foreach ($activeDraftDocument->articles as $a) {
        if ($a->id == $article['id']) {
            $buttonMessage = "в {$activeDraftDocument->id} уже {$a->quantity} шт.";
            break;
        }
    }
}
?>
<div class="title-block-blue">
    <div class="title-wrapper-blue">
        <?= Breadcrumbs::widget([
            'links' => $this->params['breadcrumbs']
        ]) ?>

        <h1 class="title-blue"><?= Html::encode($this->title) ?></h1>
    </div>
</div><!-- .title-block-blue  -->

<div class="article-section">
    <div class="row">
        <div class="photos">

            <?php
            $num = sizeof($articlePhotos);
            if ($num > 0) {
                $counter = 0;

                echo '<div>';
                echo "<div id='magnific-block'>";
                foreach ($articlePhotos as $photo) {
                    if ($counter == 0) {
                        echo "<a class='photo' id='big-photo-link' href='{$articleImageUrl}/{$article['id']}.jpg'><img id='big-photo-image' src='{$articleImageUrl}/{$article['id']}.jpg'></a>";
                    }
                    if ($counter > 0 && $num > 1) {
                        echo "<a class='photo hide' href='{$articleImageUrl}/{$article['id']}_{$photo['view_index']}.jpg'></a>";
                    }

                    $counter++;
                }
                echo '</div>';

                echo '<div class="ident-code">Код товара: <span>' . $article['id'] . '</span></div>';

                if (Yii::$app->user->isGuest) {
                    echo '<p class="article-suppliers-info">Необходимо авторизоваться чтобы увидеть информацию по ценам и доступности товара.</p>';
                } else {
                    if (empty($articleSupplierOffers) || sizeof($articleSupplierOffers['offers']) == 0) {
                        echo '<p class="article-suppliers-info">Нет в наличии.</p>';
                    } else {
                        $showFactor = false;
                        $canSelectSuppliers = false;

                        // if any factor != 1
                        $canSelectSuppliers = $articleSupplierOffers['canSelectSuppliers'];
                        foreach ($articleSupplierOffers['offers'] as $articleSupplierOffer) {
                            if ($articleSupplierOffer['factor'] != 1) {
                                $showFactor = true;
                                break;
                            }
                        }

                        echo '<table class="article-suppliers">';
                        echo '<tr>';
                        if ($canSelectSuppliers) {
                            echo '<th>Поставщик</th>';
                        }
                        if ($showFactor) {
                            echo '<th>Кратность</th>';
                        }
                        echo '<th class="supplier-price">Цена</th>';
                        echo '<th>Дата поставки</th>';
                        echo '<th>&nbsp;</th>';
                        echo '<th>&nbsp;</th>';
                        echo '</tr>';

                        foreach ($articleSupplierOffers['offers'] as $articleSupplierOffer) {
                            $buttonMessage = '';
                            if (!empty($activeDraftDocument)) {
                                foreach ($activeDraftDocument->articles as $a) {
                                    if ($a->id == $article['id']
                                        && $a->supplierId == $articleSupplierOffer['supplier_id']
                                        && $a->quantity > 0
                                    ) {
                                        $buttonMessage = "в {$activeDraftDocument->id} уже {$a->quantity} шт.";
                                        break;
                                    }
                                }
                            }

                            $timestamp = strtotime($articleSupplierOffer['shipment_date']);
                            $shipmentDate = date('d.m.Y', $timestamp);

                            echo '<tr>';
                            if ($canSelectSuppliers) {
                                echo "<td>{$articleSupplierOffer['supplier_name']}</td>";
                            }
                            if ($showFactor) {
                                echo "<td class='factor'>{$articleSupplierOffer['factor']}</td>";
                            }
                            echo '<td class="supplier-price">' . FormatHelper::money($articleSupplierOffer['price']) . ' &#x20bd;</td>';
                            echo "<td>{$shipmentDate}</td>";
                            echo "<td class='quantity'><input name='quantity' type='number' step='{$articleSupplierOffer['factor']}' min='{$articleSupplierOffer['factor']}' value='{$articleSupplierOffer['factor']}'></td>";
                            echo '<td>';
                            echo '<button class="btn to-cart-button addDraftButton" style="width: 174px;" data-article-id="' . $article['id'] . '" data-supplier-id="' . $articleSupplierOffer['supplier_id'] . '">Добавить в черновик<br/>';
                            echo '<span style="font-size: 12px;">' . $buttonMessage . '</span>';
                            echo '</button>';
                            echo '</td>';

                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                }

                echo '</div>';
                echo '<div class="clear"></div>';

                $counter = 0;

                if ($num > 1 && $num < 6) {
                    foreach ($articlePhotos as $photo) {
                        if ($counter == 0) {
                            echo "<div class='thumbnails'>";
                            echo "<a href='{$articleImageUrl}/{$article['id']}_{$photo['view_index']}.jpg'><img src='{$articleImage100Url}/{$article['id']}_{$photo['view_index']}.jpg'></a>";
                        }
                        if ($counter > 0) {
                            echo "<a href='{$articleImageUrl}/{$article['id']}_{$photo['view_index']}.jpg'><img src='{$articleImage100Url}/{$article['id']}_{$photo['view_index']}.jpg'></a>";
                        }
                        if ($counter >= 0 && $counter == $num - 1) {
                            echo '</div>';
                        }
                        $counter++;
                    }
                }
                if ($num > 5) {
                    ?>
            <div class="">
                <div class="carousel shadow">
                    <div class="carousel-button-left"><a href="#"></a></div>
                        <div class="carousel-button-right"><a href="#"></a></div>
                            <div class="carousel-wrapper">
                                <div class="carousel-items">
                    <?php
                    foreach ($articlePhotos as $photo) {
                        echo "<div class='carousel-block'>";
                        echo "<img data-href='{$articleImageUrl}/{$article['id']}_{$photo['view_index']}.jpg' src='{$articleImage100Url}/{$article['id']}_{$photo['view_index']}.jpg' onclick='dataHrefToBigPhoto(this); return false;'>";
                        echo '</div>';
                    }
                    ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            }
            ?>

            <div class="article-section" style="padding-right: 30px;">
                <div class="row">
                    <div class="tabs-block">

                        <?php
                        echo Tabs::widget([
                            'items' => [
                                [
                                    'label' => 'Характеристики',
                                    'content' => $this->render('_properties', ['articleProperties' => $articleProperties,], true),
                                    'active' => true
                                ],
                            ]
                        ]);
                        ?>
                    </div>
                </div><!-- .row -->
                <div id="gif-wrapper" style="display: none; position: absolute; top: 30%; left: 30%; z-index: 50;"><img src="/images/progress-bar-gif.gif" alt=""/></div>

            </div>

        </div>
    </div>
</div>
