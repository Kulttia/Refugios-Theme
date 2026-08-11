<?php
/**
 * Refugios — functions.php
 * Registers all theme functionality, assets, menus,
 * WooCommerce support and custom helpers.
 */

defined('ABSPATH') || exit;

/* =========================================================
 1. THEME SETUP
 ========================================================= */

function refugios_setup()
{
    load_theme_textdomain('refugios', get_template_directory() . '/languages');

    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ]);
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 600,
        'single_image_width' => 800,
        'gallery_thumbnail_image_width' => 150,
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    add_image_size('refugios-hero', 1920, 900, true);
    add_image_size('refugios-book', 400, 600, true);
    add_image_size('refugios-blog', 800, 450, true);
    add_image_size('refugios-thumb', 400, 300, true);

    register_nav_menus([
        'primary' => esc_html__('Menú Principal', 'refugios'),
        'footer-1' => esc_html__('Footer — Tienda', 'refugios'),
        'footer-2' => esc_html__('Footer — Info', 'refugios'),
        'footer-3' => esc_html__('Footer — Legal', 'refugios'),
    ]);
}
add_action('after_setup_theme', 'refugios_setup');

/* =========================================================
 2. CONTENT WIDTH
 ========================================================= */

function refugios_content_width()
{
    $GLOBALS['content_width'] = apply_filters('refugios_content_width', 1320);
}
add_action('after_setup_theme', 'refugios_content_width', 0);

/* =========================================================
 3. ENQUEUE SCRIPTS & STYLES
 ========================================================= */

function refugios_enqueue_assets()
{

    /* ---- Tailwind (compilado local — ver tailwind.config.js) ---- */
    wp_enqueue_style(
        'refugios-tailwind',
        get_template_directory_uri() . '/assets/css/tailwind.min.css',
        [],
        wp_get_theme()->get('Version')
    );

    /* ---- Google Fonts ---- */
    wp_enqueue_style(
        'refugios-fonts',
        'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400&family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,700&display=swap',
    [],
        null
    );

    /* ---- Font Awesome 6 ---- */
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    [],
        '6.5.1'
    );

    /* ---- Theme stylesheet (carga después de Tailwind para poder sobreescribirlo) ---- */
    wp_enqueue_style(
        'refugios-style-v5',
        get_stylesheet_uri(),
        ['refugios-tailwind'],
        wp_get_theme()->get('Version')
    );

    /* ---- Main JS (footer) ---- */
    wp_enqueue_script(
        'refugios-main-v5',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        wp_get_theme()->get('Version'),
        true // in footer
    );

    /* ---- Pass data to JS ---- */
    wp_localize_script('refugios-main-v5', 'refugiosData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('refugios_nonce'),
        'siteUrl' => home_url(),
        'cartUrl' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '',
        'shopUrl' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '',
    ]);

    /* ---- WooCommerce: enqueue comment-reply if needed ---- */
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    /* ---- "Comprar" con AJAX también en el Home (Woo solo lo carga en tienda) ---- */
    if (is_front_page() && function_exists('WC')) {
        wp_enqueue_script('wc-add-to-cart');
        wp_enqueue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'refugios_enqueue_assets');

/* =========================================================
 3.1 PERFORMANCE OPTIMIZATIONS
 ========================================================= */

/**
 * Add async/defer to specific scripts for better performance.
 */
function refugios_script_loader_tag($tag, $handle, $src)
{
    // Scripts to defer
    $defer_scripts = ['refugios-main-v5', 'wp-embed'];
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    // Scripts to load async
    $async_scripts = [];
    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' async src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'refugios_script_loader_tag', 10, 3);

/**
 * Add resource hints (preconnect) to headers.
 */
function refugios_resource_hints($urls, $relation_type)
{
    if ('preconnect' === $relation_type) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = 'https://fonts.gstatic.com';
        $urls[] = 'https://cdnjs.cloudflare.com';
    }
    return $urls;
}
add_filter('wp_resource_hints', 'refugios_resource_hints', 10, 2);

/* =========================================================
 3.2 CLEANUP WORDPRESS BLOAT (PageSpeed Boost)
 ========================================================= */

