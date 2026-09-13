<?php
/**
 * Refugios — page-manifiestos.php
 * Plantilla dedicada para /manifiestos/. Seis posturas, cada una como
 * una tarjeta brutalista: número, palabra, declaración y acción.
 * El contenido vive en el tema (no en Elementor).
 */

get_header();

$refugios_manifiestos = [
    [
        'word'      => __('Educación', 'refugios'),
        'statement' => __('Creemos en la educación como eje de transformación, la que se sostiene toda la vida.', 'refugios'),
        'action'    => __('Por eso tratamos cada libro como una herramienta de aprendizaje y creamos recursos para que aprender sea un proceso continuo.', 'refugios'),
    ],
    [
        'word'      => __('Libros', 'refugios'),
        'statement' => __('Creemos que un libro bien elegido cambia a una persona.', 'refugios'),
        'action'    => __('Por eso elegimos libros que se piensan la condición humana y hacen preguntas difíciles, no los que están de moda. Preferimos pocos libros con criterio que muchos sin razón.', 'refugios'),
    ],
    [
        'word'      => __('Pausa', 'refugios'),
        'statement' => __('Creemos que la prisa arruina la vida.', 'refugios'),
        'action'    => __('Hoy muchos estímulos compiten por nuestra atención. Por eso defendemos la lectura lenta y sostenemos espacios donde se puede estar sin afán.', 'refugios'),
    ],
    [
        'word'      => __('Cultura', 'refugios'),
        'statement' => __('Trabajamos por una cultura menos centralizada.', 'refugios'),
        'action'    => __('Abrimos donde no había librerías y preferimos la colaboración sobre la competencia.', 'refugios'),
    ],
    [
        'word'      => __('Salud', 'refugios'),
        'statement' => __('Creemos que el bienestar comienza por el cuidado interior.', 'refugios'),
        'action'    => __('Por eso hablamos con naturalidad de salud mental y ofrecemos un lugar donde la calma es posible.', 'refugios'),
    ],
    [
        'word'      => __('Alimentación', 'refugios'),
        'statement' => __('Somos lo que comemos.', 'refugios'),
        'action'    => __('Por eso pensamos cada ingrediente y conocemos la trazabilidad de lo que servimos: de dónde viene y cómo fue hecho.', 'refugios'),
    ],
];
?>

<main id="main-content" role="main" class="mf-page">

    <header class="page-header">
        <div class="container">
            <?php refugios_breadcrumb(); ?>
            <h1 class="page-header__title"><?php the_title(); ?></h1>
        </div>
    </header>

    <!-- Declaración de apertura -->
    <section class="section-pad mf-intro" id="mf-intro">
        <div class="container">
            <p class="section-label"><?php esc_html_e('Manifiestos', 'refugios'); ?></p>
            <h2 class="mf-intro__title">
                <?php esc_html_e('Entendemos la importancia de asumir posiciones en el mundo.', 'refugios'); ?>
            </h2>
            <p class="mf-intro__text"><?php esc_html_e('Estas son las nuestras.', 'refugios'); ?></p>
        </div>
    </section>

    <!-- Seis posturas -->
    <section class="mf-list" id="mf-posturas" aria-label="<?php esc_attr_e('Nuestras posturas', 'refugios'); ?>">
        <div class="container">
            <ol class="mf-grid" role="list">
                <?php foreach ($refugios_manifiestos as $i => $m): ?>
                    <li class="mf-card">
                        <div class="mf-card__head">
                            <span class="mf-card__num" aria-hidden="true"><?php echo esc_html(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <h2 class="mf-card__word"><?php echo esc_html($m['word']); ?></h2>
                        </div>
                        <p class="mf-card__statement"><?php echo esc_html($m['statement']); ?></p>
                        <p class="mf-card__action"><?php echo esc_html($m['action']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- Cierre -->
    <section class="section-pad mf-close" id="mf-cierre">
        <div class="container">
            <div class="mf-close__box">
                <p class="mf-close__label"><?php esc_html_e('Así lo vivimos', 'refugios'); ?></p>
                <h2 class="mf-close__title"><?php esc_html_e('Ven a leer sin afán.', 'refugios'); ?></h2>
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

<?php get_footer();
