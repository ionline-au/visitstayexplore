<?php
$args = array(
    'numberposts' => 3,
    'post_type' => 'product',
    'include' => array('759', '762', '763'),
    'orderby' => 'ID',
    'order' => 'ASC',
);

$products = get_posts($args);
?>
<div class="subscriptions">
    <div class="container">
        <div class="row">
            <?php foreach ($products as $k => $product) { ?>
                <div class="col-lg-4 col-12">
                    <div class="subsciption-block standard">
                        <div class="header">
                            <h2><?php echo $product->post_title ?></h2>
                        </div>
                        <div class="elementor-price-table__price">
                            <span class="elementor-price-table__currency">$</span><span class="elementor-price-table__integer-part"><?php echo get_post_meta($product->ID, '_sale_price', true) ?></span>
                            <div class="elementor-price-table__after-price">
                                <span class="elementor-price-table__fractional-part"></span>
                                <span class="elementor-price-table__period elementor-typo-excluded">/<?php echo get_post_meta($product->ID, '_subscription_period', true) ?></span>
                            </div>
                        </div>
                        <?php echo $product->post_content ?>
                        <div class="elementor-price-table__footer">
                            <a href="<?php echo site_url(); ?>/shop/?add-to-cart=<?php echo $product->ID; ?>" data-quantity="1" class="button product_type_subscription add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo $product->ID ?>" data-product_sku="" aria-label="Add “Basic” to your cart" rel="nofollow">Select Plan</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>