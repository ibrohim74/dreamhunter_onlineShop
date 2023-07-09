<?php
function mytheme_add_woocommerce_support()
{
    add_theme_support('woocommerce');
}

add_action('after_setup_theme', 'mytheme_add_woocommerce_support');

function load_stylesheets()
{
    wp_register_style('stylesheet', get_template_directory_uri() . '/style.css', '', 1, 'all');
    wp_enqueue_style('stylesheet');
}
add_action('wp_enqueue_scripts', 'load_stylesheets');

add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

function custom_override_checkout_fields($fields)
{
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_email']);
    return $fields;
}

add_filter('woocommerce_checkout_fields', 'change_billing_conntent_text');

// Our hooked in function - $fields is passed via the filter!
function change_billing_conntent_text($fields)
{
    $fields['billing']['billing_first_name']['placeholder'] = 'Ismingiz';
    $fields['billing']['billing_last_name']['placeholder'] = 'Telegram username yozing';
    $fields['billing']['billing_phone']['placeholder'] = '+998 99 1234567';
    $fields['billing']['billing_address_1']['placeholder'] = 'Uy / xonadon raqami / ko`chaning nomi';
    $fields['billing']['billing_address_2']['placeholder'] = 'Padez / burulish / qavat';
    $fields['order']['order_comments']['placeholder'] = 'Qoshimcha malumot (majburiyemas)';

    $fields['billing']['billing_first_name']['label'] = 'ismingiz';
    $fields['billing']['billing_last_name']['label'] = 'familyangiz';
    $fields['billing']['billing_phone']['label'] = 'Telefon raqamingiz';
    $fields['billing']['billing_city']['label'] = 'Shaxar / Viloyat';
    $fields['billing']['billing_state']['label'] = 'Tuman';
    $fields['billing']['billing_address_1']['label'] = 'Manzil';
    $fields['order']['order_comments']['label'] = 'Qoshimcha malumot yoki fikringiz';



    return $fields;
}


function wc_empty_cart_redirect_url()
{
    return '/';
}
add_filter('woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url', 10);



function my_custom_checkout_button_text()
{
    return 'Buyurtma berish';
}
add_filter('woocommerce_order_button_text', 'my_custom_checkout_button_text');