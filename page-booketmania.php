<?php
/**
 * Refugios — page-booketmania.php
 * Plantilla de /booketmania/: los libros de Booket, Maxi Tusquets y
 * Austral con existencias, la barra del combo 3x2 y, en cada libro,
 * cuánto se ahorra si se suma al carrito. El descuento lo aplica el
 * carrito (sección 22 de functions.php); aquí solo se muestra.
 */

get_header();

$bm_cfg = refugios_bm_config();
$bm_ids = refugios_bm_product_ids();

$bm_query = null;
if ($bm_ids) {
    $bm_query = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'post__in'       => $bm_ids,
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ]);
}

// Sello y precio de cada libro: filtros por sello y el "te sale gratis" en vivo.
$bm_map = [];
$bm_counts = [];
if ($bm_query && $bm_query->have_posts()) {
    foreach ($bm_query->posts as $p) {
        $product = wc_get_product($p->ID);
        $sello = refugios_bm_sello($product);
        $bm_map[$p->ID] = ['p' => (float) $product->get_price(), 's' => sanitize_title($sello)];
        $bm_counts[$sello] = ($bm_counts[$sello] ?? 0) + 1;
    }
}
$bm_count = count($bm_map);
$bm_hasta = !empty($bm_cfg['hasta']) ? wp_date('j \d\e F', strtotime($bm_cfg['hasta'])) : '';
?>

