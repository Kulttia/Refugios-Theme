<?php
/**
 * Refugios — front-page.php
 * Homepage: Hero → Libros Destacados → La Pausa → Blog → CTA Newsletter
 */

get_header(); ?>

<main id="main-content" role="main">

    <!-- =============================================
         HERO SECTION
         ============================================= -->
    <section class="hero-section" aria-label="<?php esc_attr_e('Sección principal', 'refugios'); ?>">

        <?php
$hero_image = get_theme_mod('refugios_hero_image');
if ($hero_image): ?>
            <img src="<?php echo esc_url($hero_image); ?>"
                 alt=""
                 class="hero-bg"
                 aria-hidden="true"
                 loading="eager"
                 fetchpriority="high">
        <?php
endif; ?>

        <div class="container hero-content">
            <?php
$hero_label = get_theme_mod('refugios_hero_label', 'Libros y Café de Especialidad');
$hero_title = get_theme_mod('refugios_hero_title',
    'Creamos un espacio acogedor, con libros seleccionados, café de origen para volver a disfrutar de la lectura a tu ritmo.');
$cta1_text = get_theme_mod('refugios_hero_cta_1', 'Explorar Libros');
$cta1_url = get_theme_mod('refugios_hero_cta_1_url', 'https://refugios.co/tienda-refugios/');
$cta2_text = get_theme_mod('refugios_hero_cta_2', 'Ver Menú');
$cta2_url = get_theme_mod('refugios_hero_cta_2_url', 'https://menu.refugios.co/');
?>

            <p class="hero-label" aria-label="<?php echo esc_attr($hero_label); ?>">
                <?php echo esc_html($hero_label); ?>
            </p>

            <h1 class="hero-title h1">
                <?php echo esc_html($hero_title); ?>
            </h1>

            <!-- Propósito -->
            <div class="hero-proposito">
                <p class="hero-proposito__label"><?php esc_html_e('Nuestro propósito es:', 'refugios'); ?></p>
                <p class="hero-proposito__text"><?php esc_html_e('Ayudar a personas que han perdido el interés por la lectura a reconectarse con los libros en un espacio físico y virtual acogedor, donde también pueden disfrutar de buen café y alimentos saludables.', 'refugios'); ?></p>
            </div>

            <div class="hero-cta-group">
                <a href="<?php echo esc_url($cta1_url); ?>"
                   class="btn btn-primary"
                   id="hero-cta-primary">
                    <i class="fa-solid fa-book" aria-hidden="true"></i>
                    <?php echo esc_html($cta1_text); ?>
                </a>
                <a href="<?php echo esc_url($cta2_url); ?>"
                   class="btn btn-outline-cream"
                   id="hero-cta-secondary"
                   target="_blank" rel="noopener">
                    <i class="fa-solid fa-mug-saucer" aria-hidden="true"></i>
                    <?php echo esc_html($cta2_text); ?>
                </a>
            </div>
        </div>

        <span class="hero-scroll" aria-hidden="true">
            <?php esc_html_e('Explorar', 'refugios'); ?>
        </span>

    </section><!-- .hero-section -->


    <!-- =============================================
         LIBROS DESTACADOS
         ============================================= -->
    <section class="section-pad" id="libros-destacados" aria-label="<?php esc_attr_e('Libros destacados', 'refugios'); ?>">
        <div class="container">

            <div class="section-header">
                <div class="section-header-left">
                    <p class="section-label"><?php esc_html_e('Colección Curada', 'refugios'); ?></p>
                    <h2 class="h2"><?php esc_html_e('Libros Destacados', 'refugios'); ?></h2>
                </div>
                <?php
$shop_url = function_exists('wc_get_page_permalink')
    ? wc_get_page_permalink('shop')
    : home_url('/tienda-refugios');
?>
                <a href="<?php echo esc_url($shop_url); ?>"
                   class="btn btn-secondary"
                   id="ver-catalogo-btn">
                    <?php esc_html_e('Ver Catálogo Completo', 'refugios'); ?>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <?php
