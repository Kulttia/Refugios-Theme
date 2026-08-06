<?php
/**
 * Refugios — single.php
 * Single blog post template.
 */

get_header(); ?>

<main id="main-content" role="main">

    <?php while (have_posts()):
    the_post(); ?>

    <!-- Page Header with featured image -->
    <header class="page-header" style="position:relative; overflow:hidden; min-height: 420px; display:flex; align-items:flex-end; padding-bottom: 3rem;">
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('refugios-hero', [
            'style' => 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.25;',
            'aria-hidden' => 'true',
        ]); ?>
        <?php
    endif; ?>
        <div class="container" style="position:relative; z-index:2;">
            <?php refugios_breadcrumb(); ?>

            <?php $cat = refugios_first_category(); ?>
            <?php if ($cat): ?>
                <span style="font-family:var(--font-sans); font-size:0.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:0.15em; color:var(--color-amber); display:block; margin-bottom:0.75rem;">
                    <?php echo esc_html($cat); ?>
                </span>
            <?php
    endif; ?>

            <h1 class="page-header__title"><?php the_title(); ?></h1>

            <div style="margin-top:1rem; display:flex; align-items:center; gap:2rem; flex-wrap:wrap;">
                <span style="font-family:var(--font-sans); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:rgba(245,233,226,0.65);">
                    <i class="fa-regular fa-user" aria-hidden="true"></i>
                    <?php the_author(); ?>
                </span>
                <span style="font-family:var(--font-sans); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:rgba(245,233,226,0.65);">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <?php echo esc_html(get_the_date('d M Y')); ?>
                </span>
                <span style="font-family:var(--font-sans); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:rgba(245,233,226,0.65);">
                    <i class="fa-regular fa-clock" aria-hidden="true"></i>
                    <?php
    $word_count = str_word_count(wp_strip_all_tags(get_the_content()));
    $read_min = max(1, ceil($word_count / 200));
    /* translators: %d: minutes */
    printf(esc_html__('%d min de lectura', 'refugios'), $read_min);
?>
                </span>
            </div>
        </div>
    </header>

    <!-- Single Post Content -->
    <section class="section-pad" id="single-post">
        <div class="container">
            <div class="single-layout">

                <!-- Main Content -->
                <article>
                    <div class="post-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Tags -->
                    <?php $tags = get_the_tags(); ?>
                    <?php if ($tags): ?>
                        <div style="margin-top: 2.5rem; padding-top: 1.5rem; border-top: var(--border-sm); display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center;">
                            <span style="font-family:var(--font-sans); font-size:0.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em;">
                                <?php esc_html_e('Etiquetas:', 'refugios'); ?>
                            </span>
                            <?php foreach ($tags as $tag): ?>
                                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>"
                                   style="font-family:var(--font-sans); font-size:0.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; background:var(--color-cream); color:var(--color-brown); border:2px solid var(--color-brown); padding:0.25rem 0.625rem; transition:background 0.15s, color 0.15s;"
                                   onmouseover="this.style.background='var(--color-amber)'"
                                   onmouseout="this.style.background='var(--color-cream)'">
                                    <?php echo esc_html($tag->name); ?>
                                </a>
                            <?php
        endforeach; ?>
                        </div>
                    <?php
    endif; ?>

                    <!-- Post Navigation -->
                    <nav style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-top:3rem;"
                         aria-label="<?php esc_attr_e('Navegación entre artículos', 'refugios'); ?>">
                        <?php
    $prev_post = get_previous_post();
    $next_post = get_next_post();