<?php // Estilos en línea: style.css se sirve sin versión y el CDN/navegador lo guarda viejo. ?>
<style data-no-optimize="1" data-optimized="0" id="refugios-bm-page-css">
.bm-intro{padding-bottom:clamp(1.5rem,4vw,2.5rem)}.bm-intro__grid{display:grid;grid-template-columns:1fr auto;gap:clamp(1.5rem,4vw,3rem);align-items:end}.bm-intro__title{font-family:var(--font-serif);font-size:clamp(2.25rem,5.5vw,4.25rem);font-weight:700;line-height:1.02;letter-spacing:-0.02em;color:var(--color-brown);max-width:15ch;margin-bottom:1.25rem}.bm-intro__text{font-family:var(--font-body);font-size:clamp(1.05rem,1.5vw,1.2rem);line-height:1.65;color:rgba(78,52,46,0.88);max-width:58ch;margin:0}
.bm-badge{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.35rem;min-width:11rem;padding:1.5rem 1.75rem;background:var(--color-amber);border:var(--border-md);box-shadow:6px 6px 0 var(--color-brown);transform:rotate(-3deg);text-align:center}.bm-badge__num{font-family:var(--font-serif);font-size:clamp(3rem,6vw,4.5rem);font-weight:800;line-height:1;color:var(--color-brown)}.bm-badge__txt{font-family:var(--font-sans);font-size:0.6875rem;font-weight:800;text-transform:uppercase;letter-spacing:0.18em;color:var(--color-brown)}
.bm-steps{list-style:none;display:grid;grid-template-columns:repeat(3,1fr);gap:0;margin:clamp(2rem,4vw,3rem) 0 0;padding:0;border:var(--border-md);background:var(--color-white)}.bm-steps li{display:flex;gap:0.9rem;align-items:flex-start;padding:1.1rem 1.25rem;font-family:var(--font-body);font-size:0.98rem;line-height:1.5;color:var(--color-brown)}.bm-steps li+li{border-left:var(--border-md)}.bm-steps b{flex:0 0 auto;display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;background:var(--color-brown);color:var(--color-cream);font-family:var(--font-sans);font-size:0.85rem;font-weight:800}
.bm-facts{list-style:none;display:flex;flex-wrap:wrap;gap:0.75rem;margin:1.25rem 0 0;padding:0}.bm-facts li{display:inline-flex;align-items:center;gap:0.6rem;padding:0.6rem 1rem;border:var(--border-md);background:var(--color-white);font-family:var(--font-sans);font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--color-brown)}.bm-facts i{color:var(--color-amber)}
.bm-list{padding-bottom:clamp(3rem,8vw,6rem)}.bm-filters{display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:clamp(1.5rem,3vw,2.25rem);padding-top:1.5rem;border-top:var(--border-md)}.bm-filter{font-family:var(--font-sans);font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;padding:0.55rem 0.9rem;border:2px solid var(--color-brown);background:var(--color-cream);color:var(--color-brown);cursor:pointer;transition:background 0.15s ease,box-shadow 0.15s ease}.bm-filter small{font-size:0.65rem;opacity:0.7;margin-left:0.3rem}.bm-filter:hover{box-shadow:3px 3px 0 var(--color-brown)}.bm-filter.is-active{background:var(--color-brown);color:var(--color-cream)}.bm-filter:focus-visible{outline:3px solid var(--color-amber);outline-offset:2px}.bm-products>li[hidden]{display:none !important}
.bm-tag{position:absolute;left:0;right:0;bottom:0;z-index:4;padding:0.5rem 0.6rem;background:var(--color-brown);color:var(--color-cream);font-family:var(--font-sans);font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;text-align:center;line-height:1.3}.bm-tag.is-free{background:var(--color-amber);color:var(--color-brown);border-top:2px solid var(--color-brown)}
.bm-empty{border:var(--border-md);background:var(--color-white);box-shadow:var(--shadow-amber);padding:clamp(2rem,5vw,3.5rem);text-align:center;font-family:var(--font-body);color:var(--color-brown)}.bm-empty__title{font-family:var(--font-serif);font-size:clamp(1.5rem,3vw,2.25rem);font-weight:700;margin-bottom:0.75rem}
.bm-combo{position:fixed;left:0;right:0;bottom:0;z-index:90;background:var(--color-cream);border-top:3px solid var(--color-brown);box-shadow:0 -6px 0 rgba(217,160,102,0.35)}.bm-combo__inner{display:flex;align-items:center;gap:1rem;padding-top:0.8rem;padding-bottom:0.8rem}.bm-combo__pips{display:flex;gap:0.35rem;flex:0 0 auto}.bm-pip{display:inline-flex;align-items:center;justify-content:center;min-width:2.1rem;height:2.1rem;padding:0 0.4rem;border:2px solid var(--color-brown);background:var(--color-white);color:rgba(78,52,46,0.45);font-family:var(--font-sans);font-size:0.8rem;font-weight:800}.bm-pip.is-free{font-size:0.6rem;letter-spacing:0.08em}.bm-pip.is-on{background:var(--color-brown);color:var(--color-cream)}.bm-pip.is-free.is-on{background:var(--color-amber);color:var(--color-brown)}.bm-combo__text{flex:1 1 auto;margin:0;font-family:var(--font-body);font-size:0.98rem;line-height:1.4;color:var(--color-brown)}.bm-combo__btn{flex:0 0 auto;display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.1rem;background:var(--color-brown);color:var(--color-cream) !important;border:2px solid var(--color-brown);box-shadow:3px 3px 0 var(--color-amber);font-family:var(--font-sans);font-size:0.75rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;text-decoration:none}.bm-combo__btn:hover{background:var(--color-amber);color:var(--color-brown) !important;box-shadow:3px 3px 0 var(--color-brown)}
.bm-page-body{padding-bottom:5rem}
@media (max-width:767px){.bm-intro__grid{grid-template-columns:1fr;align-items:start}.bm-intro__title{max-width:none}.bm-badge{flex-direction:row;justify-content:flex-start;gap:0.9rem;min-width:0;padding:1rem 1.25rem;transform:none;box-shadow:4px 4px 0 var(--color-brown)}.bm-badge__num{font-size:2.5rem}.bm-steps{grid-template-columns:1fr}.bm-steps li+li{border-left:0;border-top:var(--border-md)}.bm-facts li{width:100%}.bm-products{grid-template-columns:repeat(2,1fr) !important;gap:0.75rem !important}.bm-filters{flex-wrap:nowrap;overflow-x:auto;-webkit-overflow-scrolling:touch;padding-bottom:0.25rem}.bm-filter{flex:0 0 auto}.bm-combo__inner{flex-wrap:wrap;gap:0.5rem 0.75rem}.bm-combo__text{flex-basis:calc(100% - 9rem);font-size:0.88rem}.bm-pip{min-width:1.8rem;height:1.8rem}.bm-combo__btn{width:100%;justify-content:center}.bm-tag{font-size:0.62rem}.bm-page-body{padding-bottom:8rem}}
@media (max-width:420px){.bm-products{grid-template-columns:1fr !important}}
</style>

