<?php
/**
 * Refugios — woocommerce.php
 * WooCommerce wrapper template.
 * Replaces woocommerce/woocommerce.php
 */
get_header(); ?>

<main id="main-content" role="main">

    <!-- WooCommerce page header -->
    <header class="page-header page-header--teal">
        <div class="container">
            <?php refugios_breadcrumb(); ?>
            <h1 class="refugios-banner-title"><?php woocommerce_page_title(); ?></h1>
        </div>
    </header>

    <div class="container woo-main-wrapper section-pad">
        <?php woocommerce_content(); ?>
    </div>

</main><!-- #main-content -->

<?php get_footer();