function refugios_cleanup_wp_head()
{
    // Remove Emoji scripts and styles
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    
    // Remove Gutenberg CSS from head if not needed elsewhere
    // wp_dequeue_style( 'wp-block-library' );
    // wp_dequeue_style( 'wp-block-library-theme' );
    // wp_dequeue_style( 'wc-block-style' ); 
    
    // Remove RSD, WLW, Shortlink and WP Generator
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('after_setup_theme', 'refugios_cleanup_wp_head');

/* =========================================================
 4. WIDGETS / SIDEBARS
 ========================================================= */

function refugios_widgets_init()
{
    $defaults = [
        'before_widget' => '<div class="blog-sidebar-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="blog-sidebar-widget__header">',
        'after_title' => '</h4><div class="blog-sidebar-widget__body">',
    ];

    register_sidebar(array_merge($defaults, [
        'name' => esc_html__('Sidebar Blog', 'refugios'),
        'id' => 'sidebar-blog',
        'description' => esc_html__('Widgets del sidebar del blog.', 'refugios'),
    ]));

    register_sidebar(array_merge($defaults, [
        'name' => esc_html__('Sidebar Tienda', 'refugios'),
        'id' => 'sidebar-shop',
        'description' => esc_html__('Widgets del sidebar de la tienda.', 'refugios'),
    ]));
}
add_action('widgets_init', 'refugios_widgets_init');

/* =========================================================
 5. WOOCOMMERCE
 ========================================================= */

// Remove default WooCommerce wrappers ONLY when WooCommerce is active
function refugios_woo_setup()
{
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

    add_action('woocommerce_before_main_content', 'refugios_woo_wrapper_start', 10);
    add_action('woocommerce_after_main_content', 'refugios_woo_wrapper_end', 10);
}
add_action('woocommerce_loaded', 'refugios_woo_setup');

function refugios_woo_wrapper_start()
{
    echo '<div class="woo-main-wrapper container section-pad">';
}
function refugios_woo_wrapper_end()
{
    echo '</div>';
}

/* =========================================================
 6. BODY CLASSES
 ========================================================= */

function refugios_body_class($classes)
{
    if (is_front_page())
        $classes[] = 'is-front-page';
    if (is_singular())
        $classes[] = 'is-singular';
    // Guard WooCommerce-specific conditional tags
    if (function_exists('is_shop') && is_shop())
        $classes[] = 'is-shop';
    if (function_exists('is_woocommerce') && is_woocommerce())
        $classes[] = 'is-woocommerce';
    return $classes;
}
add_filter('body_class', 'refugios_body_class');

/* =========================================================
 7. CUSTOM EXCERPT LENGTH
 ========================================================= */

function refugios_excerpt_length($length)
{
    return 20;
}
add_filter('excerpt_length', 'refugios_excerpt_length');

function refugios_excerpt_more($more)
{
    return '&hellip;';
}
add_filter('excerpt_more', 'refugios_excerpt_more');

/* =========================================================
 8. HELPER FUNCTIONS
 ========================================================= */

/**
 * Display cart icon with item count.
 */
function refugios_cart_icon()
{
    if (!function_exists('WC'))
        return;
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#';
?>
    <a href="<?php echo esc_url($url); ?>" class="nav-cart" aria-label="<?php esc_attr_e('Ver carrito', 'refugios'); ?>">
        <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
        <?php if ($count > 0): ?>
            <span class="nav-cart-count" aria-label="<?php echo esc_attr(sprintf(__('%d artículos en el carrito', 'refugios'), $count)); ?>">
                <?php echo esc_html($count); ?>
            </span>
        <?php
    endif; ?>
    </a>
    <?php
}

/**
 * Update cart count via AJAX.
 */
function refugios_cart_count_ajax()
{
    if (!function_exists('WC'))
        wp_send_json_error();
    wp_send_json_success([
        'count' => WC()->cart->get_cart_contents_count(),
    ]);
}
add_action('wp_ajax_refugios_cart_count', 'refugios_cart_count_ajax');
add_action('wp_ajax_nopriv_refugios_cart_count', 'refugios_cart_count_ajax');

/**
 * Render a breadcrumb trail.
 */
function refugios_breadcrumb()
{
    $items = [];
    $home = '<a href="' . esc_url(home_url()) . '">' . esc_html__('Inicio', 'refugios') . '</a>';
    $items[] = $home;

    if (is_singular()) {
        if ($cats = get_the_category()) {
            $items[] = '<a href="' . esc_url(get_category_link($cats[0]->term_id)) . '">'
                . esc_html($cats[0]->name) . '</a>';
        }
        $items[] = '<span>' . esc_html(get_the_title()) . '</span>';
    }
    elseif (is_category()) {
        $items[] = '<span>' . esc_html(single_cat_title('', false)) . '</span>';
    }
    elseif (is_tag()) {
        $items[] = '<span>' . esc_html__('Etiqueta:', 'refugios') . ' ' . single_tag_title('', false) . '</span>';
    }
    elseif (is_search()) {
        $items[] = '<span>' . esc_html__('Búsqueda:', 'refugios') . ' ' . esc_html(get_search_query()) . '</span>';
    }
    elseif (function_exists('is_shop') && is_shop()) {
        $items[] = '<span>' . esc_html__('Tienda', 'refugios') . '</span>';
    }
    elseif (function_exists('is_product_category') && is_product_category()) {
        $page_title = function_exists('woocommerce_page_title') ? woocommerce_page_title(false) : get_the_archive_title();
        $items[] = '<span>' . esc_html($page_title) . '</span>';
    }
    elseif (is_archive()) {
        $items[] = '<span>' . esc_html(get_the_archive_title()) . '</span>';
    }
    elseif (is_page()) {
        $items[] = '<span>' . esc_html(get_the_title()) . '</span>';
    }

    $sep = '<span class="sep" aria-hidden="true">›</span>';
    echo '<nav class="page-header__breadcrumb" aria-label="' . esc_attr__('Ruta de navegación', 'refugios') . '">';
    echo implode(' ' . $sep . ' ', $items);
    echo '</nav>';
}

/**
 * Return post category as first item.
 */
function refugios_first_category($post_id = null)
{
    $post_id = $post_id ?: get_the_ID();
    $cats = get_the_category($post_id);
    return $cats ? esc_html($cats[0]->name) : '';
}

/**
 * WooCommerce: product category term name.
 */
function refugios_product_category($post_id = null)
{
    if (!function_exists('get_the_terms'))
        return '';
    $post_id = $post_id ?: get_the_ID();
    $terms = get_the_terms($post_id, 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        return esc_html($terms[0]->name);
    }
    return '';
}

/**
 * WooCommerce: Get product author from multiple possible attributes.
 */
function refugios_get_product_author($product = null)
{
    if (!$product) {
        global $product;
    }
    if (!$product || !is_a($product, 'WC_Product')) {
        return '';
    }

    $author = '';
    // Slugs to check based on user input and common variations
    $author_slugs = [
        'pa_autor-a', 'autor-a', 'Autor (a)', 
        'pa_autor', 'autor', 
        'pa_author', 'author', 
        'pa_autores', 'autores'
    ];

    foreach ($author_slugs as $slug) {
        $attr_val = $product->get_attribute($slug);
        if (!empty($attr_val)) {
            $author = $attr_val;
            break;
        }
    }

    // Fallback: try to see if it's a tag
    if (empty($author)) {
        $tags = get_the_terms($product->get_id(), 'product_tag');
        if ($tags && !is_wp_error($tags)) {
            $author = $tags[0]->name;
        }
    }

    return $author ?: 'Autor desconocido';
}

/**
 * Display author on single product page summary.
 */
function refugios_display_single_product_author()
{
    $author = refugios_get_product_author();
    if ($author) {
        echo '<div class="refugios-single-author" style="font-family: var(--font-sans); font-weight: 600; text-transform: uppercase; color: var(--color-amber); margin-bottom: 0.5rem; font-size: 0.9rem;">' . esc_html($author) . '</div>';
    }
}
add_action('woocommerce_single_product_summary', 'refugios_display_single_product_author', 7);

/* =========================================================
 9. SECURITY & MISC
 ========================================================= */

// Remove WordPress version from head
remove_action('wp_head', 'wp_generator');

// XML-RPC off (unless specifically needed)
add_filter('xmlrpc_enabled', '__return_false');

/* =========================================================
 10. CUSTOMIZER — Brand Options
 ========================================================= */

function refugios_customize_register($wp_customize)
{

    // Panel
    $wp_customize->add_panel('refugios_panel', [
        'title' => esc_html__('Refugios — Opciones de Marca', 'refugios'),
        'priority' => 30,
    ]);

    // Section: Hero
    $wp_customize->add_section('refugios_hero', [
        'title' => esc_html__('Hero Section', 'refugios'),
        'panel' => 'refugios_panel',
    ]);

    $hero_fields = [
        'refugios_hero_label' => ['label' => 'Etiqueta Hero', 'default' => 'Librería & Cafetería Especializada'],
        'refugios_hero_title' => ['label' => 'Título Hero (H1)', 'default' => 'Donde los libros encuentran su café.'],
        'refugios_hero_subtitle' => ['label' => 'Subtítulo Hero', 'default' => 'Un espacio para pausar, leer y conectar. Libros curados y café de especialidad en un mismo refugio.'],
        'refugios_hero_cta_1' => ['label' => 'CTA Primario — Texto', 'default' => 'Explorar Libros'],
        'refugios_hero_cta_1_url' => ['label' => 'CTA Primario — URL', 'default' => '/tienda'],
        'refugios_hero_cta_2' => ['label' => 'CTA Secundario — Texto', 'default' => 'Ver el Menú'],
        'refugios_hero_cta_2_url' => ['label' => 'CTA Secundario — URL', 'default' => '/manifiestos'],
    ];

    foreach ($hero_fields as $id => $args) {
        $wp_customize->add_setting($id, [
            'default' => $args['default'],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control($id, [
            'label' => $args['label'],
            'section' => 'refugios_hero',
            'type' => 'text',
        ]);
    }

    // Hero image
    $wp_customize->add_setting('refugios_hero_image', ['default' => '']);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'refugios_hero_image', [
        'label' => esc_html__('Imagen de fondo Hero', 'refugios'),
        'section' => 'refugios_hero',
    ]));

    // Section: Pausa (Café)
    $wp_customize->add_section('refugios_pausa', [
        'title' => esc_html__('Sección La Pausa (Café)', 'refugios'),
        'panel' => 'refugios_panel',
    ]);

    $pausa_fields = [
        'refugios_pausa_title' => ['label' => 'Título La Pausa', 'default' => 'El café que acompaña cada página.'],
        'refugios_pausa_desc' => ['label' => 'Descripción', 'default' => 'En Refugios creemos que cada buen libro merece una buena taza. Seleccionamos cuidadosamente nuestros granos y nuestras historias.'],
        'refugios_pausa_stat1_num' => ['label' => 'Stat 1 — Número', 'default' => '200+'],
        'refugios_pausa_stat1_label' => ['label' => 'Stat 1 — Etiqueta', 'default' => 'Títulos'],
        'refugios_pausa_stat2_num' => ['label' => 'Stat 2 — Número', 'default' => '12'],
        'refugios_pausa_stat2_label' => ['label' => 'Stat 2 — Etiqueta', 'default' => 'Orígenes café'],
        'refugios_pausa_stat3_num' => ['label' => 'Stat 3 — Número', 'default' => '6'],
        'refugios_pausa_stat3_label' => ['label' => 'Stat 3 — Etiqueta', 'default' => 'Años'],
    ];

    foreach ($pausa_fields as $id => $args) {
        $wp_customize->add_setting($id, [
            'default' => $args['default'],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control($id, [
            'label' => $args['label'],
            'section' => 'refugios_pausa',
            'type' => 'text',
        ]);
    }

    $wp_customize->add_setting('refugios_pausa_image', ['default' => '']);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'refugios_pausa_image', [
        'label' => esc_html__('Imagen taza de café', 'refugios'),
        'section' => 'refugios_pausa',
    ]));

    // Section: Contact
    $wp_customize->add_section('refugios_contact', [
        'title' => esc_html__('Datos de Contacto', 'refugios'),
        'panel' => 'refugios_panel',
    ]);

    $contact_fields = [
        'refugios_address' => ['label' => 'Dirección', 'default' => 'Calle de los Libros 42, Ciudad'],
        'refugios_hours' => ['label' => 'Horario', 'default' => 'Lun–Vie: 8:00–20:00 / Sáb–Dom: 9:00–21:00'],
        'refugios_phone' => ['label' => 'Teléfono / WhatsApp', 'default' => '+1 (555) 123-4567'],
        'refugios_email' => ['label' => 'Email', 'default' => 'hola@refugios.com'],
        'refugios_instagram' => ['label' => 'Instagram URL', 'default' => 'https://instagram.com/refugios'],
        'refugios_facebook' => ['label' => 'Facebook URL', 'default' => 'https://facebook.com/refugios'],
        'refugios_whatsapp' => ['label' => 'WhatsApp URL', 'default' => 'https://wa.me/15551234567'],
    ];

    foreach ($contact_fields as $id => $args) {
        $wp_customize->add_setting($id, [
            'default' => $args['default'],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control($id, [
            'label' => $args['label'],
            'section' => 'refugios_contact',
            'type' => 'text',
        ]);
    }
}
add_action('customize_register', 'refugios_customize_register');

/* =========================================================
 11. MENU FALLBACK FUNCTIONS
 (Called by wp_nav_menu as fallback_cb)
 ========================================================= */

/**
 * Fallback primary menu — renders all published pages.
 */
function refugios_fallback_menu()
{
    $pages = get_pages(['sort_column' => 'menu_order']);
    if (!$pages)
        return;
    echo '<ul id="primary-menu">';
    foreach ($pages as $page) {
        $current = (get_queried_object_id() === $page->ID) ? ' class="current-menu-item"' : '';
        printf(
            '<li%s><a href="%s">%s</a></li>',
            $current,
            esc_url(get_permalink($page->ID)),
            esc_html($page->post_title)
        );
    }
    echo '</ul>';
}

/**
 * Fallback footer store menu.
 */
function refugios_footer_fallback()
{
    $pages = [
        __('Tienda Refugios', 'refugios') => '/tienda-refugios',
        __('Blog', 'refugios') => '/blog',
        __('Quiénes Somos', 'refugios') => '/quienes-somos',
        __('Contacto', 'refugios') => '/contacto',
    ];
    echo '<ul class="footer-links">';
    foreach ($pages as $label => $slug) {
        printf(
            '<li><a href="%s">%s</a></li>',
            esc_url(home_url($slug)),
            esc_html($label)
        );
    }
    echo '</ul>';
}

/**
 * Fallback footer legal menu.
 */
function refugios_footer_legal_fallback()
{
    $pages = [
        __('Términos y Condiciones', 'refugios') => '/terminos-y-condiciones',
        __('Preguntas Frecuentes', 'refugios') => '/preguntas-frecuentes',
    ];
    echo '<ul class="footer-legal-links">';
    foreach ($pages as $label => $slug) {
        printf(
            '<li><a href="%s">%s</a></li>',
            esc_url(home_url($slug)),
            esc_html($label)
        );
    }
    echo '</ul>';
}

/* =========================================================
 SEO — Canonical / Open Graph / Twitter Card / JSON-LD
 ========================================================= */

function refugios_seo_head() {
    global $post;

    // Canonical, OG y Twitter Card los gestiona RankMath.
    // Esta función solo inyecta JSON-LD que RankMath no cubre.

    $site_name = 'Refugios';
    $site_url  = home_url('/');
    $logo_url  = 'https://refugios.co/wp-content/uploads/2026/06/Logo-Refugios.png';
    $phone     = '+573238113985';
    $email     = 'administracion@refugios.co';
    $instagram = 'https://www.instagram.com/refugios.co/';
    $facebook  = 'https://www.facebook.com/libreriarefugios';
    $tiktok    = 'https://www.tiktok.com/@refugios.co';

    // JSON-LD: Organization + BookStore (homepage)
    if (is_front_page()) {
        $schema = [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'      => 'WebSite',
                    '@id'        => $site_url . '#website',
                    'url'        => $site_url,
                    'name'       => $site_name,
                    'inLanguage' => 'es-CO',
                    'potentialAction' => [
                        '@type'       => 'SearchAction',
                        'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => $site_url . '?s={search_term_string}'],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
                [
                    '@type'     => ['Organization', 'BookStore'],
                    '@id'       => $site_url . '#organization',
                    'name'      => $site_name,
                    'url'       => $site_url,
                    'logo'      => ['@type' => 'ImageObject', 'url' => $logo_url],
                    'email'     => $email,
                    'telephone' => $phone,
                    'address'   => [
                        '@type'           => 'PostalAddress',
                        'streetAddress'   => 'Calle 51 # 48-53, Centro Comercial La Gran Manzana',
                        'addressLocality' => 'Itagüí',
                        'addressRegion'   => 'Antioquia',
                        'addressCountry'  => 'CO',
                    ],
                    'openingHoursSpecification' => [[
                        '@type'     => 'OpeningHoursSpecification',
                        'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                        'opens'     => '10:00',
                        'closes'    => '19:00',
                    ]],
                    'sameAs' => [$instagram, $facebook, $tiktok],
                ],
            ],
        ];
        echo "\n<!-- JSON-LD: Organization + BookStore -->\n";
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }

    // JSON-LD: Product + Offer (single product)
    if (is_singular('product') && $post && function_exists('wc_get_product')) {
        $product = wc_get_product($post->ID);
        if ($product) {
            $schema = [
                '@context' => 'https://schema.org',
                '@graph'   => [
                    [
                        '@type'           => 'BreadcrumbList',
                        '@id'             => get_permalink() . '#breadcrumb',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => $site_url],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => get_the_title(), 'item' => get_permalink()],
                        ],
                    ],
                    [
                        // Product + Book: elegible para resultados enriquecidos
                        // de libros (autor/ISBN) además de los de producto.
                        '@type'       => ['Product', 'Book'],
                        '@id'         => get_permalink() . '#product',
                        'name'        => get_the_title(),
                        'author'      => ['@type' => 'Person', 'name' => refugios_get_product_author($product)],
                        'isbn'        => $product->get_sku(),
                        'description' => wp_strip_all_tags($product->get_description() ?: $product->get_short_description())
                            ?: sprintf('%s, disponible en Refugios, librería y café en Itagüí.', get_the_title()),
                        'sku'         => $product->get_sku(),
                        // El ISBN-13 ES un GTIN-13: cierra el aviso de identificador
                        'gtin13'      => preg_match('/^97[89]\d{10}$/', (string) $product->get_sku()) ? $product->get_sku() : null,
                        'brand'       => ($sello = $product->get_attribute('Sello') ?: $product->get_attribute('Editorial'))
                            ? ['@type' => 'Brand', 'name' => $sello] : null,
                        'image'       => get_the_post_thumbnail_url($post->ID, 'full') ?: '',
                        'offers'      => [
                            '@type'         => 'Offer',
                            'url'           => get_permalink(),
                            'priceCurrency' => 'COP',
                            'price'         => $product->get_price(),
                            'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                            'itemCondition' => 'https://schema.org/NewCondition',
                            'priceValidUntil' => gmdate('Y-m-d', strtotime('+3 months')),
                            'seller'        => ['@id' => $site_url . '#organization'],
                            // Envío estándar $12.000 a toda Colombia (gratis desde $150.000)
                            'shippingDetails' => [
                                '@type' => 'OfferShippingDetails',
                                'shippingRate' => [
                                    '@type' => 'MonetaryAmount',
                                    'value' => 12000,
                                    'currency' => 'COP',
                                ],
                                'shippingDestination' => [
                                    '@type' => 'DefinedRegion',
                                    'addressCountry' => 'CO',
                                ],
                            ],
                            // Ley de retracto colombiana: 5 días hábiles
                            'hasMerchantReturnPolicy' => [
                                '@type' => 'MerchantReturnPolicy',
                                'applicableCountry' => 'CO',
                                'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                                'merchantReturnDays' => 5,
                                'returnMethod' => 'https://schema.org/ReturnByMail',
                                'returnFees' => 'https://schema.org/ReturnFeesCustomerResponsibility',
                            ],
                        ],
                    ],
                ],
            ];
            // Limpiar los campos opcionales que quedaron nulos
            $schema['@graph'][1] = array_filter($schema['@graph'][1], fn($v) => $v !== null);

            // Estrellas en Google cuando el libro tiene reseñas
            if ($product->get_review_count() > 0) {
                $schema['@graph'][1]['aggregateRating'] = [
                    '@type'       => 'AggregateRating',
                    'ratingValue' => (string) $product->get_average_rating(),
                    'reviewCount' => (int) $product->get_review_count(),
                ];
            }
            echo "\n<!-- JSON-LD: Product + Offer -->\n";
            echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
    }

    // JSON-LD: BlogPosting (single posts)
    if (is_singular('post') && $post) {
        $schema = [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'           => 'BreadcrumbList',
                    '@id'             => get_permalink() . '#breadcrumb',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => $site_url],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => home_url('/blog/')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => get_the_title(), 'item' => get_permalink()],
                    ],
                ],
                [
                    '@type'         => 'BlogPosting',
                    '@id'           => get_permalink() . '#article',
                    'headline'      => get_the_title(),
                    'description'   => wp_strip_all_tags(get_the_excerpt()),
                    'image'         => get_the_post_thumbnail_url($post->ID, 'full') ?: '',
                    'datePublished' => get_the_date('c'),
                    'dateModified'  => get_the_modified_date('c'),
                    'inLanguage'    => 'es-CO',
                    'url'           => get_permalink(),
                    'isPartOf'      => ['@id' => $site_url . '#website'],
                    'author'        => ['@type' => 'Organization', '@id' => $site_url . '#organization', 'name' => $site_name],
                    'publisher'     => ['@id' => $site_url . '#organization'],
                ],
            ],
        ];
        echo "\n<!-- JSON-LD: BlogPosting -->\n";
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'refugios_seo_head', 5);

