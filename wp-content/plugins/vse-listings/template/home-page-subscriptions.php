<?php
$args = array(
    'numberposts' => 3,
    'post_type' => 'product',
    'include' => array('759', '762', '763'),
    'orderby' => 'ID',
    'order' => 'ASC',
);

$products = get_posts($args);
//echo "<pre>"; print_r($products); die;
?>
<div class="subscriptions">
    <div class="container">
        <div class="row">
            <?php foreach ($products as $k => $product) { ?>
                <div class="col-lg-4 col-12">
                    <div class="subsciption-block <?php echo ($k == '1') ? 'standard' : ''; ?>">
                        <div class="header">
                            <h2><?php echo $product->post_title ?></h2>
                        </div>
                        <div class="elementor-price-table__price">
                            <div class="elementor-price-table__original-price elementor-typo-excluded">
                                <span class="elementor-price-table__currency">$</span><?php echo get_post_meta($product->ID, '_regular_price', true) ?>
                            </div>
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
            <!-- <div class="col-lg-4 col-12">
            <div class="subsciption-block standard">
                 <div class="header">
                   <h2>Standard</h2>
                   <div class="elementor-price-table__ribbon">
                      <div class="elementor-price-table__ribbon-inner">
                        Popular
                      </div>
                    </div>
                 </div>
                 <div class="elementor-price-table__price">
                      <div class="elementor-price-table__original-price elementor-typo-excluded">
                          <span class="elementor-price-table__currency">$</span>480</div>
                          <span class="elementor-price-table__currency">$</span><span class="elementor-price-table__integer-part">240</span>
                          <div class="elementor-price-table__after-price">
                              <span class="elementor-price-table__fractional-part"></span>
                              <span class="elementor-price-table__period elementor-typo-excluded">/year</span>
                          </div>
                    </div>
                    <ul class="elementor-price-table__features-list">
                                              <li class="elementor-repeater-item-3556ebc">
                          <div class="elementor-price-table__feature-inner">
                              <span>1 Year Subscription</span>
                                                  </div>
                                    </li>
                                              <li class="elementor-repeater-item-362c252">
                          <div class="elementor-price-table__feature-inner">
                          <span>Unlimited Categories</span></div>
                       </li>
                        <li class="elementor-repeater-item-90d89b2">
                            <div class="elementor-price-table__feature-inner">
                              <span>Monthly Payments not available during promotion period</span>
                            </div>
                        </li>
                        <li class="elementor-repeater-item-90d89b2">
                            <div class="elementor-price-table__feature-inner">
                              <span>Monthly Payments not available during promotion period</span>
                            </div>
                        </li>
                        <li class="elementor-repeater-item-90d89b2">
                            <div class="elementor-price-table__feature-inner">
                              <span>Monthly Payments not available during promotion period</span>
                            </div>
                        </li>
                        <li class="elementor-repeater-item-90d89b2">
                            <div class="elementor-price-table__feature-inner">
                              <span>Monthly Payments not available during promotion period</span>
                            </div>
                        </li>
                                      </ul>
                    <div class="elementor-price-table__footer">
                                              <a class="elementor-price-table__button elementor-button elementor-size-md" href="#">Select Plan</a>
                                      </div>
                 </div>
            </div>
            <div class="col-lg-4 col-12">
            <div class="subsciption-block">
                 <div class="header">
                   <h2>Pro</h2>
                 </div>
                 <div class="elementor-price-table__price">
                      <div class="elementor-price-table__original-price elementor-typo-excluded">
                          <span class="elementor-price-table__currency">$</span>480</div>
                          <span class="elementor-price-table__currency">$</span><span class="elementor-price-table__integer-part">240</span>
                          <div class="elementor-price-table__after-price">
                              <span class="elementor-price-table__fractional-part"></span>
                              <span class="elementor-price-table__period elementor-typo-excluded">/year</span>
                          </div>
                    </div>
                    <ul class="elementor-price-table__features-list">
                                              <li class="elementor-repeater-item-3556ebc">
                          <div class="elementor-price-table__feature-inner">
                              <span>1 Year Subscription</span>
                                                  </div>
                                    </li>
                                              <li class="elementor-repeater-item-362c252">
                          <div class="elementor-price-table__feature-inner">
                          <span>Unlimited Categories</span></div>
                       </li>
                        <li class="elementor-repeater-item-90d89b2">
                            <div class="elementor-price-table__feature-inner">
                              <span>Monthly Payments not available during promotion period</span>
                            </div>
                        </li>
                        <li class="elementor-repeater-item-90d89b2">
                            <div class="elementor-price-table__feature-inner">
                              <span>Monthly Payments not available during promotion period</span>
                            </div>
                        </li>
                        <li class="elementor-repeater-item-90d89b2">
                            <div class="elementor-price-table__feature-inner">
                              <span>Monthly Payments not available during promotion period</span>
                            </div>
                        </li>
                        <li class="elementor-repeater-item-90d89b2">
                            <div class="elementor-price-table__feature-inner">
                              <span>Monthly Payments not available during promotion period</span>
                            </div>
                        </li>
                                      </ul>
                    <div class="elementor-price-table__footer">
                                              <a class="elementor-price-table__button elementor-button elementor-size-md" href="#">Select Plan</a>
                                      </div>
                 </div>
            </div> -->
        </div>
    </div>
</div>