<?php
/**
 * Refugios — page-quienes-somos.php
 * Plantilla dedicada para /quienes-somos/. El contenido vive en el tema
 * (no en Elementor) para mantener el design system y desplegar por git.
 * Al final enlaza a /manifiestos/, la lectura que sigue.
 */

get_header(); ?>

<main id="main-content" role="main" class="qs-page">

    <header class="page-header">
        <div class="container">
            <?php refugios_breadcrumb(); ?>
            <h1 class="page-header__title"><?php the_title(); ?></h1>
        </div>
    </header>

    <!-- 01 · Declaración -->
    <section class="section-pad qs-intro" id="qs-intro">
        <div class="container">
            <div class="qs-narrow">
                <p class="section-label"><?php esc_html_e('Quiénes somos', 'refugios'); ?></p>
                <p class="qs-statement">
                    <?php esc_html_e('Somos una comunidad que cree en el poder de los libros, los alimentos y la educación para transformar la cultura.', 'refugios'); ?>
                </p>
                <p class="qs-lead">
                    <?php esc_html_e('Soñamos con un mundo donde se lean más y mejores libros, para comprender de manera más consciente lo propio y lo de otros.', 'refugios'); ?>
                </p>

                <div class="qs-purpose" role="note">
                    <p class="qs-purpose__label"><?php esc_html_e('Nuestro propósito es:', 'refugios'); ?></p>
                    <p class="qs-purpose__text"><?php esc_html_e('Hacer más y mejores lectores.', 'refugios'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 · Qué hacemos -->
    <section class="section-pad qs-body" id="qs-body">
        <div class="container">
            <div class="qs-narrow">
                <p class="section-label"><?php esc_html_e('Lo que hacemos', 'refugios'); ?></p>
                <div class="qs-prose">
                    <p><?php esc_html_e('Trabajamos especialmente por quienes han perdido el interés por la lectura, para que vuelvan a reconectarse con los libros en un espacio acogedor, físico y virtual, donde también pueden disfrutar de café de especialidad y alimentos saludables. No solo vendemos libros: acompañamos a las personas a encontrar el libro que les sirve hoy y que les moviliza el pensamiento y las emociones.', 'refugios'); ?></p>
                    <p><?php esc_html_e('Vemos los libros como herramientas de aprendizaje. Por eso la educación ocupa un lugar central en lo que hacemos, y creemos que leer es la forma más accesible de seguir aprendiendo toda la vida.', 'refugios'); ?></p>
                    <p><?php esc_html_e('Soñamos con abrir librerías donde antes no había librerías. Hoy estamos en Itagüí y esa elección no fue casual: creemos que la cultura concentrada en unas pocas zonas también es una forma de exclusión.', 'refugios'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 · Principios -->
    <section class="section-pad qs-principles" id="qs-principios">
        <div class="container">
            <div class="qs-narrow">
                <p class="section-label"><?php esc_html_e('Nos orientan tres principios', 'refugios'); ?></p>
            </div>
            <ul class="qs-principles__grid" role="list">
                <li class="qs-principle">
                    <span class="qs-principle__num" aria-hidden="true">01</span>
                    <span class="qs-principle__word"><?php esc_html_e('Ser', 'refugios'); ?></span>
                </li>
                <li class="qs-principle">
                    <span class="qs-principle__num" aria-hidden="true">02</span>
                    <span class="qs-principle__word"><?php esc_html_e('Aprender', 'refugios'); ?></span>
                </li>
                <li class="qs-principle">
                    <span class="qs-principle__num" aria-hidden="true">03</span>
                    <span class="qs-principle__word"><?php esc_html_e('Compartir', 'refugios'); ?></span>
                </li>
            </ul>
            <div class="qs-narrow">
                <p class="qs-principles__text"><?php esc_html_e('Sabemos la importancia de pertenecer a comunidades donde la confianza, la igualdad y el respeto nos lleven a aprender y compartir más.', 'refugios'); ?></p>
            </div>
        </div>
    </section>

    <!-- 04 · Preguntas -->
    <section class="section-pad qs-questions" id="qs-preguntas">
        <div class="container">
            <div class="qs-narrow">
                <p class="section-label"><?php esc_html_e('Todo comienza por hacer las preguntas correctas', 'refugios'); ?></p>
                <h2 class="qs-questions__title"><?php esc_html_e('Nos preguntamos', 'refugios'); ?></h2>
                <ol class="qs-questions__list">
                    <li><?php esc_html_e('¿Cómo democratizar el acceso a la cultura?', 'refugios'); ?></li>
                    <li><?php esc_html_e('¿Cómo diseñar comunidades más asequibles y habitables, donde la pausa sea posible en la vida cotidiana?', 'refugios'); ?></li>
                    <li><?php esc_html_e('¿Cómo hacer que leer vuelva a ser un acto consciente y no una tarea pendiente?', 'refugios'); ?></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- 05 · Siguiente lectura: Manifiestos -->
    <section class="section-pad qs-next" id="qs-manifiestos" aria-labelledby="qs-next-title">
        <div class="container">
            <div class="qs-next__box">
                <p class="qs-next__label"><?php esc_html_e('Sigue leyendo', 'refugios'); ?></p>
                <h2 class="qs-next__title" id="qs-next-title">
                    <?php esc_html_e('Entendemos la importancia de asumir posiciones en el mundo.', 'refugios'); ?>
                </h2>
                <p class="qs-next__text"><?php esc_html_e('Estas son las nuestras.', 'refugios'); ?></p>
                <a href="<?php echo esc_url(home_url('/manifiestos/')); ?>" class="btn btn-primary qs-next__btn">
                    <?php esc_html_e('Leer los manifiestos', 'refugios'); ?>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

</main><!-- #main-content -->

<?php get_footer();
