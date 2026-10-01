<?php
/**
 * Refugios — footer.php
 * Footer con todos los links de la marca.
 */
?>

    <!-- =============================================
         SITE FOOTER
         ============================================= -->
    <footer id="site-footer" role="contentinfo">

        <div class="container">
            <div class="footer-top">

                <!-- Col 1: Brand -->
                <div class="footer-col footer-brand">
                    <div class="footer-brand__logo">
                        <?php
$blog_name = get_bloginfo('name');
echo '<span>' . esc_html(mb_substr($blog_name, 0, 1)) . '</span>'
    . esc_html(mb_substr($blog_name, 1));
?>
                    </div>
                    <p class="footer-brand__tagline">
                        <?php echo esc_html(get_theme_mod(
    'refugios_hero_subtitle',
    'Un refugio para libros, ideas y café de especialidad.'
)); ?>
                    </p>
                    <div class="footer-social">
                        <a href="https://www.instagram.com/libreriarefugios/"
                           target="_blank" rel="noopener"
                           aria-label="Instagram @libreriarefugios">
                            <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                        </a>
                        <a href="https://www.tiktok.com/@libreriarefugios"
                           target="_blank" rel="noopener"
                           aria-label="TikTok @libreriarefugios">
                            <i class="fa-brands fa-tiktok" aria-hidden="true"></i>
                        </a>
                        <a href="https://www.facebook.com/libreriarefugios/"
                           target="_blank" rel="noopener"
                           aria-label="Facebook libreriarefugios">
                            <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Tienda & Libros -->
                <div class="footer-col">
                    <h4 class="footer-widget__title"><?php esc_html_e('Tienda', 'refugios'); ?></h4>
                    <ul class="footer-links">
                        <li>
                            <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/tienda-refugios')); ?>">
                                <?php esc_html_e('Todos los libros', 'refugios'); ?>
                            </a>
                        </li>
                        <li><a href="<?php echo esc_url(home_url('/libros-leidos/')); ?>"><?php esc_html_e('Libros Leídos', 'refugios'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/pedidos-especiales/')); ?>"><?php esc_html_e('Pedidos Especiales', 'refugios'); ?></a></li>
                        <?php if (function_exists('wc_get_page_permalink')): ?>
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('cart')); ?>"><?php esc_html_e('Carrito', 'refugios'); ?></a></li>
                        <?php
endif; ?>
                    </ul>
                </div>

                <!-- Col 3: Información -->
                <div class="footer-col">
                    <h4 class="footer-widget__title"><?php esc_html_e('Nosotros', 'refugios'); ?></h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo esc_url(home_url('/quienes-somos/')); ?>"><?php esc_html_e('Quiénes Somos', 'refugios'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/manifiestos/')); ?>"><?php esc_html_e('Manifiestos', 'refugios'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/blog/')); ?>"><?php esc_html_e('Blog', 'refugios'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/preguntas-frecuentes/')); ?>"><?php esc_html_e('Preguntas Frecuentes', 'refugios'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contacto/')); ?>"><?php esc_html_e('Contacto', 'refugios'); ?></a></li>
                    </ul>
                </div>

                <!-- Col 4: Visítanos — hardcoded para que siempre muestre contenido -->
                <div class="footer-col">
                    <h4 class="footer-widget__title"><?php esc_html_e('Visítanos', 'refugios'); ?></h4>

                    <div class="footer-contact-item">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        <a href="https://share.google/IbIQ900QOHDdCiUCu"
                           target="_blank" rel="noopener">
                            Calle 51 &middot; 48&ndash;53 Local 5<br>Centro Comercial La Gran Manzana
                        </a>
                    </div>

                    <div class="footer-contact-item">
                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
                        <span>Medellín, Colombia<br>Lunes a Sábado &middot; 10 am &ndash; 7 pm</span>
                    </div>

                    <div class="footer-contact-item">
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                        <a href="https://wa.me/573238113985"
                           target="_blank" rel="noopener">
                            +57 323 811 39 85
                        </a>
                    </div>

                    <div class="footer-contact-item">
                        <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                        <a href="mailto:administracion@refugios.co">
                            administracion@refugios.co
                        </a>
                    </div>
                </div>

            </div><!-- .footer-top -->
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom-wrap">
            <div class="container">
                <div class="footer-bottom">
                    <p class="footer-copy">
                        &copy; <?php echo esc_html(gmdate('Y')); ?>
                        <strong><?php bloginfo('name'); ?></strong> —
                        <?php esc_html_e('Todos los derechos reservados.', 'refugios'); ?>
                    </p>
                    <nav class="footer-legal-links" aria-label="<?php esc_attr_e('Legal', 'refugios'); ?>">
                        <a href="<?php echo esc_url(home_url('/terminos-y-condiciones/')); ?>">
                            <?php esc_html_e('Términos y Condiciones', 'refugios'); ?>
                        </a>
                        <a href="<?php echo esc_url(home_url('/preguntas-frecuentes/')); ?>">
                            <?php esc_html_e('FAQ', 'refugios'); ?>
                        </a>
                    </nav>
                </div>
            </div>
        </div>

    </footer><!-- #site-footer -->

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/573238113985" class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat con Refugios en WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

<?php wp_footer(); ?>
</body>
</html>