/**
 * ¿El texto es ficha técnica y no una provocación de lectura?
 * (páginas, tapa, encuadernación, colección…). Se usa para decidir qué mostrar
 * en las cards: la ficha técnica nunca invita a leer.
 */
function refugios_text_is_technical($text)
{
    if (!$text) return true;
    return (bool) preg_match(
        '/p[aá]ginas?\s*[:\d]|tapa\s+(blanda|dura)|encuadernaci[oó]n|colecci[oó]n\s*:|formato\s*:|isbn|n[°º]\s*p[aá]ginas/iu',
        $text
    );
}

/**
 * Provocación del libro para la card y la cita flotante: primero el atributo
 * "frase", luego la descripción larga; la descripción corta solo si no es
 * ficha técnica. Nunca "Páginas: 48, Tapa blanda".
 */
function refugios_book_teaser($product, $words = 20)
{
    // Un teaser de menos de 25 caracteres no provoca nada ("Alma", "N/A"…)
    $usable = fn($t) => $t && mb_strlen(trim($t)) >= 25 && !refugios_text_is_technical(mb_substr($t, 0, 80));

    $frase = $product->get_attribute('frase');
    if ($frase && mb_strlen(trim($frase)) >= 10) return $frase;

    $long = wp_strip_all_tags($product->get_description());
    if ($usable($long)) return wp_trim_words($long, $words, '…');

    $short = wp_strip_all_tags($product->get_short_description());
    if ($usable($short)) return wp_trim_words($short, $words, '…');

    return 'El buen libro es de todos los siglos.';
}

/* =========================================================
 12. DESTACADOS DINÁMICOS DEL HOME
 Cada mes se eligen solos: las novedades del catálogo
 (lo último sincronizado desde Alegra) con mejor portada.
 ========================================================= */

function refugios_cron_schedules($schedules)
{
    $schedules['refugios_monthly'] = [
        'interval' => 30 * DAY_IN_SECONDS,
        'display' => __('Cada mes (Refugios)', 'refugios'),
    ];
    return $schedules;
}
add_filter('cron_schedules', 'refugios_cron_schedules');

function refugios_schedule_featured_refresh()
{
    if (!wp_next_scheduled('refugios_refresh_featured')) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'refugios_monthly', 'refugios_refresh_featured');
    }
}
add_action('init', 'refugios_schedule_featured_refresh');

/**
 * Área en píxeles de la imagen destacada: el criterio de "mejor calidad".
 */
function refugios_cover_area($product)
{
    $img_id = $product->get_image_id();
    if (!$img_id) return 0;
    $meta = wp_get_attachment_metadata($img_id);
    if (empty($meta['width']) || empty($meta['height'])) return 0;
    return (int) $meta['width'] * (int) $meta['height'];
}

/**
 * Rota los destacados del Home: toma las últimas novedades publicadas,
 * las ordena por calidad de portada y marca las 6 mejores como destacadas.
 */
