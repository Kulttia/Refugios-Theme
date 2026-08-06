<?php
/**
 * Refugios — page.php
 * Generic page template. Handles: Contacto, Quiénes Somos, FAQ,
 * Términos, Privacidad, Manifiestos, Libros Leídos, Lista de Deseos,
 * Pedidos Especiales and any other non-WooCommerce page.
 */

get_header(); ?>

<main id="main-content" role="main">

    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <?php refugios_breadcrumb(); ?>
            <h1 class="page-header__title"><?php the_title(); ?></h1>
        </div>
    </header>

    <?php while (have_posts()):
    the_post(); ?>

    <?php
    /* ----------------------------------------------------------------
     PLANTILLA ESPECIAL: CONTACTO
     Se detecta por slug 'contacto'
     ---------------------------------------------------------------- */
    if (is_page('contacto')): ?>

        <section class="section-pad" id="contacto-main">
            <div class="container">

                <div style="display: grid; grid-template-columns: 45% 55%; gap: 4rem; align-items: start;">

                    <!-- Left: Contact Info -->
                    <div>
                        <p class="section-label"><?php esc_html_e('Encuéntranos', 'refugios'); ?></p>
                        <h2 class="h2" style="margin-bottom: 2rem;">
                            <?php esc_html_e('Visítanos', 'refugios'); ?>
                        </h2>

                        <div class="contact-info-item">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            <div>
                                <div class="contact-info-label"><?php esc_html_e('Dirección', 'refugios'); ?></div>
                                <div class="contact-info-value" style="line-height:1.5;">
                                    Calle 51 &middot; 48&ndash;53 Local 5<br>
                                    Centro Comercial La Gran Manzana<br>
                                    Medellín, Colombia
                                </div>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i>
                            <div>
                                <div class="contact-info-label"><?php esc_html_e('Horario', 'refugios'); ?></div>
                                <div class="contact-info-value">
                                    Lunes a Sábado &middot; 10 am &ndash; 7 pm
                                </div>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                            <div>
                                <div class="contact-info-label"><?php esc_html_e('WhatsApp', 'refugios'); ?></div>
                                <div class="contact-info-value">
                                    <a href="https://wa.me/573238113985"
                                       target="_blank" rel="noopener"
                                       style="color: var(--color-amber);">
                                        +57 323 811 39 85
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                            <div>
                                <div class="contact-info-label"><?php esc_html_e('Email', 'refugios'); ?></div>
                                <div class="contact-info-value">
                                    <a href="mailto:administracion@refugios.co"
                                       style="color: var(--color-amber);">
                                        administracion@refugios.co
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Social -->
                        <div style="margin-top: 2rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <?php if ($ig = get_theme_mod('refugios_instagram')): ?>
                                <a href="<?php echo esc_url($ig); ?>" target="_blank" rel="noopener"
                                   class="btn btn-secondary" style="gap: 0.5rem; padding: 0.625rem 1rem;">
                                    <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                    Instagram
                                </a>
                            <?php
        endif; ?>
                            <?php if ($wa = get_theme_mod('refugios_whatsapp')): ?>
                                <a href="<?php echo esc_url($wa); ?>" target="_blank" rel="noopener"
                                   class="btn btn-primary" style="gap: 0.5rem; padding: 0.625rem 1rem;">
                                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                                    WhatsApp
                                </a>
                            <?php
        endif; ?>
                        </div>

                        <!-- Map placeholder removed -->
                    </div>

                    <!-- Right: Contact Form -->
                    <div class="box-brutal" style="padding: 2.5rem;">
                        <p class="section-label"><?php esc_html_e('Escríbenos', 'refugios'); ?></p>
                        <h2 class="h3" style="margin-bottom: 0.5rem;">
                            <?php esc_html_e('Escríbenos', 'refugios'); ?>
                        </h2>
                        <p style="font-family: var(--font-body); font-size: 0.875rem; color: rgba(78,52,46,0.65); margin-bottom: 2rem;">
                            <?php esc_html_e('Respondemos en menos de 24 horas.', 'refugios'); ?>
                        </p>

                        <?php echo do_shortcode('[contact-form-7 id="332cf75" title="Formulario de contacto"]'); ?>
                    </div>

                </div>
            </div>
        </section>

        <!-- Quote strip removed -->

    <?php
    /* ----------------------------------------------------------------
     PLANTILLA ESPECIAL: TÉRMINOS / PRIVACIDAD / FAQ
     ---------------------------------------------------------------- */
    elseif (is_page(['terminos-y-condiciones', 'preguntas-frecuentes'])): ?>

        <section class="section-pad" id="legal-page">
            <div class="container">
                <div class="legal-layout">

                    <!-- TOC Sidebar -->
                    <aside class="legal-toc" aria-label="<?php esc_attr_e('Tabla de contenidos', 'refugios'); ?>">
                        <div class="legal-toc__header"><?php esc_html_e('Contenido', 'refugios'); ?></div>
                        <ul class="legal-toc__list" id="legal-toc-list">
                            <!-- Populated via JS from h2 headings -->
                        </ul>
                    </aside>

                    <!-- Content -->
                    <div class="legal-content post-content" id="legal-main">
                        <?php the_content(); ?>
                    </div>

                </div>

                <!-- CTA Box -->
                <div class="box-brutal" style="text-align: center; padding: 2rem; margin-top: 4rem;">
                    <h3 style="font-family: var(--font-serif); font-size: 1.5rem; margin-bottom: 0.75rem;">
                        <?php esc_html_e('¿Tienes preguntas?', 'refugios'); ?>
                    </h3>
                    <a href="<?php echo esc_url(home_url('/contacto')); ?>"
                       class="btn btn-primary"
                       id="legal-contact-btn">
                        <?php esc_html_e('Contáctanos', 'refugios'); ?>
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>

    <?php
    /* ----------------------------------------------------------------
     PLANTILLA GENÉRICA: Quiénes Somos, Manifiestos, etc.
     ---------------------------------------------------------------- */
    else: ?>

        <section class="section-pad" id="page-content">
            <div class="container">
                <div style="max-width: 860px; margin-inline: auto;">
                    <div class="post-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </section>

    <?php
    endif; ?>

    <?php
endwhile; ?>

</main><!-- #main-content -->

<?php get_footer();
