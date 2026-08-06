<?php
/**
 * Refugios — index.php
 * Required WordPress fallback template.
 * Also serves as the Blog archive.
 */

get_header(); ?>

<main id="main-content" role="main">

    <!-- Page Header -->
    <header class="page-header page-header--teal">
        <div class="container">
            <?php refugios_breadcrumb(); ?>
            <h1 class="page-header__title">
                <?php
if (is_home() && !is_front_page()):
    esc_html_e('Blog', 'refugios');
elseif (is_category()):
    single_cat_title();
elseif (is_tag()):
    /* translators: %s: tag name */
    printf(esc_html__('Etiqueta: %s', 'refugios'), single_tag_title('', false));
elseif (is_search()):
    /* translators: %s: search query */
    printf(esc_html__('Búsqueda: &ldquo;%s&rdquo;', 'refugios'), esc_html(get_search_query()));
elseif (is_archive()):
    the_archive_title();
else:
    esc_html_e('Artículos', 'refugios');
endif;
?>
            </h1>

            <?php if (is_category() && category_description()): ?>
                <p class="page-header__subtitle"><?php echo esc_html(category_description()); ?></p>
            <?php
endif; ?>

            <?php if (is_home() && !is_front_page()): ?>
                <p class="page-header__subtitle">
                    <?php esc_html_e('Lecturas, pausas y conversaciones', 'refugios'); ?>
                </p>
            <?php
endif; ?>
        </div>
    </header>

    <section class="section-pad" id="blog-archive">
        <div class="container">
            <div class="blog-archive-layout">

                <!-- Main Articles -->
                <div id="archive-main">

                    <?php if (have_posts()): ?>

                        <?php while (have_posts()):
        the_post(); ?>

                            <article class="blog-article-row" id="post-<?php the_ID(); ?>">

                                <?php if (has_post_thumbnail()): ?>
                                    <a href="<?php the_permalink(); ?>"
                                       class="blog-article-row__image"
                                       tabindex="-1"
                                       aria-hidden="true">
                                        <?php the_post_thumbnail('refugios-blog', ['loading' => 'lazy']); ?>
                                    </a>
                                <?php
        endif; ?>

                                <div class="blog-article-row__body">
                                    <div>
                                        <?php $cat = refugios_first_category(); ?>
                                        <?php if ($cat): ?>
                                            <span class="blog-article-row__cat"><?php echo esc_html($cat); ?></span>
                                        <?php
        endif; ?>

                                        <h2 class="blog-article-row__title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>

                                        <p class="blog-article-row__excerpt">
                                            <?php echo esc_html(wp_trim_words(get_the_excerpt(), 25, '…')); ?>
                                        </p>
                                    </div>

                                    <div class="blog-article-row__footer">
                                        <span class="blog-card__meta">
                                            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                            <?php echo esc_html(get_the_date('d M Y')); ?>
                                            &nbsp;·&nbsp;
                                            <i class="fa-regular fa-user" aria-hidden="true"></i>
                                            <?php the_author(); ?>
                                        </span>
                                        <a href="<?php the_permalink(); ?>"
                                           class="blog-card__link">
                                            <?php esc_html_e('Leer artículo', 'refugios'); ?>
                                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>

                            </article><!-- .blog-article-row -->

                        <?php
    endwhile; ?>

                        <!-- Pagination -->
                        <nav class="rf-pagination" aria-label="<?php esc_attr_e('Paginación', 'refugios'); ?>">
                            <?php
    echo paginate_links([
        'type' => 'list',
        'prev_text' => '<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>',
        'next_text' => '<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>',
    ]);
?>
                        </nav>

                    <?php
else: ?>

                        <div class="box-brutal" style="text-align:center; padding: 4rem 2rem;">
                            <i class="fa-solid fa-book-open-reader"
                               style="font-size:3rem; color:var(--color-amber); display:block; margin-bottom:1rem;"
                               aria-hidden="true"></i>
                            <h2 class="h4"><?php esc_html_e('Sin artículos aún', 'refugios'); ?></h2>
                            <p style="font-family: var(--font-body); margin-top: 0.75rem; color: rgba(78,52,46,0.7);">
                                <?php esc_html_e('Estamos escribiendo. Vuelve pronto.', 'refugios'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url()); ?>"
                               class="btn btn-primary" style="margin-top: 1.5rem;">
                                <?php esc_html_e('Ir al Inicio', 'refugios'); ?>
                            </a>
                        </div>

                    <?php