function refugios_refresh_featured_books()
{
    if (!function_exists('wc_get_products')) return;

    // Candidatas: las 40 novedades más recientes en la tienda
    $recent = wc_get_products([
        'status' => 'publish',
        'limit' => 40,
        'orderby' => 'date',
        'order' => 'DESC',
        'stock_status' => 'instock',
    ]);
    if (empty($recent)) return;

    // Regla: al Home solo van libros. Los productos de regalos (bolsas de
    // café, objetos) no se destacan aunque sean novedad.
    $recent = array_values(array_filter($recent, function ($p) {
        return !has_term('regalos-de-siempre', 'product_cat', $p->get_id());
    }));

    // Portada mínima decente (500x700); ordenadas por resolución real
    $scored = [];
    foreach ($recent as $p) {
        $area = refugios_cover_area($p);
        if ($area >= 500 * 700) {
            $scored[] = ['product' => $p, 'area' => $area];
        }
    }
    usort($scored, fn($a, $b) => $b['area'] <=> $a['area']);

    $picks = array_slice(array_column($scored, 'product'), 0, 6);

    // Si no alcanzan 6 con portada grande, completar con las novedades restantes con imagen
    if (count($picks) < 6) {
        $have = array_map(fn($p) => $p->get_id(), $picks);
        foreach ($recent as $p) {
            if (count($picks) >= 6) break;
            if (!in_array($p->get_id(), $have, true) && $p->get_image_id()) {
                $picks[] = $p;
            }
        }
    }
    if (empty($picks)) return;

    // Quitar la marca a los destacados anteriores
    foreach (wc_get_featured_product_ids() as $old_id) {
        $old = wc_get_product($old_id);
        if ($old) {
            $old->set_featured(false);
            $old->save();
        }
    }

    // Marcar los nuevos
    foreach ($picks as $p) {
        $p->set_featured(true);
        $p->save();
    }

    // La portada del Home cambió: vaciar la caché de LiteSpeed si está activa
    if (has_action('litespeed_purge_all')) {
        do_action('litespeed_purge_all');
    }
}
add_action('refugios_refresh_featured', 'refugios_refresh_featured_books');

// Disparo manual para administradores: /?refugios_rf=1 estando logueado
function refugios_manual_featured_refresh()
{
    if (isset($_GET['refugios_rf']) && current_user_can('manage_options')) {
        refugios_refresh_featured_books();
        wp_safe_redirect(home_url('/'));
        exit;
    }
}
add_action('template_redirect', 'refugios_manual_featured_refresh');

/* =========================================================
 13. PORTADAS: RECORTE DE MARCOS BLANCOS
 Muchas portadas llegan dentro de un lienzo blanco enorme.
 Se recorta al área real solo si los 4 bordes son blanco
 uniforme; una portada cuyo diseño es blanco no pasa esa
 prueba (su arte llega hasta los bordes) y no se toca.
 ========================================================= */

/**
 * ¿La fila/columna es blanca uniforme? Muestrea cada N píxeles.
 */
function refugios_scanline_is_white($img, $fixed, $length, $vertical, $tol = 210)
{
    // Tolerante a ruido JPEG: hasta 5% de muestras impuras siguen contando
    // como línea blanca (sombras fantasma de 1px arruinaban la detección).
    $step = max(1, (int) ($length / 40));
    $bad = 0;
    $total = 0;
    $maxBad = 2;
    for ($i = 0; $i < $length; $i += $step) {
        $total++;
        $rgb = $vertical ? imagecolorat($img, $fixed, $i) : imagecolorat($img, $i, $fixed);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if ($r < $tol || $g < $tol || $b < $tol) {
            $bad++;
            if ($bad > $maxBad) return false;
        }
    }
    return true;
}

/**
 * Recorta el marco blanco de un adjunto y regenera sus tamaños.
 * Devuelve true si recortó.
 */
function refugios_trim_cover($attachment_id)
{
    if (!function_exists('imagecreatefromstring')) return false;
    $path = get_attached_file($attachment_id);
    if (!$path || !file_exists($path)) return false;

    $data = file_get_contents($path);
    $img = @imagecreatefromstring($data);
    if (!$img) return false;

    $w = imagesx($img);
    $h = imagesy($img);
    if ($w < 100 || $h < 100) { imagedestroy($img); return false; }

    // Buscar límites del contenido no-blanco. Algunas imágenes traen un
    // rectángulo fantasma de 1-3px cerca del borde: una línea fina seguida de
    // más blanco no es contenido, se atraviesa y se sigue buscando.
    $advance = function ($from, $to, $stepDir, $lineLen, $vertical) use ($img) {
        $i = $from;
        while ($i !== $to) {
            if (refugios_scanline_is_white($img, $i, $lineLen, $vertical)) {
                $i += $stepDir;
                continue;
            }
            // Línea no blanca: medir el grosor de la franja
            $run = 0;
            $j = $i;
            while ($j !== $to && !refugios_scanline_is_white($img, $j, $lineLen, $vertical) && $run <= 6) {
                $j += $stepDir;
                $run++;
            }
            if ($run > 5) return $i; // franja gruesa: contenido real
            // Franja fina: ¿la siguen al menos 12 líneas blancas? → fantasma
            $white_after = 0;
            $k = $j;
            while ($k !== $to && $white_after < 12 && refugios_scanline_is_white($img, $k, $lineLen, $vertical)) {
                $k += $stepDir;
                $white_after++;
            }
            if ($white_after >= 12) { $i = $j; continue; }
            return $i;
        }
        return $i;
    };

    $top = $advance(0, $h - 1, 1, $w, false);
    $bottom = $advance($h - 1, $top, -1, $w, false);
    $left = $advance(0, $w - 1, 1, $h, true);
    $right = $advance($w - 1, $left, -1, $h, true);

    $cw = $right - $left + 1;
    $ch = $bottom - $top + 1;

    // Solo vale la pena si el marco es real: contenido entre 10% y 88% del área
    $ratio = ($cw * $ch) / ($w * $h);
    if ($ratio > 0.88 || $ratio < 0.10 || $cw < 80 || $ch < 80) {
        imagedestroy($img);
        return false;
    }

    // Respiro del 2% alrededor del contenido
    $pad = (int) round(max($cw, $ch) * 0.02);
    $left = max(0, $left - $pad);
    $top = max(0, $top - $pad);
    $cw = min($w - $left, $cw + 2 * $pad);
    $ch = min($h - $top, $ch + 2 * $pad);

    $crop = imagecrop($img, ['x' => $left, 'y' => $top, 'width' => $cw, 'height' => $ch]);
    imagedestroy($img);
    if (!$crop) return false;

    $saved = preg_match('/\.png$/i', $path)
        ? imagepng($crop, $path, 9)
        : imagejpeg($crop, $path, 88);
    imagedestroy($crop);
    if (!$saved) return false;

    // Regenerar todos los tamaños derivados
    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $path));
    return true;
}

/** Portadas nuevas del sync: recorte automático al subirse a un producto. */
function refugios_trim_on_upload($attachment_id)
{
    $parent = get_post_parent($attachment_id);
    if ($parent && $parent->post_type === 'product' && wp_attachment_is_image($attachment_id)) {
        refugios_trim_cover($attachment_id);
    }
}
add_action('add_attachment', 'refugios_trim_on_upload', 20);

/**
 * Pasada manual sobre portadas existentes (admins):
 * /?refugios_trim=1&offset=0 — procesa 40 por llamada y enlaza la siguiente.
 */
function refugios_manual_trim_pass()
{
    if (!isset($_GET['refugios_trim']) || !current_user_can('manage_options')) return;

    $offset = max(0, (int) ($_GET['offset'] ?? 0));
    $batch = 40;
    $products = get_posts([
        'post_type' => 'product',
        'post_status' => 'any',
        'numberposts' => $batch,
        'offset' => $offset,
        'fields' => 'ids',
        'orderby' => 'ID',
        'order' => 'ASC',
    ]);

    $trimmed = 0;
    foreach ($products as $pid) {
        $thumb = get_post_thumbnail_id($pid);
        if ($thumb && refugios_trim_cover($thumb)) $trimmed++;
    }

    $next = count($products) === $batch
        ? home_url('/?refugios_trim=1&offset=' . ($offset + $batch))
        : null;

    if (!$next && has_action('litespeed_purge_all')) do_action('litespeed_purge_all');

    header('Content-Type: text/html; charset=utf-8');
    echo '<p>Revisados ' . count($products) . ' productos (offset ' . $offset . '), recortadas ' . $trimmed . ' portadas.</p>';
    echo $next
        ? '<p><a href="' . esc_url($next) . '">Continuar con el siguiente lote →</a></p>'
        : '<p><strong>Pasada completa.</strong> Caché purgada.</p>';
    exit;
}
add_action('template_redirect', 'refugios_manual_trim_pass');

/* =========================================================
 14. MEDIOS: REPORTE Y LIMPIEZA DE IMÁGENES HUÉRFANAS
 Huérfana = adjunta a un producto pero ya no es su portada
 ni parte de su galería (quedó atrás al reemplazar portadas).
 El borrado es definitivo y SOLO corre si el admin lo
 confirma con el enlace.
 ========================================================= */

function refugios_orphan_media_ids()
{
    // Conjunto de imágenes realmente en uso por productos (portada + galería)
    $used = [];
    $products = get_posts([
        'post_type' => 'product', 'post_status' => 'any',
        'numberposts' => -1, 'fields' => 'ids',
    ]);
    foreach ($products as $pid) {
        $t = (int) get_post_thumbnail_id($pid);
        if ($t) $used[$t] = true;
        foreach (array_filter(explode(',', (string) get_post_meta($pid, '_product_image_gallery', true))) as $g) {
            $used[(int) $g] = true;
        }
    }

    $orphans = [];
    $attachments = get_posts([
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'numberposts' => -1,
        'fields' => 'id=>parent',
    ]);
    foreach ($attachments as $att_id => $parent_id) {
        $att_id = (int) $att_id;
        if (isset($used[$att_id])) continue;

        if ($parent_id) {
            // Adjunta a un producto pero ya no es su portada ni galería
            $parent = get_post($parent_id);
            if ($parent && $parent->post_type === 'product') $orphans[] = $att_id;
            continue;
        }

        // Sin adjuntar: solo las que subió el sync (nombre = hash del CDN de
        // Alegra), para no tocar imágenes del blog o del tema.
        $file = basename((string) get_attached_file($att_id));
        if (preg_match('/^[0-9a-f]{40}-/', $file)) $orphans[] = $att_id;
    }
    return $orphans;
}