<main id="main-content" role="main" class="bm-page">

    <header class="page-header">
        <div class="container">
            <nav class="page-header__breadcrumb" aria-label="<?php esc_attr_e('Ruta de navegación', 'refugios'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Inicio', 'refugios'); ?></a>
                <span class="sep" aria-hidden="true">›</span>
                <a href="<?php echo esc_url(home_url('/tienda-refugios/')); ?>"><?php esc_html_e('Tienda', 'refugios'); ?></a>
                <span class="sep" aria-hidden="true">›</span>
                <span><?php echo esc_html($bm_cfg['nombre']); ?></span>
            </nav>
            <h1 class="page-header__title"><?php echo esc_html($bm_cfg['nombre']); ?></h1>
        </div>
    </header>

    <section class="section-pad bm-intro" id="bm-intro">
        <div class="container bm-intro__grid">
            <div>
                <p class="section-label"><?php esc_html_e('Booket · Maxi Tusquets · Austral', 'refugios'); ?></p>
                <h2 class="bm-intro__title"><?php esc_html_e('Lleva tres, paga dos.', 'refugios'); ?></h2>
                <p class="bm-intro__text">
                    <?php echo esc_html(sprintf(
                        __('Este mes los libros de bolsillo de Planeta vienen en combo: por cada tres que elijas de Booket, Maxi Tusquets o Austral, el de menor valor te sale gratis. Mézclalos como quieras; el descuento se aplica solo en el carrito%s.', 'refugios'),
                        $bm_hasta ? sprintf(__(' hasta el %s', 'refugios'), $bm_hasta) : ''
                    )); ?>
                </p>
            </div>
            <div class="bm-badge" aria-label="<?php esc_attr_e('Tres por dos', 'refugios'); ?>">
                <span class="bm-badge__num">3x2</span>
                <span class="bm-badge__txt"><?php echo $bm_hasta ? esc_html(sprintf(__('hasta el %s', 'refugios'), $bm_hasta)) : esc_html__('por tiempo limitado', 'refugios'); ?></span>
            </div>
        </div>

        <div class="container">
            <ol class="bm-steps">
                <li><b>1</b><span><?php esc_html_e('Elige tres libros de cualquiera de los tres sellos.', 'refugios'); ?></span></li>
                <li><b>2</b><span><?php esc_html_e('El de menor valor de cada tres te sale gratis.', 'refugios'); ?></span></li>
                <li><b>3</b><span><?php esc_html_e('¿Seis libros? Dos gratis. El carrito hace la cuenta y te muestra cuánto ahorras.', 'refugios'); ?></span></li>
            </ol>
            <ul class="bm-facts" role="list">
                <li><i class="fa-solid fa-book" aria-hidden="true"></i>
                    <?php echo esc_html(sprintf(_n('%d título disponible', '%d títulos disponibles', $bm_count, 'refugios'), $bm_count)); ?></li>
                <li><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i>
                    <?php echo $bm_hasta
                        ? esc_html(sprintf(__('Hasta el %s o hasta agotar existencias', 'refugios'), $bm_hasta))
                        : esc_html__('Hasta agotar existencias', 'refugios'); ?></li>
                <li><i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                    <?php esc_html_e('Envíos a toda Colombia', 'refugios'); ?></li>
            </ul>
        </div>
    </section>

    <section class="bm-list" id="bm-libros" aria-label="<?php esc_attr_e('Libros de Booketmanía', 'refugios'); ?>">
        <div class="container">
            <?php if ($bm_query && $bm_query->have_posts()): ?>

                <?php if (count($bm_counts) > 1): ?>
                    <div class="bm-filters" role="group" aria-label="<?php esc_attr_e('Filtrar por sello', 'refugios'); ?>">
                        <button type="button" class="bm-filter is-active" data-sello="" aria-pressed="true"><?php esc_html_e('Todos', 'refugios'); ?><small><?php echo (int) $bm_count; ?></small></button>
                        <?php foreach ($bm_cfg['sellos'] as $s):
                            if (empty($bm_counts[$s['nombre']])) continue; ?>
                            <button type="button" class="bm-filter" data-sello="<?php echo esc_attr(sanitize_title($s['nombre'])); ?>" aria-pressed="false"><?php echo esc_html($s['nombre']); ?><small><?php echo (int) $bm_counts[$s['nombre']]; ?></small></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="woocommerce">
                    <ul class="products columns-4 bm-products">
                        <?php
                        while ($bm_query->have_posts()) {
                            $bm_query->the_post();
                            wc_get_template_part('content', 'product');
                        }
                        wp_reset_postdata();
                        ?>
                    </ul>
                </div>

            <?php else: ?>
                <div class="bm-empty">
                    <p class="bm-empty__title"><?php esc_html_e('Ya no quedan libros de estos sellos.', 'refugios'); ?></p>
                    <p><?php esc_html_e('Se fueron con sus lectores. Hay mucho más por descubrir en la librería.', 'refugios'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section-pad mf-close" id="bm-cierre">
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

<?php echo refugios_bm_combo_bar(); // Se reemplaza con el estado real del carrito vía fragmentos de Woo. ?>

<script>
(function () {
    var MAP = <?php echo wp_json_encode((object) $bm_map); ?>;
    var cards = document.querySelectorAll('.bm-products > li');

    function idOf(card) {
        var m = card.className.match(/(?:^|\s)post-(\d+)(?:\s|$)/);
        return m ? m[1] : null;
    }
    function money(n) {
        return '$ ' + Math.round(n).toLocaleString('es-CO');
    }
    // Lo mismo que el carrito: de mayor a menor, cada tercero sale gratis.
    function savings(prices, lleva) {
        var s = prices.slice().sort(function (a, b) { return b - a; });
        var total = 0;
        for (var i = lleva - 1; i < s.length; i += lleva) total += s[i];
        return total;
    }

    // Filtro por sello
    var btns = document.querySelectorAll('.bm-filter');
    btns.forEach(function (b) {
        b.addEventListener('click', function () {
            var sello = b.getAttribute('data-sello');
            btns.forEach(function (o) {
                var on = o === b;
                o.classList.toggle('is-active', on);
                o.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            cards.forEach(function (c) {
                var info = MAP[idOf(c)];
                c.hidden = !!sello && (!info || info.s !== sello);
            });
        });
    });

    // En cada libro: cuánto ahorras si lo sumas a lo que ya llevas.
    function paint() {
        var bar = document.querySelector('.bm-combo');
        var state = { lleva: 3, precios: [] };
        try { state = JSON.parse(bar.getAttribute('data-bm')) || state; } catch (e) {}
        var base = savings(state.precios, state.lleva);
        cards.forEach(function (c) {
            var info = MAP[idOf(c)];
            var media = c.querySelector('.refugios-product-card__media');
            var tag = c.querySelector('.bm-tag');
            if (!info || !media) return;
            var delta = savings(state.precios.concat([info.p]), state.lleva) - base;
            if (delta <= 0) {
                if (tag) tag.remove();
                return;
            }
            if (!tag) {
                tag = document.createElement('span');
                tag.className = 'bm-tag';
                media.appendChild(tag);
            }
            var free = Math.abs(delta - info.p) < 1;
            tag.classList.toggle('is-free', free);
            tag.textContent = free ? '¡Este te sale gratis!' : 'Con este ahorras ' + money(delta);
        });
    }
    paint();
    if (window.jQuery) {
        jQuery(document.body).on('wc_fragments_loaded wc_fragments_refreshed added_to_cart removed_from_cart', function () {
            setTimeout(paint, 0);
        });
    }
})();
</script>

<?php get_footer();