?>
                        <?php if ($prev_post): ?>
                            <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>"
                               class="box-brutal"
                               style="display:block; text-decoration:none; padding:1.25rem; transition: box-shadow 0.15s, transform 0.15s;"
                               onmouseover="this.style.boxShadow='var(--shadow-brutal)'; this.style.transform='translate(-2px,-2px)'"
                               onmouseout="this.style.boxShadow='none'; this.style.transform='none'">
                                <span style="font-family:var(--font-sans); font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.15em; color:var(--color-amber); display:block; margin-bottom:0.5rem;">
                                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                    <?php esc_html_e('Anterior', 'refugios'); ?>
                                </span>
                                <span style="font-family:var(--font-serif); font-size:1rem; font-weight:600; color:var(--color-brown); line-height:1.3;">
                                    <?php echo esc_html(get_the_title($prev_post->ID)); ?>
                                </span>
                            </a>
                        <?php
    else: ?>
                            <div></div>
                        <?php
    endif; ?>

                        <?php if ($next_post): ?>
                            <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>"
                               class="box-brutal"
                               style="display:block; text-align:right; text-decoration:none; padding:1.25rem; transition: box-shadow 0.15s, transform 0.15s;"
                               onmouseover="this.style.boxShadow='var(--shadow-brutal)'; this.style.transform='translate(-2px,-2px)'"
                               onmouseout="this.style.boxShadow='none'; this.style.transform='none'">
                                <span style="font-family:var(--font-sans); font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.15em; color:var(--color-amber); display:block; margin-bottom:0.5rem;">
                                    <?php esc_html_e('Siguiente', 'refugios'); ?>
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </span>
                                <span style="font-family:var(--font-serif); font-size:1rem; font-weight:600; color:var(--color-brown); line-height:1.3;">
                                    <?php echo esc_html(get_the_title($next_post->ID)); ?>
                                </span>
                            </a>
                        <?php
    endif; ?>
                    </nav>

                    <!-- Comments -->
                    <?php if (comments_open() || get_comments_number()): ?>
                        <div style="margin-top: 3rem; padding-top: 2rem; border-top: var(--border-md);">
                            <?php comments_template(); ?>
                        </div>
                    <?php
    endif; ?>

                </article><!-- article -->

                <!-- Sidebar -->
                <aside aria-label="<?php esc_attr_e('Sidebar del artículo', 'refugios'); ?>">

                    <!-- About author REMOVED per user request -->
                    <div class="blog-sidebar-widget" style="margin-bottom:1.5rem; display:none;">
                        <h3 class="blog-sidebar-widget__header"><?php esc_html_e('El Autor', 'refugios'); ?></h3>
                        <div class="blog-sidebar-widget__body">
                            <div style="display:flex; gap:1rem; align-items:flex-start;">
                                <div style="border:2px solid var(--color-brown); flex-shrink:0;">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 60, '', '', ['style' => 'display:block; border-radius:0;']); ?>
                                </div>
                                <div>
                                    <div style="font-family:var(--font-sans); font-size:0.875rem; font-weight:700; color:var(--color-brown); margin-bottom:0.375rem;">
                                        <?php the_author(); ?>
                                    </div>
                                    <div style="font-family:var(--font-body); font-size:0.8125rem; color:rgba(78,52,46,0.75);">
                                        <?php echo esc_html(get_the_author_meta('description') ?: esc_html__('Escritor y colaborador de Refugios.', 'refugios')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related posts -->
                    <?php
    $cats = get_the_category();
    $cat_ids = array_map(fn($c) => $c->term_id, $cats);
    $related = new WP_Query([
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post__not_in' => [get_the_ID()],
        'category__in' => $cat_ids,
    ]);
    if ($related->have_posts()): ?>
                        <div class="blog-sidebar-widget">
                            <h3 class="blog-sidebar-widget__header"><?php esc_html_e('También te puede interesar', 'refugios'); ?></h3>
                            <div class="blog-sidebar-widget__body" style="padding:0.75rem; display:flex; flex-direction:column; gap:0.75rem;">
                                <?php while ($related->have_posts()):
            $related->the_post(); ?>
                                    <a href="<?php the_permalink(); ?>"
                                       style="display:flex; gap:0.75rem; text-decoration:none; border-bottom:1px solid rgba(78,52,46,0.1); padding-bottom:0.75rem;">
                                        <?php if (has_post_thumbnail()): ?>
                                            <?php the_post_thumbnail('thumbnail', [
                    'style' => 'width:60px; height:60px; object-fit:cover; border:1px solid var(--color-brown); flex-shrink:0;',
                ]); ?>
                                        <?php
            endif; ?>
                                        <span style="font-family:var(--font-sans); font-size:0.8125rem; font-weight:600; color:var(--color-brown); line-height:1.3;">
                                            <?php the_title(); ?>
                                        </span>
                                    </a>
                                <?php
        endwhile;
        wp_reset_postdata(); ?>
                            </div>
                        </div>
                    <?php
    endif; ?>

                    <!-- Share -->
                    <div class="blog-sidebar-widget">
                        <h3 class="blog-sidebar-widget__header"><?php esc_html_e('Compartir', 'refugios'); ?></h3>
                        <div class="blog-sidebar-widget__body" style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                            <?php
    $share_url = urlencode(get_permalink());
    $share_title = urlencode(get_the_title());
    $shares = [
        ['fa-brands fa-facebook-f', 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url],
        ['fa-brands fa-x-twitter', 'X/Twitter', 'https://twitter.com/intent/tweet?url=' . $share_url . '&text=' . $share_title],
        ['fa-brands fa-whatsapp', 'WhatsApp', 'https://wa.me/?text=' . $share_title . '+' . $share_url],
    ];
    foreach ($shares as $s): ?>
                                <a href="<?php echo esc_url($s[2]); ?>"
                                   target="_blank" rel="noopener"
                                   aria-label="<?php echo esc_attr($s[1]); ?>"
                                   class="btn btn-secondary"
                                   style="padding:0.5rem 0.875rem; gap:0.4rem;">
                                    <i class="<?php echo esc_attr($s[0]); ?>" aria-hidden="true"></i>
                                    <?php echo esc_html($s[1]); ?>
                                </a>
                            <?php
    endforeach; ?>
                        </div>
                    </div>

                </aside>

            </div><!-- .single-layout -->
        </div>
    </section>

    <?php
endwhile; ?>

</main><!-- #main-content -->

<?php get_footer();
