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

<?php // Estilos en línea: style.css se sirve sin versión y el CDN/navegador lo guarda viejo. ?>
<style data-no-optimize="1" data-optimized="0" id="refugios-devol-css">
.dv-intro{padding-bottom:clamp(1.5rem,4vw,2.5rem)}.dv-intro__grid{display:grid;grid-template-columns:1fr auto;gap:clamp(1.5rem,4vw,3rem);align-items:end}.dv-intro__title{font-family:var(--font-serif);font-size:clamp(2.25rem,5.5vw,4.25rem);font-weight:700;line-height:1.02;letter-spacing:-0.02em;color:var(--color-brown);max-width:16ch;margin-bottom:1.25rem}.dv-intro__text{font-family:var(--font-body);font-size:clamp(1.05rem,1.5vw,1.2rem);line-height:1.65;color:rgba(78,52,46,0.88);max-width:56ch;margin:0}.dv-badge{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.35rem;min-width:11rem;padding:1.5rem 1.75rem;background:var(--color-amber);border:var(--border-md);box-shadow:6px 6px 0 var(--color-brown);transform:rotate(-3deg);text-align:center}.dv-badge__num{font-family:var(--font-serif);font-size:clamp(3rem,6vw,4.5rem);font-weight:800;line-height:1;color:var(--color-brown)}.dv-badge__txt{font-family:var(--font-sans);font-size:0.6875rem;font-weight:800;text-transform:uppercase;letter-spacing:0.18em;color:var(--color-brown)}.dv-facts{list-style:none;display:flex;flex-wrap:wrap;gap:0.75rem;margin:clamp(2rem,4vw,3rem) 0 0;padding:0}.dv-facts li{display:inline-flex;align-items:center;gap:0.6rem;padding:0.6rem 1rem;border:var(--border-md);background:var(--color-white);font-family:var(--font-sans);font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--color-brown)}.dv-facts i{color:var(--color-amber)}.dv-list{padding-bottom:clamp(3rem,8vw,6rem)}.dv-filters{display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:clamp(1.5rem,3vw,2.25rem);padding-top:1.5rem;border-top:var(--border-md)}.dv-filter{font-family:var(--font-sans);font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;padding:0.55rem 0.9rem;border:2px solid var(--color-brown);background:var(--color-cream);color:var(--color-brown);cursor:pointer;transition:background 0.15s ease,box-shadow 0.15s ease}.dv-filter:hover{box-shadow:3px 3px 0 var(--color-brown)}.dv-filter.is-active{background:var(--color-brown);color:var(--color-cream)}.dv-filter:focus-visible{outline:3px solid var(--color-amber);outline-offset:2px}.dv-products>li[hidden]{display:none !important}.dv-empty{border:var(--border-md);background:var(--color-white);box-shadow:var(--shadow-amber);padding:clamp(2rem,5vw,3.5rem);text-align:center;font-family:var(--font-body);color:var(--color-brown)}.dv-empty__title{font-family:var(--font-serif);font-size:clamp(1.5rem,3vw,2.25rem);font-weight:700;margin-bottom:0.75rem}@media (max-width:767px){.dv-intro__grid{grid-template-columns:1fr;align-items:start}.dv-intro__title{max-width:none}.dv-badge{flex-direction:row;justify-content:flex-start;gap:0.9rem;min-width:0;padding:1rem 1.25rem;transform:none;box-shadow:4px 4px 0 var(--color-brown)}.dv-badge__num{font-size:2.5rem}.dv-facts li{width:100%}.dv-products{grid-template-columns:repeat(2,1fr) !important;gap:0.75rem !important}.dv-filters{flex-wrap:nowrap;overflow-x:auto;-webkit-overflow-scrolling:touch;padding-bottom:0.25rem}.dv-filter{flex:0 0 auto}}@media (max-width:420px){.dv-products{grid-template-columns:1fr !important}}
</style>

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
