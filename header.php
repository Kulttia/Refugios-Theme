<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5e9e2">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo esc_url(get_template_directory_uri() . '/assets/favicon.png'); ?>" sizes="32x32">

    <!-- Font Awesome 6 (Immediate Load) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <?php 
    // Optimization: Preload LCP (Hero Image) if on front page
    if (is_front_page()) {
        $hero_image = get_theme_mod('refugios_hero_image');
        if ($hero_image) {
            echo '<link rel="preload" as="image" href="' . esc_url($hero_image) . '" fetchpriority="high">';
        }
    }
    ?>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e('Saltar al contenido', 'refugios'); ?></a>

<!-- =============================================
     SITE HEADER
     ============================================= -->
<header id="site-header" role="banner">
    <div class="container">
        <div class="nav-inner">

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>"
               class="site-logo"
               id="site-logo-main"
               aria-label="<?php bloginfo('name'); ?>">
                <?php
                if (has_custom_logo()) {
                    $custom_logo_id = get_theme_mod('custom_logo');
                    $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                    if ($logo) {
                        echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '">';
                    } else {
                        the_custom_logo();
                    }
                } else {
                    $blog_name = get_bloginfo('name');
                    echo '<span>' . esc_html(mb_substr($blog_name, 0, 1)) . '</span>'
                        . esc_html(mb_substr($blog_name, 1));
                }
                ?>
            </a>

            <!-- Primary Navigation -->
            <nav id="primary-navigation"
                 aria-label="<?php esc_attr_e('Menú principal', 'refugios'); ?>">
                <?php
wp_nav_menu([
    'theme_location' => 'primary',
    'menu_id' => 'primary-menu',
    'container' => false,
    'fallback_cb' => 'refugios_fallback_menu',
]);
?>
            </nav>

            <!-- Nav Actions -->
            <div class="nav-actions">

                <!-- Search Toggle -->
                <button class="nav-actions__btn"
                        id="nav-search-toggle"
                        aria-label="<?php esc_attr_e('Buscar', 'refugios'); ?>"
                        aria-expanded="false"
                        aria-controls="nav-search-panel">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <span class="btn-text-label"><?php esc_html_e('Buscar', 'refugios'); ?></span>
                </button>

                <!-- WooCommerce Cart -->
                <?php if (function_exists('WC')): ?>
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>"
                       class="nav-actions__btn nav-cart"
                       id="nav-cart-btn"
                       aria-label="<?php esc_attr_e('Ver carrito de compras', 'refugios'); ?>">
                        <i class="fa-solid fa-basket-shopping" aria-hidden="true"></i>
                        <?php
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    if ($count > 0):
?>
                            <span class="nav-cart-count" aria-label="<?php echo esc_attr($count . ' ' . __('artículos', 'refugios')); ?>"><?php echo esc_html($count); ?></span>
                        <?php
    endif; ?>
                        <span class="btn-text-label"><?php esc_html_e('Carrito', 'refugios'); ?></span>
                    </a>
                <?php
endif; ?>

                <!-- Account -->
                <?php if (function_exists('wc_get_account_endpoint_url')): ?>
                    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                       class="nav-actions__btn"
                       aria-label="<?php esc_attr_e('Mi cuenta', 'refugios'); ?>">
                        <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
                        <span class="btn-text-label"><?php esc_html_e('Cuenta', 'refugios'); ?></span>
                    </a>
                <?php
endif; ?>
            </div><!-- .nav-actions -->

            <!-- Hamburger (mobile) -->
            <button class="menu-toggle"
                    id="menu-toggle-btn"
                    aria-controls="primary-navigation"
                    aria-expanded="false"
                    aria-label="<?php esc_attr_e('Abrir menú', 'refugios'); ?>">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>

        </div><!-- .nav-inner -->
    </div><!-- .container -->

    <!-- Search Panel (dropdown) -->
    <div id="nav-search-panel" class="nav-search-panel" aria-hidden="true" hidden>
        <div class="container">
            <form role="search"
                  method="get"
                  action="<?php echo esc_url(home_url('/')); ?>"
                  class="nav-search-form">
                <label for="nav-search-input" class="visually-hidden">
                    <?php esc_html_e('Buscar en Refugios', 'refugios'); ?>
                </label>
                <input type="search"
                       id="nav-search-input"
                       name="s"
                       value="<?php echo esc_attr(get_search_query()); ?>"
                       placeholder="<?php esc_attr_e('Busca libros, artículos, café…', 'refugios'); ?>"
                       autocomplete="off">
                <button type="submit">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <span><?php esc_html_e('Buscar', 'refugios'); ?></span>
                </button>
                <button type="button" id="nav-search-close" aria-label="<?php esc_attr_e('Cerrar búsqueda', 'refugios'); ?>">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div><!-- #nav-search-panel -->

</header><!-- #site-header -->
