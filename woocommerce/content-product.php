<?php
/**
 * The template for displaying product content within loops
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('refugios-product-card book-card', $product); ?>>
    <?php
$title = $product->get_title();
$price = $product->get_price_html();

// Attempt to get an author from a WooCommerce product attribute named 'autor' or 'pa_autor'
$author = refugios_get_product_author($product);

// Attempt to get a quote from 'frase'
$quote = $product->get_attribute('frase');
if (!$quote && $product->get_short_description()) {
    $quote = wp_strip_all_tags($product->get_short_description());
    $quote = wp_trim_words($quote, 15, '...');
}
elseif (!$quote) {
    $quote = 'El buen libro es de todos los siglos.'; // Default
}

$desc = $product->get_short_description();
if (!$desc) {
    $desc = wp_trim_words($product->get_description(), 12, '...');
}

// Categoria
$cats = wc_get_product_category_list($product->get_id(), ', ');
$link = apply_filters('woocommerce_loop_product_link', get_the_permalink(), $product);

$phone = get_theme_mod('refugios_phone', '5551234567');
$clean_phone = preg_replace('/[^0-9]/', '', $phone);
$wa_url = 'https://wa.me/' . $clean_phone . '?text=' . rawurlencode('Hola, me interesa consultar por el libro: ' . $title);
?>

    <div class="refugios-product-card__content">
        <div class="refugios-product-card__cat"><?php echo wp_kses_post($cats); ?></div>
        
        <h2 class="refugios-product-card__title">
            <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
        </h2>
        
        <div class="refugios-product-card__author"><?php echo esc_html($author); ?></div>
        
        <div class="refugios-product-card__desc">
            <?php echo wp_kses_post($desc); ?>
        </div>

        <div class="refugios-product-card__footer">
            <span class="refugios-product-card__price"><?php echo $price; ?></span>
            <a href="<?php echo esc_url($link); ?>" class="refugios-product-card__btn-wa">
                Ver Libro
            </a>
        </div>
    </div>

    <!-- The subtle floating quote on hover -->
    <div class="refugios-product-card__hover-quote">
        <p>"<?php echo esc_html($quote); ?>"</p>
    </div>
</li>