function refugios_media_cleanup()
{
    if (!isset($_GET['refugios_media_report']) || !current_user_can('manage_options')) return;

    $orphans = refugios_orphan_media_ids();
    header('Content-Type: text/html; charset=utf-8');

    if (isset($_GET['confirm']) && $_GET['confirm'] === '1') {
        $deleted = 0;
        foreach (array_slice($orphans, 0, 150) as $att_id) {
            if (wp_delete_attachment($att_id, true)) $deleted++;
        }
        $rest = count($orphans) - $deleted;
        echo '<p>Eliminadas ' . $deleted . ' imágenes huérfanas.</p>';
        echo $rest > 0
            ? '<p><a href="' . esc_url(home_url('/?refugios_media_report=1&confirm=1')) . '">Quedan ' . $rest . ' — continuar →</a></p>'
            : '<p><strong>Biblioteca limpia.</strong></p>';
        exit;
    }

    echo '<p>Imágenes huérfanas de productos (reemplazadas, sin uso): <strong>' . count($orphans) . '</strong></p>';
    echo '<p>Esto las borra DEFINITIVAMENTE de la biblioteca. ';
    echo '<a href="' . esc_url(home_url('/?refugios_media_report=1&confirm=1')) . '">Confirmar borrado →</a></p>';
    exit;
}
add_action('template_redirect', 'refugios_media_cleanup');

/* =========================================================
 15. CONVERSIÓN — CONFIANZA, META DESCRIPTIONS Y SCHEMA BOOK
 ========================================================= */

/**
 * Franja de confianza bajo el botón de compra del producto:
 * métodos de pago reales de la tienda + envíos. Lo que más pesa
 * en la decisión de primera compra en Colombia.
 */
function refugios_trust_strip()
{
    ?>
    <div class="refugios-trust">
        <div class="refugios-trust__item">
            <i class="fa-solid fa-lock" aria-hidden="true"></i>
            <span><strong><?php esc_html_e('Pago 100% seguro', 'refugios'); ?></strong> · Wompi · Bancolombia</span>
        </div>
        <div class="refugios-trust__item">
            <i class="fa-regular fa-credit-card" aria-hidden="true"></i>
            <span><?php esc_html_e('Todas las tarjetas · Addi · Sistecrédito · PSE', 'refugios'); ?></span>
        </div>
        <div class="refugios-trust__item">
            <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
            <span><?php esc_html_e('Envíos a toda Colombia desde nuestra librería en Itagüí', 'refugios'); ?></span>
        </div>
    </div>
    <?php
}
add_action('woocommerce_single_product_summary', 'refugios_trust_strip', 35);

/**
 * Meta description: nunca la ficha técnica. Si RankMath no tiene una
 * descripción editada a mano, se arma con el inicio de la descripción
 * real del libro (la reescrita con IA).
 */
function refugios_meta_description($description)
{
    if (!is_singular('product')) return $description;

    $is_junk = !$description
        || refugios_text_is_technical($description)
        || mb_strlen(trim($description)) < 40;
    if (!$is_junk) return $description;

    $product = wc_get_product(get_the_ID());
    if (!$product) return $description;

    $body = wp_strip_all_tags($product->get_description());
    if (!$body || mb_strlen($body) < 40) return $description;

    $meta = mb_substr($body, 0, 158);
    // Cortar en la última palabra completa
    $cut = mb_strrpos($meta, ' ');
    if ($cut !== false && $cut > 100) $meta = mb_substr($meta, 0, $cut);
    return trim($meta) . '…';
}
add_filter('rank_math/frontend/description', 'refugios_meta_description', 20);

/**
 * Schema de producto enriquecido para libros: Product + Book con autor
 * e ISBN (resultados enriquecidos específicos de libros en Google).
 * Extiende el JSON-LD que ya emite refugios_seo_head.
 */
function refugios_book_schema_filter($schema)
{
    return $schema; // reservado: el JSON-LD se ajusta directo en refugios_seo_head
}

/**
 * Título de relacionados con intención de venta.
 */
add_filter('woocommerce_product_related_products_heading', fn() => __('También te puede gustar', 'refugios'));

/**
 * Recomendaciones en el carrito: libros de las mismas colecciones de lo
 * que ya está en el carrito. El momento de mayor intención de compra.
 */
function refugios_cart_recommendations()
{
    if (!function_exists('WC') || WC()->cart->is_empty()) return;

    $in_cart = [];
    $cat_ids = [];
    foreach (WC()->cart->get_cart() as $item) {
        $in_cart[] = $item['product_id'];
        foreach (wc_get_product_term_ids($item['product_id'], 'product_cat') as $t) {
            $cat_ids[] = $t;
        }
    }
    if (!$cat_ids) return;

    $recs = wc_get_products([
        'status' => 'publish',
        'limit' => 4,
        'category' => array_map(
            fn($t) => get_term($t, 'product_cat')->slug ?? '',
            array_unique($cat_ids)
        ),
        'exclude' => $in_cart,
        'stock_status' => 'instock',
        'orderby' => 'rand',
    ]);
    if (!$recs) return;

    echo '<section class="refugios-cart-recs"><h2>' . esc_html__('Completa tu pedido', 'refugios') . '</h2>';
    echo '<ul class="products columns-4">';
    foreach ($recs as $rec) {
        $post_object = get_post($rec->get_id());
        setup_postdata($GLOBALS['post'] = $post_object);
        wc_get_template_part('content', 'product');
    }
    wp_reset_postdata();
    echo '</ul></section>';
}
add_action('woocommerce_cart_collaterals', 'refugios_cart_recommendations', 15);

/* =========================================================
 16. ENVÍO GRATIS DESDE $150.000 — BARRA DE PROGRESO
 ========================================================= */

const REFUGIOS_ENVIO_GRATIS = 150000;

function refugios_free_shipping_bar()
{
    if (!function_exists('WC') || WC()->cart->is_empty()) return;

    $subtotal = (float) WC()->cart->get_displayed_subtotal();
    $meta = REFUGIOS_ENVIO_GRATIS;
    $pct = min(100, round($subtotal / $meta * 100));
    $falta = max(0, $meta - $subtotal);
    ?>
    <div class="refugios-envio-bar" role="status">
        <p class="refugios-envio-bar__text">
            <?php if ($falta > 0): ?>
                <?php printf(
                    /* translators: %s: monto faltante */
                    esc_html__('Te faltan %s para el envío gratis', 'refugios'),
                    '<strong>' . wp_kses_post(wc_price($falta)) . '</strong>'
                ); ?>
            <?php else: ?>
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                <strong><?php esc_html_e('¡Tienes envío gratis! 🎉', 'refugios'); ?></strong>
            <?php endif; ?>
        </p>
        <div class="refugios-envio-bar__track">
            <div class="refugios-envio-bar__fill<?php echo $falta <= 0 ? ' is-full' : ''; ?>"
                 style="width: <?php echo esc_attr($pct); ?>%;"></div>
            <span class="refugios-envio-bar__truck<?php echo $falta <= 0 ? ' is-full' : ''; ?>"
                  style="left: <?php echo esc_attr($pct); ?>%;" aria-hidden="true">
                <i class="fa-solid fa-truck-fast"></i>
            </span>
        </div>
    </div>
    <?php
}
add_action('woocommerce_before_cart', 'refugios_free_shipping_bar', 5);
add_action('woocommerce_before_checkout_form', 'refugios_free_shipping_bar', 5);

/* =========================================================
 17. CORREO DE RESEÑA — 7 DÍAS DESPUÉS DEL PEDIDO COMPLETADO
 ========================================================= */

/** Al completarse el pedido, se agenda el correo una sola vez. */
function refugios_schedule_review_email($order_id)
{
    if (get_post_meta($order_id, '_refugios_review_email', true)) return;
    update_post_meta($order_id, '_refugios_review_email', 'programado');
    wp_schedule_single_event(time() + 7 * DAY_IN_SECONDS, 'refugios_send_review_email', [$order_id]);
}
add_action('woocommerce_order_status_completed', 'refugios_schedule_review_email');

function refugios_send_review_email_handler($order_id)
{
    $order = wc_get_order($order_id);
    if (!$order) return;
    if (get_post_meta($order_id, '_refugios_review_email', true) === 'enviado') return;

    $email = $order->get_billing_email();
    if (!$email) return;
    $nombre = $order->get_billing_first_name() ?: __('lector', 'refugios');

    // Lista de libros del pedido con enlace directo a su sección de reseñas
    $items_html = '';
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if (!$product) continue;
        $url = get_permalink($product->get_id()) . '#reviews';
        $items_html .= '<li style="margin:0 0 10px;">'
            . '<a href="' . esc_url($url) . '" style="color:#4e342e;font-weight:bold;">'
            . esc_html($product->get_name()) . '</a></li>';
    }
    if (!$items_html) return;

    $asunto = sprintf(__('%s, ¿qué te pareció tu lectura?', 'refugios'), $nombre);

    $cuerpo = '
    <div style="background:#f5e9e2;padding:32px 16px;font-family:Georgia,serif;color:#4e342e;">
      <div style="max-width:520px;margin:0 auto;background:#fff;border:2px solid #4e342e;padding:32px;">
        <h1 style="font-size:22px;margin:0 0 16px;">Hola, ' . esc_html($nombre) . ' 👋</h1>
        <p style="line-height:1.6;">Hace unos días te llevaste esto de Refugios y queremos saber:
        ¿qué tal estuvo? Tu opinión ayuda a otros lectores a encontrar su próximo libro
        — y a nosotros a seguir curando bien la colección.</p>
        <ul style="line-height:1.6;padding-left:18px;">' . $items_html . '</ul>
        <p style="line-height:1.6;">Déjanos tu reseña con un clic en el título. Dos líneas bastan.</p>
        <p style="margin-top:24px;font-size:13px;color:#8a6f66;">Con café,<br>el equipo de Refugios<br>
        Librería &amp; Café · Itagüí</p>
      </div>
    </div>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: Refugios <' . get_option('admin_email') . '>',
    ];
    if (wp_mail($email, $asunto, $cuerpo, $headers)) {
        update_post_meta($order_id, '_refugios_review_email', 'enviado');
    }
}
add_action('refugios_send_review_email', 'refugios_send_review_email_handler');

