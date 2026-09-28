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

// Provocación de lectura: frase → descripción larga; nunca la ficha técnica
$quote = refugios_book_teaser($product, 20);
$desc = refugios_book_teaser($product, 14);

// Categoria
$cats = wc_get_product_category_list($product->get_id(), ', ');
$link = apply_filters('woocommerce_loop_product_link', get_the_permalink(), $product);

$phone = get_theme_mod('refugios_phone', '5551234567');
$clean_phone = preg_replace('/[^0-9]/', '', $phone);
$wa_url = 'https://wa.me/' . $clean_phone . '?text=' . rawurlencode('Hola, me interesa consultar por el libro: ' . $title);
?>

    <a href="<?php echo esc_url($link); ?>" class="refugios-product-card__media" aria-hidden="true" tabindex="-1">
        <?php if ($product->is_on_sale()) { echo wp_kses_post(apply_filters('woocommerce_sale_flash', '<span class="onsale">' . esc_html__('¡Oferta!', 'refugios') . '</span>', null, $product)); } ?>
        <?php echo $product->get_image('large'); ?>
    </a>

    <div class="refugios-product-card__content">
        <div class="refugios-product-card__cat"><?php echo wp_kses_post($cats); ?></div>
        
        <h2 class="refugios-product-card__title">
            <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
        </h2>
        
        <div class="refugios-product-card__author"><?php echo esc_html($author); ?></div>
        
        <div class="refugios-product-card__desc">
            <p><?php echo esc_html($desc); ?></p>
        </div>

        <div class="refugios-product-card__footer">
            <span class="refugios-product-card__price"><?php echo $price; ?></span>
            <div class="refugios-product-card__actions">
                <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()): ?>
                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
                       data-quantity="1"
                       data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                       class="refugios-card-buy add_to_cart_button ajax_add_to_cart"
                       rel="nofollow">
                        <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                        <?php esc_html_e('Comprar', 'refugios'); ?>
                    </a>
                <?php else: ?>
                    <a href="<?php echo esc_url($link); ?>" class="refugios-card-buy">
                        <?php esc_html_e('Ver libro', 'refugios'); ?>
                    </a>
                <?php endif; ?>
                <a href="<?php echo esc_url($wa_url); ?>" class="refugios-card-wa"
                   target="_blank" rel="noopener"
                   aria-label="<?php esc_attr_e('Consultar por WhatsApp', 'refugios'); ?>">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- The subtle floating quote on hover -->
    <div class="refugios-product-card__hover-quote">
        <p>"<?php echo esc_html($quote); ?>"</p>
    </div>
</li>