// WooCommerce: featured products
$featured_products = [];
if (function_exists('wc_get_products')) {
    $featured_products = wc_get_products([
        'status' => 'publish',
        'featured' => true,
        'limit' => 6,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
    // If no featured, show latest
    if (empty($featured_products)) {
        $featured_products = wc_get_products([
            'status' => 'publish',
            'limit' => 6,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
    }
}

if (!empty($featured_products)): ?>

                <div class="books-grid" id="featured-books-grid">
                    <?php foreach ($featured_products as $product):
        $product_id = $product->get_id();
        $title = $product->get_name();
        $price = $product->get_price_html();
        $permalink = get_permalink($product_id);
        $img_id = $product->get_image_id();
        $img_url = $img_id
            ? wp_get_attachment_image_url($img_id, 'refugios-book')
            : wc_placeholder_img_src('refugios-book');
        $cat = refugios_product_category($product_id);
        $is_featured = $product->is_featured();
        $is_sale = $product->is_on_sale();
?>

                        <article class="book-card" id="product-<?php echo esc_attr($product_id); ?>">
                            <?php
        $author = refugios_get_product_author($product);

        $quote = $product->get_attribute('frase');
        if (!$quote && $product->get_short_description()) {
            $quote = wp_trim_words(wp_strip_all_tags($product->get_short_description()), 15, '...');
        }
        elseif (!$quote) {
            $quote = 'El buen libro es de todos los siglos.';
        }

        $desc = $product->get_short_description() ?: $product->get_description();
        $desc = wp_trim_words(wp_strip_all_tags($desc), 12, '...');

        $category_list = wc_get_product_category_list($product_id, ', ');

        $phone = get_theme_mod('refugios_phone', '5551234567');
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);
        $wa_url = 'https://wa.me/' . $clean_phone . '?text=' . rawurlencode('Hola, me interesa consultar por el libro: ' . $title);
?>

                            <a href="<?php echo esc_url($permalink); ?>" class="refugios-product-card__media" aria-hidden="true" tabindex="-1">
                                <?php echo $product->get_image('refugios-book'); ?>
                            </a>

                            <div class="refugios-product-card__content">
                                <?php if ($category_list): ?>
                                    <div class="refugios-product-card__cat"><?php echo wp_kses_post($category_list); ?></div>
                                <?php
        endif; ?>
                                
                                <h3 class="refugios-product-card__title">
                                    <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                                </h3>
                                
                                <div class="refugios-product-card__author"><?php echo esc_html($author); ?></div>
                                
                                <div class="refugios-product-card__desc">
                                    <p><?php echo esc_html($desc); ?></p>
                                </div>

                                <div class="refugios-product-card__footer">
                                    <span class="refugios-product-card__price"><?php echo wp_kses_post($price); ?></span>
                                    <a href="<?php echo esc_url($wa_url); ?>" class="refugios-product-card__btn-wa" target="_blank" rel="noopener">
                                        <i class="fa-brands fa-whatsapp"></i> Consultar
                                    </a>
                                </div>
                            </div>

                            <!-- The subtle floating quote on hover -->
                            <div class="refugios-product-card__hover-quote">
                                <p>"<?php echo esc_html($quote); ?>"</p>
                            </div>
                        </article><!-- .refugios-product-card -->

                    <?php
    endforeach; ?>
                </div><!-- .books-grid -->

            <?php
else: ?>

                <!-- Placeholder si no hay WooCommerce o productos -->
                <div class="books-grid" id="featured-books-placeholder">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <article class="book-card">
                            <div class="book-card__image"
                                 style="background: linear-gradient(135deg, #e8d8ce, #d4c0b5); display:flex; align-items:center; justify-content:center; min-height:260px;">
                                <i class="fa-solid fa-book-open"
                                   style="font-size:3rem; color: rgba(78,52,46,0.25);"
                                   aria-hidden="true"></i>
                            </div>
                            <div class="book-card__body">
                                <span class="book-card__category"><?php esc_html_e('Ficción', 'refugios'); ?></span>
                                <h3 class="book-card__title"><?php echo esc_html(sprintf(__('Libro Destacado %d', 'refugios'), $i)); ?></h3>
                                <p class="book-card__author"><?php esc_html_e('Autor', 'refugios'); ?></p>
                                <div class="book-card__footer">
                                    <span class="book-card__price">$00.00</span>
                                    <a href="<?php echo esc_url(home_url('/tienda-refugios')); ?>" class="book-card__btn">
                                        <?php esc_html_e('Ver', 'refugios'); ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php
    endfor; ?>
                </div>

            <?php
endif; ?>

        </div>
    </section><!-- #libros-destacados -->


    <!-- =============================================
         LA PAUSA — CAFÉ SUIZO
         ============================================= -->
    <section class="pausa-section section-pad" id="la-pausa" aria-label="<?php esc_attr_e('Sección café La Pausa', 'refugios'); ?>">
        <div class="container pausa-inner">

            <!-- Text -->
            <div class="pausa-text">

                <h2 class="pausa-heading">
                    <?php echo wp_kses(
    get_theme_mod('refugios_pausa_title', 'El café que acompaña cada <em>página.</em>'),
['em' => [], 'strong' => []]
); ?>
                </h2>

                <p class="pausa-desc">
                    <?php echo esc_html(get_theme_mod('refugios_pausa_desc',
    'En Refugios creemos que cada buen libro merece una buena taza. Seleccionamos cuidadosamente nuestros granos y nuestras historias.')); ?>
                </p>

                <a href="https://share.google/IbIQ900QOHDdCiUCu"
                   class="btn btn-outline-cream"
                   id="ver-menu-btn"
                   target="_blank" rel="noopener">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    <?php esc_html_e('Hacer una Pausa', 'refugios'); ?>
                </a>

                <!-- Stats -->
                <div class="pausa-stats">
                    <?php
$stats = [
    [get_theme_mod('refugios_pausa_stat1_num', '4'), get_theme_mod('refugios_pausa_stat1_label', 'Años')],
    [get_theme_mod('refugios_pausa_stat2_num', '100+'), get_theme_mod('refugios_pausa_stat2_label', 'Libros donados')],
    [get_theme_mod('refugios_pausa_stat3_num', '1'), get_theme_mod('refugios_pausa_stat3_label', 'Tienda')],
];
foreach ($stats as $stat): ?>
                        <div class="pausa-stat">
                            <div class="pausa-stat__num"><?php echo esc_html($stat[0]); ?></div>
                            <div class="pausa-stat__label"><?php echo esc_html($stat[1]); ?></div>
                        </div>
                    <?php
endforeach; ?>
                </div>
            </div>

            <!-- Image -->
            <div class="pausa-image-wrapper">
                <?php
$pausa_img = get_theme_mod('refugios_pausa_image',
    'https://refugios.co/wp-content/uploads/2025/05/PortadaRefugios.png'
);
?>
                <img src="<?php echo esc_url($pausa_img); ?>"
                     alt="<?php esc_attr_e('Portada Refugios — Librería & Café', 'refugios'); ?>"
                     loading="lazy"
                     width="600" height="450">
            </div>
            </div>

        </div>
    </section><!-- #la-pausa -->


    <!-- =============================================
         ÚLTIMAS ENTRADAS DEL BLOG
         ============================================= -->
    <section class="section-pad" id="blog-home" aria-label="<?php esc_attr_e('Últimas entradas del blog', 'refugios'); ?>">
        <div class="container">

            <div class="section-header">
                <div class="section-header-left">
                    <p class="section-label"><?php esc_html_e('Lecturas Recientes', 'refugios'); ?></p>
                    <h2 class="h2"><?php esc_html_e('Del Blog', 'refugios'); ?></h2>
                </div>
                <a href="<?php echo esc_url(home_url('/blog')); ?>"
                   class="btn btn-secondary"
                   id="ver-blog-btn">
                    <?php esc_html_e('Ir al Blog', 'refugios'); ?>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <?php
$blog_posts = new WP_Query([
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 3,
    'orderby' => 'date',
    'order' => 'DESC',
]);
?>

            <?php if ($blog_posts->have_posts()): ?>
                <div class="blog-grid" id="blog-home-grid">
                    <?php while ($blog_posts->have_posts()):
        $blog_posts->the_post(); ?>

                        <article class="blog-card" id="blog-post-<?php the_ID(); ?>">

                            <?php if (has_post_thumbnail()): ?>
                                <a href="<?php the_permalink(); ?>"
                                   class="blog-card__image"
                                   tabindex="-1"
                                   aria-hidden="true">
                                    <?php the_post_thumbnail('refugios-blog', ['loading' => 'lazy']); ?>
                                </a>
                            <?php
        endif; ?>

                            <div class="blog-card__body">
                                <?php $cat = refugios_first_category(); ?>
                                <?php if ($cat): ?>
                                    <span class="blog-card__cat"><?php echo esc_html($cat); ?></span>
                                <?php
        endif; ?>

                                <h3 class="blog-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <p class="blog-card__excerpt">
                                    <?php echo esc_html(wp_trim_words(get_the_excerpt(), 18, '…')); ?>
                                </p>

                                <div class="blog-card__footer">
                                    <span class="blog-card__meta">
                                        <?php echo esc_html(get_the_date('d M Y')); ?>
                                    </span>
                                    <a href="<?php the_permalink(); ?>"
                                       class="blog-card__link">
                                        <?php esc_html_e('Leer más', 'refugios'); ?>
                                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>

                        </article>

                    <?php
    endwhile;
    wp_reset_postdata(); ?>
                </div><!-- .blog-grid -->

            <?php
else: ?>
                <div class="box-brutal" style="text-align:center; padding: 3rem;">
                    <i class="fa-solid fa-feather-pointed"
                       style="font-size:2.5rem; color:var(--color-amber); margin-bottom:1rem; display:block;"
                       aria-hidden="true"></i>
                    <h3 class="h4"><?php esc_html_e('Próximamente', 'refugios'); ?></h3>
                    <p style="font-family: var(--font-body); margin-top: 0.5rem;">
                        <?php esc_html_e('Estamos preparando nuevas lecturas. ¡Vuelve pronto!', 'refugios'); ?>
                    </p>
                </div>
            <?php
endif; ?>

        </div>
    </section><!-- #blog-home -->


    <!-- =============================================
         CTA + NEWSLETTER
         ============================================= -->
    <section class="cta-section" id="newsletter-cta" aria-label="<?php esc_attr_e('Suscripción y contacto', 'refugios'); ?>">
        <div class="container cta-inner">

            <!-- Left: Copy -->
            <div class="cta-copy">
                <p class="section-label" style="color: var(--color-amber);">
                    <?php esc_html_e('Únete a Refugios', 'refugios'); ?>
                </p>
                <h2 class="cta-heading">
                    <?php esc_html_e('Recibe la ', 'refugios'); ?>
                    <em><?php esc_html_e('Pausa Semanal', 'refugios'); ?></em>
                </h2>
                <p class="cta-desc">
                    <?php esc_html_e('Recomendaciones de libros, lanzamientos, eventos y el café de la semana. Sin spam, solo cultura.', 'refugios'); ?>
                </p>

                <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div class="contact-info-item" style="border-color: rgba(245,233,226,0.15); color: rgba(245,233,226,0.8);">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        <div>
                            <div class="contact-info-label" style="color: rgba(245,233,226,0.5);">
                                <?php esc_html_e('Dirección', 'refugios'); ?>
                            </div>
                            <div class="contact-info-value" style="color: rgba(245,233,226,0.85);">
                                <?php echo esc_html(get_theme_mod('refugios_address', 'Calle 51 · 48 - 53, Medellín')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="contact-info-item" style="border-color: rgba(245,233,226,0.15); color: rgba(245,233,226,0.8);">
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                        <div>
                            <div class="contact-info-label" style="color: rgba(245,233,226,0.5);">
                                <?php esc_html_e('WhatsApp', 'refugios'); ?>
                            </div>
                            <div class="contact-info-value">
                                <a href="<?php echo esc_url(get_theme_mod('refugios_whatsapp', 'https://wa.me/573238113985')); ?>"
                                   style="color: var(--color-amber);">
                                    <?php echo esc_html(get_theme_mod('refugios_phone', '+57 323 811 39 85')); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <a href="<?php echo esc_url(home_url('/contacto')); ?>"
                       class="btn btn-teal"
                       id="contacto-cta-btn"
                       style="border-color: var(--color-amber);">
                        <?php esc_html_e('Ir a Contacto', 'refugios'); ?>
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Right: Newsletter Form -->
            <div class="newsletter-form">
                <span class="newsletter-form__label"><?php esc_html_e('Newsletter', 'refugios'); ?></span>
                <h3 class="newsletter-form__title">
                    <?php esc_html_e('La Pausa Semanal', 'refugios'); ?>
                </h3>
                <p class="newsletter-form__desc">
                    <?php esc_html_e('Cada semana, una recomendación literaria y la historia detrás de una taza.', 'refugios'); ?>
                </p>

                <?php if (shortcode_exists('mc4wp_form')):
    echo do_shortcode('[mc4wp_form id="1"]');
else: ?>
                    <form class="newsletter-form__group"
                          id="newsletter-form"
                          method="post"
                          novalidate>
                        <?php wp_nonce_field('refugios_newsletter', 'nonce'); ?>
                        <label for="newsletter-email" class="visually-hidden">
                            <?php esc_html_e('Tu correo electrónico', 'refugios'); ?>
                        </label>
                        <input type="email"
                               id="newsletter-email"
                               name="email"
                               class="newsletter-form__input"
                               placeholder="<?php esc_attr_e('tu@email.com', 'refugios'); ?>"
                               required
                               autocomplete="email">
                        <button type="submit"
                                class="newsletter-form__submit"
                                id="newsletter-submit">
                            <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                            <span class="visually-hidden"><?php esc_html_e('Suscribirse', 'refugios'); ?></span>
                        </button>
                    </form>
                    <p style="font-family: var(--font-body); font-size: 0.75rem; color: rgba(245,233,226,0.5); margin-top: 0.75rem;">
                        <?php esc_html_e('Sin spam. Cancela cuando quieras. Respondemos en menos de 24h.', 'refugios'); ?>
                    </p>
                <?php
endif; ?>
            </div>

        </div>
    </section><!-- #newsletter-cta -->

</main><!-- #main-content -->

<?php get_footer();