/* =========================================================
 18. CARRITO ABANDONADO — CAPTURA + CORREO DE RESCATE (4h)
 Apenas el cliente escribe su correo en el checkout se guarda
 una foto del carrito. Si en 4 horas no hay pedido con ese
 correo, se envía UN correo de rescate. Comprar lo cancela.
 ========================================================= */

/** Post type interno para las capturas (sin UI). */
function refugios_acart_cpt()
{
    register_post_type('refugios_acart', [
        'public' => false,
        'show_ui' => false,
        'supports' => ['title'],
    ]);
}
add_action('init', 'refugios_acart_cpt');

/** JS de captura: manda el correo del checkout por AJAX al escribirlo. */
function refugios_acart_capture_js()
{
    if (!function_exists('is_checkout') || !is_checkout() || is_wc_endpoint_url()) return;
    $js = "
    (function(){
      var f=document.getElementById('billing_email');
      if(!f||!window.refugiosData)return;
      var sent='';
      function capture(){
        var v=f.value.trim();
        if(!v||v===sent||!/^[^@\\s]+@[^@\\s]+\\.[^@\\s]+$/.test(v))return;
        sent=v;
        var d=new FormData();
        d.append('action','refugios_acart');
        d.append('nonce',refugiosData.nonce);
        d.append('email',v);
        fetch(refugiosData.ajaxUrl,{method:'POST',body:d,credentials:'same-origin'});
      }
      f.addEventListener('blur',capture);
      f.addEventListener('change',capture);
      if(f.value)capture();
    })();";
    wp_add_inline_script('refugios-main-v5', $js);
}
add_action('wp_enqueue_scripts', 'refugios_acart_capture_js', 20);

/** AJAX: guarda/actualiza la captura y programa el correo a +4h. */
function refugios_acart_capture()
{
    check_ajax_referer('refugios_nonce', 'nonce');
    $email = sanitize_email($_POST['email'] ?? '');
    if (!is_email($email) || !function_exists('WC') || WC()->cart->is_empty()) {
        wp_send_json_success(); // silencio: esto jamás debe romper el checkout
    }

    $items = [];
    foreach (WC()->cart->get_cart() as $item) {
        $items[] = ['id' => (int) $item['product_id'], 'qty' => (int) $item['quantity']];
    }

    // Una captura activa por correo: se reutiliza y se reprograma
    $existing = get_posts([
        'post_type' => 'refugios_acart',
        'meta_key' => '_acart_email',
        'meta_value' => $email,
        'post_status' => 'any',
        'numberposts' => 1,
        'fields' => 'ids',
    ]);

    if ($existing) {
        $post_id = $existing[0];
        wp_clear_scheduled_hook('refugios_acart_send', [$post_id]);
    } else {
        $post_id = wp_insert_post([
            'post_type' => 'refugios_acart',
            'post_title' => $email,
            'post_status' => 'private',
        ]);
        if (!$post_id || is_wp_error($post_id)) wp_send_json_success();
        update_post_meta($post_id, '_acart_email', $email);
    }

    update_post_meta($post_id, '_acart_items', wp_json_encode($items));
    update_post_meta($post_id, '_acart_status', 'pendiente');
    update_post_meta($post_id, '_acart_time', time());
    wp_schedule_single_event(time() + 4 * HOUR_IN_SECONDS, 'refugios_acart_send', [$post_id]);
    wp_send_json_success();
}
add_action('wp_ajax_refugios_acart', 'refugios_acart_capture');
add_action('wp_ajax_nopriv_refugios_acart', 'refugios_acart_capture');

/** Compró: cancelar el rescate pendiente de ese correo. */
function refugios_acart_on_order($order_id)
{
    $order = wc_get_order($order_id);
    if (!$order) return;
    $email = $order->get_billing_email();
    if (!$email) return;
    $captures = get_posts([
        'post_type' => 'refugios_acart',
        'meta_key' => '_acart_email',
        'meta_value' => $email,
        'post_status' => 'any',
        'numberposts' => 5,
        'fields' => 'ids',
    ]);
    foreach ($captures as $pid) {
        if (get_post_meta($pid, '_acart_status', true) === 'pendiente') {
            update_post_meta($pid, '_acart_status', 'recuperado');
            wp_clear_scheduled_hook('refugios_acart_send', [$pid]);
        }
    }
}
add_action('woocommerce_new_order', 'refugios_acart_on_order');

/** El correo de rescate. */
function refugios_acart_send_handler($post_id)
{
    if (get_post_meta($post_id, '_acart_status', true) !== 'pendiente') return;
    $email = get_post_meta($post_id, '_acart_email', true);
    $since = (int) get_post_meta($post_id, '_acart_time', true);
    if (!is_email($email)) return;

    // Doble chequeo: ¿pidió algo después de la captura?
    $orders = wc_get_orders([
        'billing_email' => $email,
        'date_created' => '>' . ($since - 60),
        'limit' => 1,
        'return' => 'ids',
    ]);
    if ($orders) {
        update_post_meta($post_id, '_acart_status', 'recuperado');
        return;
    }

    $items = json_decode((string) get_post_meta($post_id, '_acart_items', true), true) ?: [];
    $items_html = '';
    $subtotal = 0;
    foreach ($items as $it) {
        $product = wc_get_product($it['id'] ?? 0);
        if (!$product || !$product->is_in_stock()) continue;
        $qty = max(1, (int) ($it['qty'] ?? 1));
        $subtotal += (float) $product->get_price() * $qty;
        $author = function_exists('refugios_get_product_author') ? refugios_get_product_author($product) : '';
        $items_html .= '<p style="margin:0 0 10px;line-height:1.5;">📖 <strong>'
            . esc_html($product->get_name()) . '</strong>'
            . ($author && $author !== 'Autor desconocido' ? ' — ' . esc_html($author) : '')
            . '<br><span style="font-family:Arial,sans-serif;font-size:14px;">'
            . wp_strip_all_tags(wc_price($product->get_price())) . '</span></p>';
    }
    if (!$items_html) {
        update_post_meta($post_id, '_acart_status', 'sin_items');
        return;
    }

    $falta = max(0, REFUGIOS_ENVIO_GRATIS - $subtotal);
    $envio_html = $falta > 0
        ? 'Te faltan <strong style="color:#4e342e;">' . wp_strip_all_tags(wc_price($falta)) . '</strong> para el envío gratis 🚚'
        : '<strong style="color:#2e7d52;">¡Este pedido ya tiene envío gratis! 🚚</strong>';

    $phone = get_theme_mod('refugios_phone', '');
    $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $phone);
    $cart_url = wc_get_cart_url();

    $asunto = __('Tus libros siguen esperándote 📚', 'refugios');
    $cuerpo = '
    <div style="background:#f5e9e2;padding:36px 16px;font-family:Georgia,serif;color:#4e342e;">
      <div style="max-width:520px;margin:0 auto;background:#fff;border:2px solid #4e342e;padding:36px;">
        <p style="margin:0 0 4px;font-family:Arial,sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#d9a066;font-weight:bold;">Refugios · Librería &amp; Café</p>
        <h1 style="font-size:24px;line-height:1.25;margin:0 0 18px;">' . esc_html__('Tus libros siguen esperándote en la mesa 📚', 'refugios') . '</h1>
        <p style="line-height:1.65;margin:0 0 18px;">' . esc_html__('Dejaste esto apartado y no queremos que se te pierda entre las pestañas del navegador. En la librería, cuando alguien deja un libro sobre la mesa, se lo guardamos un ratico. Esto es lo mismo, pero digital:', 'refugios') . '</p>
        <div style="border:2px solid #4e342e;background:#f5e9e2;padding:16px 20px;margin:0 0 20px;">'
          . $items_html .
          '<p style="margin:14px 0 0;padding-top:12px;border-top:1px solid #d9a066;font-family:Arial,sans-serif;font-size:13px;color:#8a6f66;">' . $envio_html . '</p>
        </div>
        <div style="text-align:center;margin:0 0 22px;">
          <a href="' . esc_url($cart_url) . '" style="display:inline-block;background:#d9a066;color:#4e342e;border:2px solid #4e342e;font-family:Arial,sans-serif;font-size:13px;font-weight:bold;letter-spacing:1.5px;text-transform:uppercase;text-decoration:none;padding:14px 28px;">' . esc_html__('Retomar mi carrito', 'refugios') . '</a>
        </div>
        <p style="line-height:1.65;margin:0 0 6px;font-size:15px;">' . esc_html__('Pago seguro con Wompi y Bancolombia · todas las tarjetas · Addi y Sistecrédito si prefieres cuotas.', 'refugios') . '</p>
        <p style="margin-top:26px;font-size:13px;color:#8a6f66;line-height:1.5;">' . esc_html__('Si ya compraste o cambiaste de idea, ignora este correo sin culpa.', 'refugios') . '<br><br>'
          . esc_html__('Con café,', 'refugios') . '<br>' . esc_html__('el equipo de Refugios', 'refugios') . '<br>'
          . '<a href="' . esc_url($wa) . '" style="color:#8a6f66;">' . esc_html__('¿Alguna duda? Escríbenos por WhatsApp', 'refugios') . '</a></p>
      </div>
    </div>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: Refugios <' . get_option('admin_email') . '>',
    ];
    if (wp_mail($email, $asunto, $cuerpo, $headers)) {
        update_post_meta($post_id, '_acart_status', 'enviado');
    }
}
add_action('refugios_acart_send', 'refugios_acart_send_handler');


/**
 * Insignia de oferta con el porcentaje real ("-15%") en vez del
 * "¡Oferta!" genérico de WooCommerce.
 */
