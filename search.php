<?php
/**
 * Refugios — search.php
 * Resultados de búsqueda separados por naturaleza: los libros se muestran
 * como libros (con su precio y botón de compra) y los artículos del blog
 * como artículos. Antes todo caía en la plantilla de blog y un libro
 * aparecía con fecha, autor y "Leer artículo", que confunde y no vende.
 */

get_header();

$query = get_search_query();

/* Libros primero: en una librería, quien busca suele buscar un libro. */
$productos = [];
if (function_exists('wc_get_products') && $query) {
    $productos = wc_get_products([
        'status' => 'publish',
        's' => $query,
        'limit' => 24,
        'orderby' => 'relevance',
    ]);
}

/* Artículos del blog, excluyendo productos */
$posts_query = new WP_Query([
    'post_type' => 'post',
    's' => $query,
    'posts_per_page' => 10,
    'post_status' => 'publish',
]);

$total = count($productos) + (int) $posts_query->found_posts;
?>

<main id="main-content" role="main">

    <header class="page-header page-header--teal">
        <div class="container">
            <?php refugios_breadcrumb(); ?>
            <h1 class="page-header__title">
                <?php printf(esc_html__('Búsqueda: &ldquo;%s&rdquo;', 'refugios'), esc_html($query)); ?>
            </h1>
            <p class="page-header__subtitle">
                <?php
                printf(
                    esc_html(_n('%d resultado', '%d resultados', $total, 'refugios')),
                    (int) $total
                );
                ?>
            </p>
        </div>
    </header>

    <section class="section-pad">
        <div class="container">

            <?php if ($total === 0): ?>

                <div class="box-brutal" style="text-align:center; padding: 4rem 2rem;">
                    <i class="fa-solid fa-magnifying-glass"
                       style="font-size:3rem; color:var(--color-amber); display:block; margin-bottom:1rem;"
                       aria-hidden="true"></i>
                    <h2 class="h4"><?php esc_html_e('No encontramos nada con esa búsqueda', 'refugios'); ?></h2>
                    <p style="font-family: var(--font-body); margin-top: 0.75rem; color: rgba(78,52,46,0.7);">
                        <?php esc_html_e('Prueba con el título, el autor o el tema del libro.', 'refugios'); ?>
                    </p>
                    <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/tienda-refugios')); ?>"
                       class="btn btn-primary" style="margin-top: 1.5rem;">
                        <?php esc_html_e('Ver todo el catálogo', 'refugios'); ?>
                    </a>
                </div>

            <?php endif; ?>

            <?php if (!empty($productos)): ?>
                <div class="section-header">
                    <div class="section-header-left">
                        <p class="section-label"><?php esc_html_e('En el catálogo', 'refugios'); ?></p>
                        <h2 class="h2">
                            <?php
                            printf(
                                esc_html(_n('%d libro', '%d libros', count($productos), 'refugios')),
                                count($productos)
                            );
                            ?>
                        </h2>
                    </div>
                </div>

                <ul class="products columns-4 woocommerce refugios-search-products">
                    <?php
                    global $post;
                    foreach ($productos as $producto) {
                        $post = get_post($producto->get_id());
                        setup_postdata($post);
                        wc_get_template_part('content', 'product');
                    }
                    wp_reset_postdata();
                    ?>
                </ul>
            <?php endif; ?>

            <?php if ($posts_query->have_posts()): ?>
                <div class="section-header" style="margin-top: 3rem;">
                    <div class="section-header-left">
                        <p class="section-label"><?php esc_html_e('En el blog', 'refugios'); ?></p>
                        <h2 class="h2">
                            <?php
                            printf(
                                esc_html(_n('%d artículo', '%d artículos', $posts_query->found_posts, 'refugios')),
                                (int) $posts_query->found_posts
                            );
                            ?>
                        </h2>
                    </div>
                </div>

                <?php while ($posts_query->have_posts()):
                    $posts_query->the_post(); ?>

                    <article class="blog-article-row" id="post-<?php the_ID(); ?>">
                        <?php if (has_post_thumbnail()): ?>
                            <a href="<?php the_permalink(); ?>"
                               class="blog-article-row__image"
                               tabindex="-1"
                               aria-hidden="true">
                                <?php the_post_thumbnail('refugios-blog', ['loading' => 'lazy']); ?>
                            </a>
                        <?php endif; ?>

                        <div class="blog-article-row__body">
                            <div>
                                <?php $cat = refugios_first_category(); ?>
                                <?php if ($cat): ?>
                                    <span class="blog-article-row__cat"><?php echo esc_html($cat); ?></span>
                                <?php endif; ?>

                                <h3 class="blog-article-row__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <p class="blog-article-row__excerpt">
                                    <?php echo esc_html(wp_trim_words(get_the_excerpt(), 25, '…')); ?>
                                </p>
                            </div>

                            <div class="blog-article-row__footer">
                                <span class="blog-card__meta">
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                    <?php echo esc_html(get_the_date('d M Y')); ?>
                                </span>
                                <a href="<?php the_permalink(); ?>" class="blog-card__link">
                                    <?php esc_html_e('Leer artículo', 'refugios'); ?>
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </article>

                <?php endwhile;
                wp_reset_postdata(); ?>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>