endif; ?>

                </div><!-- #archive-main -->

                <!-- Sidebar -->
                <aside id="archive-sidebar" aria-label="<?php esc_attr_e('Sidebar', 'refugios'); ?>">

                    <!-- Search -->
                    <div class="blog-sidebar-widget">
                        <h3 class="blog-sidebar-widget__header">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true" style="font-size:0.75rem; margin-right:0.375rem;"></i>
                            <?php esc_html_e('Buscar', 'refugios'); ?>
                        </h3>
                        <div class="blog-sidebar-widget__body blog-search-box" style="padding:1rem;">
                            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                                <label for="sidebar-search" class="visually-hidden">
                                    <?php esc_html_e('Buscar artículos', 'refugios'); ?>
                                </label>
                                <input type="search"
                                       id="sidebar-search"
                                       name="s"
                                       value="<?php echo esc_attr(get_search_query()); ?>"
                                       placeholder="<?php esc_attr_e('Libros, café, cultura…', 'refugios'); ?>">
                                <button type="submit">
                                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                    <?php esc_html_e('Buscar', 'refugios'); ?>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="blog-sidebar-widget">
                        <h3 class="blog-sidebar-widget__header">
                            <?php esc_html_e('Categorías', 'refugios'); ?>
                        </h3>
                        <div class="blog-sidebar-widget__body" style="padding: 0;">
                            <?php
$cats = get_categories(['hide_empty' => true]);
foreach ($cats as $cat): ?>
                                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"
                                   class="sidebar-cat-link">
                                    <?php echo esc_html($cat->name); ?>
                                    <span class="sidebar-cat-count"><?php echo esc_html($cat->count); ?></span>
                                </a>
                            <?php
endforeach; ?>
                        </div>
                    </div>

                    <!-- Recent Libros (WooCommerce) -->
                    <?php if (function_exists('wc_get_products')):
    $recent_books = wc_get_products([
        'status' => 'publish',
        'limit' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
    if ($recent_books): ?>
                            <div class="blog-sidebar-widget">
                                <h3 class="blog-sidebar-widget__header"><?php esc_html_e('Libros Recientes', 'refugios'); ?></h3>
                                <div class="blog-sidebar-widget__body" style="padding: 0.75rem;">
                                    <?php foreach ($recent_books as $book):
            $img_id = $book->get_image_id();
            $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : wc_placeholder_img_src();
?>
                                        <a href="<?php echo esc_url(get_permalink($book->get_id())); ?>"
                                           style="display:flex; gap:0.75rem; padding:0.5rem 0; border-bottom:1px solid rgba(78,52,46,0.1); text-decoration:none;">
                                            <img src="<?php echo esc_url($img_url); ?>"
                                                 alt="<?php echo esc_attr($book->get_name()); ?>"
                                                 style="width:50px; height:70px; object-fit:cover; border:1px solid var(--color-brown);"
                                                 loading="lazy">
                                            <div>
                                                <div style="font-family:var(--font-sans); font-size:0.8125rem; font-weight:600; color:var(--color-brown); line-height:1.3;">
                                                    <?php echo esc_html($book->get_name()); ?>
                                                </div>
                                                <div style="color:var(--color-amber); font-family:var(--font-sans); font-size:0.8125rem; font-weight:700; margin-top:0.25rem;">
                                                    <?php echo wp_kses_post($book->get_price_html()); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php
        endforeach; ?>
                                </div>
                            </div>
                        <?php
    endif;
endif; ?>

                    <!-- Newsletter (sidebar) -->
                    <div class="blog-sidebar-widget" style="background:var(--color-brown); border-color:var(--color-brown);">
                        <h3 class="blog-sidebar-widget__header" style="background:var(--color-brown); color:var(--color-cream); border-bottom:1px dashed rgba(245,233,226,0.3);">
                            <?php esc_html_e('La Pausa Semanal', 'refugios'); ?>
                        </h3>
                        <div class="blog-sidebar-widget__body">
                            <p style="font-family:var(--font-body); font-size:0.875rem; color:var(--color-cream); margin-bottom:1rem;">
                                <?php esc_html_e('Suscríbete y recibe recomendaciones cada semana.', 'refugios'); ?>
                            </p>
                            <form style="display:flex; flex-direction:column; gap:0.5rem;" method="post">
                                <?php wp_nonce_field('refugios_newsletter', 'nonce'); ?>
                                <input type="email" name="email" required
                                       placeholder="<?php esc_attr_e('tu@email.com', 'refugios'); ?>"
                                       style="border:2px solid var(--color-cream); background:transparent; color:var(--color-cream); padding:0.625rem 0.875rem; border-radius:0; font-family:var(--font-body); width:100%;">
                                <button type="submit" class="btn btn-teal"
                                        style="background:var(--color-cream); color:var(--color-brown); border:2px solid var(--color-cream); justify-content:center; width:100%; font-weight:700;">
                                    <?php esc_html_e('Suscribirme', 'refugios'); ?>
                                    <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                </aside><!-- #archive-sidebar -->

            </div><!-- .blog-archive-layout -->
        </div>
    </section>

</main><!-- #main-content -->

<?php get_footer();
