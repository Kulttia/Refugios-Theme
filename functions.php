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
                        '@type'       => 'Product',
                        '@id'         => get_permalink() . '#product',
                        'name'        => get_the_title(),
                        'description' => wp_strip_all_tags($product->get_short_description()),
                        'sku'         => $product->get_sku(),
                        'image'       => get_the_post_thumbnail_url($post->ID, 'full') ?: '',
                        'offers'      => [
                            '@type'         => 'Offer',
                            'url'           => get_permalink(),
                            'priceCurrency' => 'COP',
                            'price'         => $product->get_price(),
                            'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                            'itemCondition' => 'https://schema.org/NewCondition',
                            'seller'        => ['@id' => $site_url . '#organization'],
                        ],
                    ],
                ],
            ];
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

    // Buscar límites del contenido no-blanco
    $top = 0;
    while ($top < $h - 1 && refugios_scanline_is_white($img, $top, $w, false)) $top++;
    $bottom = $h - 1;
    while ($bottom > $top && refugios_scanline_is_white($img, $bottom, $w, false)) $bottom--;
    $left = 0;
    while ($left < $w - 1 && refugios_scanline_is_white($img, $left, $h, true)) $left++;
    $right = $w - 1;
    while ($right > $left && refugios_scanline_is_white($img, $right, $h, true)) $right--;

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
    $orphans = [];
    $attachments = get_posts([
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'numberposts' => -1,
        'fields' => 'id=>parent',
    ]);
    foreach ($attachments as $att_id => $parent_id) {
        if (!$parent_id) continue;
        $parent = get_post($parent_id);
        if (!$parent || $parent->post_type !== 'product') continue;
        $thumb = (int) get_post_thumbnail_id($parent_id);
        $gallery = array_map('intval', array_filter(explode(',', (string) get_post_meta($parent_id, '_product_image_gallery', true))));
        if ((int) $att_id !== $thumb && !in_array((int) $att_id, $gallery, true)) {
            $orphans[] = (int) $att_id;
        }
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
