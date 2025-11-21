<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

do_action('woocommerce_before_customer_login_form'); ?>
<!-- <div class="breadcrumb-login">

	Log in or Register
</div> -->
<?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) : ?>
<style>
    .woocommerce .col2-set .col-2, .woocommerce-page .col2-set .col-2 {
        float: none !important;
        width: 48% !important;
        margin: 0 auto !important;
    }

    @media (max-width: 767px) {
        .woocommerce .col2-set .col-2, .woocommerce-page .col2-set .col-2 {
            float: none !important;
            width: 100% !important;
            margin: 0 auto !important;
        }

        element.style {
        }
        .login-right-content .sign-register {
            margin-top: 220px;
        }
    }

    @media (max-width: 480px) {
        .woocommerce .col2-set .col-2, .woocommerce-page .col2-set .col-2 {
            float: none !important;
            width: 100% !important;
            margin: 0 auto !important;
        }

        element.style {
        }
        .login-right-content .sign-register {
            margin-top: 220px;
        }
    }


</style>
<div class="container">
    <div class="u-columns col2-set" id="customer_login">
        <div class="u-column2 col-2">
            <div class="login-right-content">
                <?php endif; ?>
                <div class="login" id="login-form">
                    <h2><?php esc_html_e('Login', 'woocommerce'); ?></h2>
                    <form class="woocommerce-form woocommerce-form-login login" method="post">
                        <?php do_action('woocommerce_login_form_start'); ?>
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <!-- <label for="username"><?php //esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label> -->
                            <i class="fa fa-user"></i>
                            <input type="text" placeholder="Username" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"/><?php // @codingStandardsIgnoreLine ?>
                        </p>
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <!-- <label for="password"><?php //esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label> -->
                            <i class="fa fa-lock"></i>
                            <input class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Password" type="password" name="password" id="password" autocomplete="current-password"/>
                        </p>
                        <?php // echo do_shortcode('[bws_google_captcha]') ?>

                        <?php do_action('woocommerce_login_form'); ?>
                        <p class="form-row">
                            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                                <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever"/>
                                <span><?php esc_html_e('Remember me', 'woocommerce'); ?></span> </label>
                        <p class="woocommerce-LostPassword lost_password">
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Lost your password?', 'woocommerce'); ?></a>
                        </p>
                        <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                        <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e('Log in', 'woocommerce'); ?>"><?php esc_html_e('Log in', 'woocommerce'); ?></button>
                        </p>
                        <?php do_action('woocommerce_login_form_end'); ?>
                    </form>
                    <div class="sign-register">
                        <a href="#" onclick="myRegisterForm()">Sign Up to Register</a>
                    </div>
                </div>
                <?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) : ?>
                <div class="register" id="register" style="display:none;">
                    <!-- <div class="u-column2 col-2"> -->
                    <h2><?php esc_html_e('Sign Up to Register', 'woocommerce'); ?></h2>
                    <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action('woocommerce_register_form_tag'); ?> >
                        <?php do_action('woocommerce_register_form_start'); ?>

                        <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>
                            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                <i class="fa fa-user"></i>
                                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Username" name="username" id="reg_username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"/><?php // @codingStandardsIgnoreLine ?>
                            </p>
                        <?php endif; ?>
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <i class="fa fa-envelope"></i>
                            <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Email Address" name="email" id="reg_email" autocomplete="email" value="<?php echo (!empty($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>"/><?php // @codingStandardsIgnoreLine ?>
                        </p>
                        <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
                            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                <label for="reg_password"><?php esc_html_e('Password', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
                                <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password"/>
                            </p>
                            <? phpelse : ?>
                            <p><?php esc_html_e('A link to set a new password will be sent to your email address.', 'woocommerce'); ?></p>
                        <?php endif; ?>

                        <?php do_action('woocommerce_register_form'); ?>
                        <?php // echo do_shortcode('[bws_google_captcha]') ?>
                        <p class="woocommerce-form-row form-row">
                            <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                            <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e('Register', 'woocommerce'); ?>"><?php esc_html_e('Register', 'woocommerce'); ?></button>
                        </p>
                        <?php do_action('woocommerce_register_form_end'); ?>
                    </form>
                    <!-- </div> -->
                    <div class="sign-register">
                        <a href="#" onclick="myRegisterForm()">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="container">
    <?php do_action('woocommerce_after_customer_login_form'); ?>
</div>