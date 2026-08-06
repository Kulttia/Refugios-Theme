<?php
/**
 * Refugios — 404.php
 * Page not found template.
 */

get_header(); ?>

<main id="main-content" role="main">

    <section class="section-pad" style="min-height: 70vh; display: flex; align-items: center;">
        <div class="container" style="text-align: center;">

            <p style="font-family: var(--font-serif); font-size: clamp(6rem, 20vw, 14rem); font-weight: 700; color: rgba(78,52,46,0.08); line-height:1; user-select:none;" aria-hidden="true">
                404
            </p>

            <div style="margin-top: -3rem; position: relative; z-index: 2;">
                <span class="section-label" style="justify-content: center;">
                    <?php esc_html_e('Página extraviada', 'refugios'); ?>
                </span>
                <h1 class="h2" style="margin-bottom: 1rem;">
                    <?php esc_html_e('Esta página se perdió entre los estantes.', 'refugios'); ?>
                </h1>
                <p style="font-family: var(--font-body); font-size: 1.0625rem; color: rgba(78,52,46,0.7); max-width: 42ch; margin-inline: auto; margin-bottom: 2.5rem;">
                    <?php esc_html_e('El contenido que buscas no existe o fue movido. Quizás un buen libro te ayude a encontrar lo que necesitas.', 'refugios'); ?>
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
                    <a href="<?php echo esc_url(home_url()); ?>"
                       class="btn btn-primary"
                       id="btn-404-home">
                        <i class="fa-solid fa-house" aria-hidden="true"></i>
                        <?php esc_html_e('Volver al Inicio', 'refugios'); ?>
                    </a>
                    <?php
$shop_url = function_exists('wc_get_page_permalink')
    ? wc_get_page_permalink('shop')
    : home_url('/tienda-refugios');
?>
                    <a href="<?php echo esc_url($shop_url); ?>"
                       class="btn btn-secondary"
                       id="btn-404-shop">
                        <i class="fa-solid fa-book-open" aria-hidden="true"></i>
                        <?php esc_html_e('Explorar Libros', 'refugios'); ?>
                    </a>
                </div>

                <!-- Search -->
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
                      style="margin-top: 2.5rem; max-width: 480px; margin-inline: auto;">
                    <label for="search-404" class="visually-hidden">
                        <?php esc_html_e('Buscar', 'refugios'); ?>
                    </label>
                    <div style="display:flex; gap:0; border: 3px solid var(--color-brown); box-shadow: var(--shadow-brutal);">
                        <input type="search"
                               id="search-404"
                               name="s"
                               placeholder="<?php esc_attr_e('Buscar libros, artículos…', 'refugios'); ?>"
                               style="flex:1; border:none; padding:0.875rem 1.125rem; background:var(--color-cream); font-family:var(--font-body); font-size:1rem; outline:none;">
                        <button type="submit"
                                style="background:var(--color-brown); color:var(--color-cream); border:none; padding:0.875rem 1.25rem; cursor:pointer; font-family:var(--font-sans); font-weight:700; text-transform:uppercase; font-size:0.8125rem; letter-spacing:0.08em; white-space:nowrap; display:flex; align-items:center; gap:0.5rem;">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            <?php esc_html_e('Buscar', 'refugios'); ?>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </section>

</main><!-- #main-content -->

<?php get_footer();