function refugios_sale_flash($html, $post, $product)
{
    if ($product && $product->is_type('simple') && $product->is_on_sale()) {
        $regular = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();
        if ($regular > 0 && $sale > 0 && $sale < $regular) {
            $pct = round((1 - $sale / $regular) * 100);
            return '<span class="onsale">-' . $pct . '%</span>';
        }
    }
    return $html;
}
add_filter('woocommerce_sale_flash', 'refugios_sale_flash', 10, 3);

/* =========================================================
 19. ACTUALIZACIÓN DEL DOCUMENTO LEGAL (una sola corrida)
 Corrige la dirección desactualizada del responsable del
 tratamiento de datos y agrega el Programa de fidelización.
 El contenido vive en un widget de Elementor: se edita el
 meta _elementor_data respetando su estructura, con copia
 de respaldo previa.
 ========================================================= */

const REFUGIOS_LEGAL_PAGE_ID = 3022;

/** Dirección correcta del establecimiento (responsable del tratamiento). */
function refugios_legal_fix_address($html)
{
    // Segunda pasada: completa la dirección ya reemplazada con el centro comercial
    $html = preg_replace(
        '/Calle 51 # 48 (?:-|&#8211;|–) 53, local 5, Itagüí \(Antioquia\)/u',
        'Calle 51 # 48 - 53, local 5, Centro Comercial La Gran Manzana, Itagüí (Antioquia)',
        $html
    );
    return preg_replace(
        '/Carrera\s*50\s*a?\s*#?\s*76\s*sur\s*111(\s*ITAGUI\s*\(ANT\)\.?)?/iu',
        'Calle 51 # 48 - 53, local 5, Centro Comercial La Gran Manzana, Itagüí (Antioquia)',
        $html
    );
}

/** Bloque HTML del programa de fidelización, con el formato del documento. */
function refugios_loyalty_html()
{
    $b = '<p><b>PROGRAMA DE FIDELIZACIÓN DE CLIENTES</b></p>';

    $b .= '<p><b>1. Descripción del programa</b></p>';
    $b .= '<p>Librería Refugios ofrece a sus clientes un programa de fidelización sin costo, que reconoce las compras recurrentes mediante la acumulación de sellos y la entrega de beneficios. El programa consta de dos tarjetas independientes, una de bebidas y una de libros, cuyos sellos se acumulan por separado y no son intercambiables entre sí.</p>';

    $b .= '<p><b>2. Vinculación</b></p>';
    $b .= '<p>La vinculación es automática y gratuita. El cliente queda inscrito cuando realiza una compra en el establecimiento y suministra su número de documento de identidad y su correo electrónico al momento de la facturación. No se requiere inscripción previa ni tarjeta física: la acumulación se registra a nombre del documento de identidad reportado en la factura.</p>';
    $b .= '<p>Las compras facturadas a consumidor final, sin documento de identidad identificado, no acumulan sellos y no pueden asignarse posteriormente.</p>';

    $b .= '<p><b>3. Acumulación de sellos</b></p>';
    $b .= '<p><b>Tarjeta de bebidas.</b> Cada bebida preparada en barra equivale a un (1) sello, sin importar su tipo o precio. Si en una misma compra se adquieren varias bebidas, se acumula un sello por cada una.</p>';
    $b .= '<p>No acumulan sellos las bebidas envasadas o embotelladas, incluidas el agua embotellada, los tés y las sodas de marca comercial.</p>';
    $b .= '<p><b>Tarjeta de libros.</b> Cada libro adquirido equivale a un (1) sello. Si en una misma compra se adquieren varios libros, se acumula un sello por cada uno.</p>';
    $b .= '<p>No acumulan sellos los libros de segunda mano ni los pedidos especiales encargados a solicitud del cliente.</p>';
    $b .= '<p><b>Exclusión general.</b> No acumulan sellos los productos entregados como cortesía, los obtenidos con el cien por ciento (100%) de descuento, ni los redimidos como premio de este mismo programa.</p>';

    $b .= '<p><b>4. Beneficios</b></p>';
    $b .= '<table class="refugios-legal-table"><thead><tr>'
        . '<th>Tarjeta</th><th>Sellos requeridos</th><th>Beneficio</th>'
        . '</tr></thead><tbody>'
        . '<tr><td>Bebidas</td><td>9</td><td>Un (1) café americano sin costo</td></tr>'
        . '<tr><td>Libros</td><td>5</td><td>Veinticinco por ciento (25%) de descuento sobre un (1) libro</td></tr>'
        . '</tbody></table>';
    $b .= '<p>Al completar los sellos requeridos, el beneficio queda disponible para su redención y la tarjeta correspondiente reinicia su conteo en cero.</p>';

    $b .= '<p><b>5. Redención</b></p>';
    $b .= '<p>Los beneficios se redimen únicamente de forma presencial en el establecimiento, presentando el documento de identidad del titular. Los beneficios son personales e intransferibles, no son canjeables por dinero en efectivo, no son acumulables entre sí y no son acumulables con otras promociones vigentes.</p>';
    $b .= '<p>El descuento sobre libros aplica sobre un único ejemplar por beneficio redimido y no aplica sobre libros de segunda mano ni sobre pedidos especiales.</p>';

    $b .= '<p><b>6. Vigencia</b></p>';
    $b .= '<p>Los sellos acumulados y los beneficios obtenidos no tienen fecha de vencimiento mientras el programa se encuentre vigente. El cliente podrá redimirlos en la oportunidad que prefiera, sin límite de tiempo.</p>';

    $b .= '<p><b>7. Comunicaciones</b></p>';
    $b .= '<p>Al vincularse al programa, el cliente autoriza a Librería Refugios a enviarle comunicaciones por correo electrónico relacionadas con su tarjeta: confirmación de vinculación, avisos de progreso, notificación de beneficios obtenidos y recomendaciones de lectura.</p>';
    $b .= '<p>El cliente podrá solicitar en cualquier momento la suspensión de estas comunicaciones respondiendo a cualquiera de los correos recibidos o escribiendo a administracion@refugios.co, sin que ello afecte los sellos ya acumulados.</p>';

    $b .= '<p><b>8. Retiro del programa</b></p>';
    $b .= '<p>El cliente podrá solicitar su retiro del programa en cualquier momento. El retiro implica la eliminación de sus sellos acumulados y de los beneficios no redimidos, y no afecta su condición de cliente ni la información de facturación que Librería Refugios debe conservar por obligación legal.</p>';

    $b .= '<p><b>9. Exclusiones de participación</b></p>';
    $b .= '<p>No podrán participar en el programa los empleados de Librería Refugios ni sus establecimientos asociados.</p>';

    $b .= '<p><b>10. Tratamiento de datos</b></p>';
    $b .= '<p>Los datos personales suministrados para el programa —nombre, documento de identidad y correo electrónico— serán tratados conforme a la Ley 1581 de 2012 y a la Política de Privacidad contenida en este mismo documento, con la finalidad exclusiva de administrar el programa de fidelización y enviar las comunicaciones descritas en el numeral 7.</p>';

    $b .= '<p><b>11. Modificación y terminación</b></p>';
    $b .= '<p>Librería Refugios podrá modificar las condiciones del programa o darlo por terminado en cualquier momento, informándolo a través de este sitio web con una antelación no inferior a treinta (30) días calendario. Los beneficios ya obtenidos y no vencidos al momento de la terminación podrán redimirse dentro del plazo informado.</p>';

    $b .= '<p><b>12. Contacto</b></p>';
    $b .= '<p>Para cualquier consulta, reclamación o solicitud relacionada con el programa:</p>';
    $b .= '<p>Librería Refugios<br>Calle 51 # 48 - 53, local 5, Centro Comercial La Gran Manzana, Itagüí (Antioquia)<br>'
        . 'administracion@refugios.co<br>+57 323 811 39 85<br>'
        . '<a href="https://www.refugios.co">www.refugios.co</a></p>';

    return $b;
}

/**
 * Inserta el programa antes del Anexo 1; si no lo encuentra, al final.
 */
function refugios_legal_insert_loyalty($html)
{
    if (stripos($html, 'PROGRAMA DE FIDELIZACIÓN') !== false) {
        return $html; // ya está: no duplicar
    }
    $bloque = refugios_loyalty_html();

    $out = preg_replace(
        '/(<p[^>]*>(?:(?!<\/p>).)*?ANEXO\s*1)/isu',
        $bloque . '$1',
        $html,
        1,
        $count
    );
    if ($count > 0 && $out !== null) return $out;

    return $html . $bloque;
}

/** Aplica ambas correcciones a un HTML cualquiera. */
function refugios_legal_transform($html)
{
    return refugios_legal_insert_loyalty(refugios_legal_fix_address($html));
}

/**
 * Recorre la estructura de Elementor y transforma los widgets de texto.
 */
function refugios_legal_walk(array $nodes, &$hits)
{
    foreach ($nodes as &$node) {
        if (isset($node['settings']['editor']) && is_string($node['settings']['editor'])) {
            $before = $node['settings']['editor'];
            $after = refugios_legal_transform($before);
            if ($after !== $before) {
                $node['settings']['editor'] = $after;
                $hits++;
            }
        }
        if (!empty($node['elements']) && is_array($node['elements'])) {
            $node['elements'] = refugios_legal_walk($node['elements'], $hits);
        }
    }
    return $nodes;
}

/**
 * Corrida manual (solo administradores): /?refugios_legal_update=1
 */
