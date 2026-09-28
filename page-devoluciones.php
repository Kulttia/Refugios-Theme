<?php
/**
 * Refugios — page-devoluciones.php
 * Plantilla de /devoluciones/: libros que vuelven a la editorial,
 * con descuento mientras sigan en la estantería. La lista y el
 * porcentaje viven en devoluciones-config.json (ver sección 20 de
 * functions.php). Se sirve por la ruta propia del tema o por una
 * página con slug "devoluciones".
 */

get_header();

$dv_cfg = refugios_devol_config();
$dv_pct = (int) $dv_cfg['descuento'];
$dv_ids = array_keys($dv_cfg['ids']);

$dv_query = null;
if ($dv_ids && function_exists('wc_get_template_part')) {
    $dv_query = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'post__in'       => $dv_ids,
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
        'meta_query'     => [[
            'key'   => '_stock_status',
            'value' => 'instock',
        ]],
    ]);
}

// Categorías presentes, para filtrar la grilla sin recargar.
$dv_cats = [];
if ($dv_query && $dv_query->have_posts()) {
    foreach ($dv_query->posts as $p) {
        $terms = get_the_terms($p->ID, 'product_cat');
        if ($terms && !is_wp_error($terms)) {
            $dv_cats[$terms[0]->slug] = $terms[0]->name;
        }
    }
    asort($dv_cats);
}
$dv_count = $dv_query ? $dv_query->post_count : 0;
?>

<main id="main-content" role="main" class="dv-page">

    <header class="page-header">
        <div class="container">
            <nav class="page-header__breadcrumb" aria-label="<?php esc_attr_e('Ruta de navegación', 'refugios'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Inicio', 'refugios'); ?></a>
                <span class="sep" aria-hidden="true">›</span>
                <a href="<?php echo esc_url(home_url('/tienda-refugios/')); ?>"><?php esc_html_e('Tienda', 'refugios'); ?></a>
                <span class="sep" aria-hidden="true">›</span>
                <span><?php esc_html_e('Devoluciones', 'refugios'); ?></span>
            </nav>
            <h1 class="page-header__title"><?php esc_html_e('Devoluciones', 'refugios'); ?></h1>
        </div>
    </header>

    <section class="section-pad dv-intro" id="dv-intro">
        <div class="container dv-intro__grid">
            <div>
                <p class="section-label"><?php esc_html_e('Últimos ejemplares', 'refugios'); ?></p>
                <h2 class="dv-intro__title">
                    <?php esc_html_e('Estos libros vuelven a la editorial.', 'refugios'); ?>
                </h2>
                <p class="dv-intro__text">
                    <?php esc_html_e('Antes de despedirlos queremos darles otra oportunidad de encontrar lector. Mientras sigan en la estantería, los tienes con descuento aquí y en la librería.', 'refugios'); ?>
                </p>
            </div>
            <?php if ($dv_pct > 0): ?>
                <div class="dv-badge" aria-label="<?php echo esc_attr(sprintf(__('%d por ciento de descuento', 'refugios'), $dv_pct)); ?>">
                    <span class="dv-badge__num">-<?php echo esc_html($dv_pct); ?>%</span>
                    <span class="dv-badge__txt"><?php esc_html_e('en web y en tienda', 'refugios'); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="container">
            <ul class="dv-facts" role="list">
                <li><i class="fa-solid fa-book" aria-hidden="true"></i>
                    <?php echo esc_html(sprintf(_n('%d título disponible', '%d títulos disponibles', $dv_count, 'refugios'), $dv_count)); ?></li>
                <li><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i>
                    <?php
                    if (!empty($dv_cfg['hasta'])) {
                        echo esc_html(sprintf(__('Hasta el %s o hasta agotar existencias', 'refugios'), wp_date('j \d\e F', strtotime($dv_cfg['hasta']))));
                    } else {
                        esc_html_e('Casi todos son ejemplar único', 'refugios');
                    }
                    ?></li>
                <li><i class="fa-solid fa-store" aria-hidden="true"></i>
                    <?php esc_html_e('Mismo precio en la librería de Itagüí', 'refugios'); ?></li>
            </ul>
        </div>
    </section>

    <section class="dv-list" id="dv-libros" aria-label="<?php esc_attr_e('Libros en devolución', 'refugios'); ?>">
        <div class="container">
            <?php if ($dv_query && $dv_query->have_posts()): ?>

                <?php if (count($dv_cats) > 1): ?>
                    <div class="dv-filters" role="group" aria-label="<?php esc_attr_e('Filtrar por categoría', 'refugios'); ?>">
                        <button type="button" class="dv-filter is-active" data-cat="" aria-pressed="true"><?php esc_html_e('Todos', 'refugios'); ?></button>
                        <?php foreach ($dv_cats as $slug => $name): ?>
                            <button type="button" class="dv-filter" data-cat="<?php echo esc_attr($slug); ?>" aria-pressed="false"><?php echo esc_html($name); ?></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="woocommerce">
                    <ul class="products columns-4 dv-products">
                        <?php
                        while ($dv_query->have_posts()) {
                            $dv_query->the_post();
                            wc_get_template_part('content', 'product');
                        }
                        wp_reset_postdata();
                        ?>
                    </ul>
                </div>

            <?php else: ?>
                <div class="dv-empty">
                    <p class="dv-empty__title"><?php esc_html_e('Ya no quedan libros en esta lista.', 'refugios'); ?></p>
                    <p><?php esc_html_e('Encontraron lector o volvieron a la editorial. Hay mucho más por descubrir en la librería.', 'refugios'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section-pad mf-close" id="dv-cierre">
        <div class="container">
            <div class="mf-close__box">
                <p class="mf-close__label"><?php esc_html_e('¿Buscas otro libro?', 'refugios'); ?></p>
                <h2 class="mf-close__title"><?php esc_html_e('Hay más refugios en la estantería.', 'refugios'); ?></h2>
                <div class="mf-close__actions">
                    <a href="<?php echo esc_url(home_url('/tienda-refugios/')); ?>" class="btn btn-primary">
                        <?php esc_html_e('Explorar la librería', 'refugios'); ?>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <a href="<?php echo esc_url(home_url('/contacto/')); ?>" class="btn btn-secondary">
                        <?php esc_html_e('Visítanos', 'refugios'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main><!-- #main-content -->

<?php if (count($dv_cats) > 1): ?>
<script>
(function () {
    var btns = document.querySelectorAll('.dv-filter');
    var cards = document.querySelectorAll('.dv-products > li');
    btns.forEach(function (b) {
        b.addEventListener('click', function () {
            var cat = b.getAttribute('data-cat');
            btns.forEach(function (o) {
                var on = o === b;
                o.classList.toggle('is-active', on);
                o.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            cards.forEach(function (c) {
                c.hidden = !!cat && !c.classList.contains('product_cat-' + cat);
            });
        });
    });
})();
</script>
<?php endif; ?>

<?php get_footer();
