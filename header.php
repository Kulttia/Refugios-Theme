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

    <!-- Estilos Críticos: Sobrescribir Lupa de WooCommerce (Bypass UCSS/LiteSpeed Cache) -->
    <style id="refugios-brutal-zoom-override" data-no-optimize="1">
    .woocommerce-product-gallery__trigger,
    .woocommerce div.product div.images .woocommerce-product-gallery__trigger {
      position: absolute !important;
      right: 1.5rem !important;
      top: 1.5rem !important;
      left: auto !important;
      z-index: 999 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 2.75rem !important;
      height: 2.75rem !important;
      border-radius: 0 !important;
      background: var(--color-amber) !important;
      color: var(--color-brown) !important;
      border: 2px solid var(--color-brown) !important;
      box-shadow: 2px 2px 0 var(--color-brown) !important;
      text-decoration: none !important;
      font-size: 0 !important; /* Esconde el emoji de la lupa nativo de WooCommerce */
      cursor: pointer !important;
      transition: all 0.2s ease !important;
    }
    .woocommerce-product-gallery__trigger img,
    .woocommerce-product-gallery__trigger svg,
    .woocommerce-product-gallery__trigger .emoji,
    .woocommerce-product-gallery__trigger img.emoji,
    .woocommerce-product-gallery__trigger * {
      display: none !important;
    }
    .woocommerce-product-gallery__trigger::after,
    .woocommerce div.product div.images .woocommerce-product-gallery__trigger::after {
      content: "" !important;
      display: none !important;
    }
    .woocommerce-product-gallery__trigger::before,
    .woocommerce div.product div.images .woocommerce-product-gallery__trigger::before {
      content: "\f002" !important; /* Icono lupa de FontAwesome */
      font-family: "Font Awesome 6 Free" !important;
      font-weight: 900 !important;
      font-size: 1.1rem !important;
      color: var(--color-brown) !important;
      text-indent: 0 !important;
      display: inline-block !important;
    }
    .woocommerce-product-gallery__trigger:hover,
    .woocommerce div.product div.images .woocommerce-product-gallery__trigger:hover {
      background: var(--color-cream) !important;
      transform: translate(-1px, -1px) !important;
      box-shadow: 3px 3px 0 var(--color-amber) !important;
    }
    </style>

    <!-- Estilos Críticos: Menú móvil + Botón Favoritos (Bypass UCSS/LiteSpeed Cache).
         UCSS purga selectores que no existen en el HTML estático (.is-open la añade JS;
         el markup del plugin de wishlist se renderiza tarde), así que viven aquí inline. -->
    <style id="refugios-critical-nav-wishlist" data-no-optimize="1">
    @media (max-width: 900px) {
      .menu-toggle {
        display: flex !important;
        align-items: center;
        justify-content: center;
      }
      #primary-navigation {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 85%;
        max-width: 340px;
        height: 100vh;
        background: var(--color-cream);
        z-index: 1001;
        overflow-y: auto;
        transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1), border-radius 0.4s ease, visibility 0.4s, box-shadow 0.4s ease;
        border-right: var(--border-md);
        border-radius: 0 100% 100% 0 / 0 40% 40% 0;
        transform: translateX(-110%);
        visibility: hidden;
        padding: 6rem 1.5rem 2.5rem;
        display: block !important;
      }
      #primary-navigation.is-open {
        transform: translateX(0) !important;
        border-radius: 0 !important;
        visibility: visible !important;
        display: block !important;
        box-shadow: 0 0 0 100vw rgba(78, 52, 46, 0.4), 10px 0 30px rgba(78, 52, 46, 0.15) !important;
      }
      #primary-navigation ul {
        display: flex;
        flex-direction: column;
        gap: 0;
        width: 100%;
      }
      #primary-navigation ul li { width: 100%; }
      #primary-navigation ul li a {
        display: block;
        width: 100%;
        padding: 1.125rem 0;
        border-bottom: 2px solid rgba(78, 52, 46, 0.1);
        font-size: 1.125rem;
        font-weight: 700;
      }
      #primary-navigation li.menu-item-has-children > ul.sub-menu {
        display: none;
        padding: 0.5rem 0 0.5rem 1rem;
        border-left: 2px solid var(--color-amber);
        margin-top: 0;
        background: transparent;
        box-shadow: none;
        position: static;
      }
      #primary-navigation li.is-active > ul.sub-menu { display: block; }
    }
    @media (min-width: 901px) {
      .menu-toggle { display: none !important; }
    }
    /* Botón Favoritos — cuadrado brutalista, icono a tamaño */
    [class*="wishlist"] a,
    [class*="wishlist"] button,
    .tinvwl_add_to_wishlist_button,
    .yith-wcwl-add-button > a,
    a.add_to_wishlist {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 8px !important;
      border: 2px solid var(--color-brown) !important;
      border-radius: 0 !important;
      box-shadow: 3px 3px 0 var(--color-brown) !important;
      background: var(--color-amber) !important;
      color: var(--color-brown) !important;
      text-decoration: none !important;
      line-height: 1 !important;
      padding: 0.5rem 1.25rem !important;
      min-height: 3.25rem !important;
      width: auto !important;
      height: auto !important;
    }
    [class*="wishlist"] a:hover,
    [class*="wishlist"] button:hover,
    .tinvwl_add_to_wishlist_button:hover,
    .yith-wcwl-add-button > a:hover,
    a.add_to_wishlist:hover {
      background: var(--color-cream) !important;
      transform: translate(-1px, -1px) !important;
      box-shadow: 4px 4px 0 var(--color-amber) !important;
    }
    [class*="wishlist"] svg,
    [class*="wishlist"] img,
    [class*="wishlist"] i,
    .tinvwl_add_to_wishlist_button svg,
    .tinvwl_add_to_wishlist_button img,
    .tinvwl_add_to_wishlist_button i,
    .yith-wcwl-add-button > a svg,
    .yith-wcwl-add-button > a img,
    .yith-wcwl-add-button > a i,
    a.add_to_wishlist svg,
    a.add_to_wishlist img,
    a.add_to_wishlist i {
      width: 1.25rem !important;
      height: 1.25rem !important;
      max-width: 1.25rem !important;
      max-height: 1.25rem !important;
      font-size: 1.1rem !important;
      flex: 0 0 auto !important;
    }
    @media (max-width: 768px) {
      .tinvwl_add_to_wishlist_button,
      .yith-wcwl-add-button > a {
        width: 100% !important;
        justify-content: center !important;
        padding: 1rem !important;
        min-height: 48px !important;
      }
    }
    </style>

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