function refugios_legal_update()
{
    if (!isset($_GET['refugios_legal_update']) || !current_user_can('manage_options')) return;

    $page_id = REFUGIOS_LEGAL_PAGE_ID;
    header('Content-Type: text/html; charset=utf-8');
    echo '<div style="font-family:system-ui;max-width:640px;margin:40px auto;line-height:1.6;">';

    $raw = get_post_meta($page_id, '_elementor_data', true);
    if (!$raw) {
        echo '<p>No se encontró _elementor_data en la página ' . (int) $page_id . '.</p></div>';
        exit;
    }

    // Respaldo: solo la primera vez, para poder revertir
    if (!get_post_meta($page_id, '_refugios_legal_backup', true)) {
        update_post_meta($page_id, '_refugios_legal_backup', wp_slash($raw));
        echo '<p>Respaldo guardado en el meta <code>_refugios_legal_backup</code>.</p>';
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        echo '<p>No se pudo leer la estructura de Elementor.</p></div>';
        exit;
    }

    $hits = 0;
    $data = refugios_legal_walk($data, $hits);

    if ($hits > 0) {
        $json = wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        update_post_meta($page_id, '_elementor_data', wp_slash($json));

        // Elementor cachea el CSS/HTML renderizado: hay que regenerarlo
        if (class_exists('\\Elementor\\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }
        if (has_action('litespeed_purge_all')) do_action('litespeed_purge_all');
    }

    // El contenido clásico también se corrige, por consistencia de datos
    $post = get_post($page_id);
    if ($post && $post->post_content) {
        $nuevo = refugios_legal_transform($post->post_content);
        if ($nuevo !== $post->post_content) {
            wp_update_post(['ID' => $page_id, 'post_content' => wp_slash($nuevo)]);
        }
    }

    echo '<p><strong>Widgets de texto actualizados: ' . (int) $hits . '</strong></p>';
    echo '<p>Dirección corregida y programa de fidelización insertado.</p>';
    echo '<p><a href="' . esc_url(get_permalink($page_id)) . '">Ver la página →</a></p>';
    echo '</div>';
    exit;
}
add_action('template_redirect', 'refugios_legal_update');

/* =========================================================
 20. PORTADA DE RESPALDO PROPIA
 El "No photo" genérico de WooCommerce rompe la estética.
 Se reemplaza por un lomo de libro dibujado en SVG, con los
 colores de la marca. Sin archivos: viaja como data URI.
 ========================================================= */

function refugios_placeholder_svg()
{
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 600">'
        . '<rect width="400" height="600" fill="#efe0d6"/>'
        // Cuerpo del libro
        . '<rect x="112" y="130" width="176" height="250" rx="3" fill="none" stroke="#4e342e" stroke-width="7"/>'
        // Lomo
        . '<line x1="150" y1="130" x2="150" y2="380" stroke="#4e342e" stroke-width="5"/>'
        // Renglones
        . '<line x1="175" y1="195" x2="258" y2="195" stroke="#d9a066" stroke-width="7" stroke-linecap="round"/>'
        . '<line x1="175" y1="230" x2="240" y2="230" stroke="#d9a066" stroke-width="7" stroke-linecap="round"/>'
        . '<line x1="175" y1="265" x2="252" y2="265" stroke="#d9a066" stroke-width="7" stroke-linecap="round"/>'
        // Marcador
        . '<path d="M236 130 v70 l17-15 17 15 v-70 z" fill="#d9a066" stroke="#4e342e" stroke-width="5" stroke-linejoin="round"/>'
        // Texto
        . '<text x="200" y="440" text-anchor="middle" font-family="Georgia, serif" font-size="27" fill="#4e342e">Refugios</text>'
        . '<text x="200" y="472" text-anchor="middle" font-family="Arial, sans-serif" font-size="15" letter-spacing="2" fill="#a98d80">PORTADA EN CAMINO</text>'
        . '</svg>';

    return 'data:image/svg+xml;charset=utf-8,' . rawurlencode($svg);
}

function refugios_placeholder_src($src)
{
    return refugios_placeholder_svg();
}
add_filter('woocommerce_placeholder_img_src', 'refugios_placeholder_src', 20);

function refugios_placeholder_img($html, $size, $dimensions)
{
    $w = is_array($dimensions) && !empty($dimensions['width']) ? (int) $dimensions['width'] : 400;
    $h = is_array($dimensions) && !empty($dimensions['height']) ? (int) $dimensions['height'] : 600;
    return sprintf(
        '<img src="%s" alt="%s" width="%d" height="%d" class="woocommerce-placeholder wp-post-image" />',
        esc_attr(refugios_placeholder_svg()),
        esc_attr__('Portada aún no disponible', 'refugios'),
        $w,
        $h
    );
}
add_filter('woocommerce_placeholder_img', 'refugios_placeholder_img', 20, 3);

/* =========================================================
 21. ESTILOS CRÍTICOS A PRUEBA DE OPTIMIZADORES
 LiteSpeed genera un CSS "único" que descarta reglas que
 cree no usadas; cuando ese archivo queda viejo, las cards
 de búsqueda pierden su grilla y una portada ocupa toda la
 pantalla. Estas reglas viajan inline y marcadas para que
 el optimizador no las toque.
 ========================================================= */

function refugios_critical_css()
{
    ?>
<style data-no-optimize="1" data-optimized="0" id="refugios-critical">
.refugios-search-products{list-style:none;margin:0 0 1rem;padding:0;display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem}
.refugios-search-products>li.product{width:100%;max-width:100%;float:none;margin:0}
.refugios-product-card__media{display:block!important;position:relative!important;aspect-ratio:2/3!important;overflow:hidden!important;background:linear-gradient(160deg,#efe0d6,#e2cfc2)!important;padding:.9rem!important;box-sizing:border-box!important;border-bottom:2px solid #4e342e!important}
.refugios-product-card__media img{width:100%!important;height:100%!important;object-fit:contain!important;object-position:center!important;display:block!important;margin:0!important}
.woocommerce div.product div.images{max-width:460px!important;margin-left:auto!important;margin-right:auto!important}
.woocommerce div.product div.images .woocommerce-product-gallery__image{aspect-ratio:2/3!important;display:flex!important;align-items:center!important;justify-content:center!important;padding:1.5rem!important;box-sizing:border-box!important;background:linear-gradient(160deg,#efe0d6,#e2cfc2)!important;overflow:hidden!important}
.woocommerce div.product div.images .woocommerce-product-gallery__image a{display:flex!important;align-items:center!important;justify-content:center!important;width:100%!important;height:100%!important}
.woocommerce div.product div.images .woocommerce-product-gallery__image img,.woocommerce div.product div.images .woocommerce-product-gallery__image a img{width:auto!important;height:auto!important;max-width:100%!important;max-height:100%!important;object-fit:contain!important;border:none!important}
.woocommerce span.onsale,.woocommerce div.product span.onsale{position:absolute!important;top:.9rem!important;left:.9rem!important;right:auto!important;z-index:30!important;margin:0!important;display:inline-block!important;background:#d9a066!important;color:#4e342e!important;border:2px solid #4e342e!important;box-shadow:2px 2px 0 #4e342e!important;border-radius:0!important;font-family:Montserrat,Arial,sans-serif!important;font-size:.8rem!important;font-weight:800!important;letter-spacing:.08em!important;line-height:1!important;min-width:0!important;min-height:0!important;width:auto!important;height:auto!important;padding:.45rem .6rem!important;text-align:center!important}
@media(max-width:1024px){.refugios-search-products{grid-template-columns:repeat(3,1fr)}}
@media(max-width:767px){
.refugios-search-products{grid-template-columns:repeat(2,1fr);gap:.75rem}
.refugios-product-card__media{aspect-ratio:3/4;padding:.75rem}
.woocommerce div.product div.images{max-width:100%}
.woocommerce div.product div.images .woocommerce-product-gallery__image{aspect-ratio:3/4;padding:1rem}
}
.refugios-product-card__actions{display:flex;gap:.5rem;align-items:stretch;position:relative}
.refugios-card-buy{flex:1;display:inline-flex;align-items:center;justify-content:center;gap:.5rem;background:#d9a066;color:#4e342e;border:2px solid #4e342e;font-family:Montserrat,Arial,sans-serif;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;padding:.75rem;text-decoration:none;cursor:pointer;position:relative;z-index:10;box-sizing:border-box}
.refugios-card-buy:hover{background:#4e342e;color:#f5e9e2;box-shadow:3px 3px 0 #4e342e}
.refugios-card-wa{display:inline-flex;align-items:center;justify-content:center;width:2.9rem;min-width:2.9rem;background:#f5e9e2;color:#4e342e;border:2px solid #4e342e;font-size:1.15rem;text-decoration:none;position:relative;z-index:10;box-sizing:border-box}
.refugios-card-wa:hover{background:#25d366;color:#fff;border-color:#4e342e}
.refugios-product-card__actions .added_to_cart{position:absolute;inset:auto 0 -1.6rem 0;font-family:Montserrat,Arial,sans-serif;font-size:.68rem;font-weight:700;text-transform:uppercase;color:#4e342e;text-align:center;z-index:10}
.refugios-product-card__media .onsale{position:absolute;top:.7rem;left:.7rem;right:auto;z-index:5;margin:0;display:inline-block;background:#d9a066;color:#4e342e;border:2px solid #4e342e;box-shadow:2px 2px 0 #4e342e;border-radius:0;font-family:Montserrat,Arial,sans-serif;font-size:.72rem;font-weight:800;letter-spacing:.08em;line-height:1;min-width:0;min-height:0;height:auto;padding:.35rem .5rem;text-align:center}
.refugios-product-card__price ins{text-decoration:none;border-bottom:none}
.refugios-product-card__price del{font-size:.9rem;color:rgba(78,52,46,.55);text-decoration:line-through;margin-right:.45rem}
@media(max-width:420px){.refugios-search-products{grid-template-columns:1fr}}
</style>
    <?php
}
add_action('wp_head', 'refugios_critical_css', 99);

/**
 * La hoja del tema nunca entra al combinado de LiteSpeed.
 * Cuando entraba, cualquier cambio nuevo quedaba fuera hasta que se
 * regenerara el archivo optimizado, y la tienda perdía sus botones.
 */
function refugios_no_optimize_style($tag, $handle)
{
    if (in_array($handle, ['refugios-style-v5', 'refugios-tailwind'], true)) {
        $tag = str_replace('<link ', '<link data-no-optimize="1" data-optimized="0" ', $tag);
    }
    return $tag;
}
add_filter('style_loader_tag', 'refugios_no_optimize_style', 20, 2);
