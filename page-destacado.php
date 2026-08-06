<?php
/**
 * Template Name: Página Producto Destacado
 * The template for displaying the monthly featured product storytelling page.
 * 
 * Auto-mapped to URL slug /destacado
 */

defined('ABSPATH') || exit;

// 1. Load configuration file
$config_path = get_template_directory() . '/featured-book-config.json';
$config = [];
if (file_exists($config_path)) {
    $config_json = file_get_contents($config_path);
    $config = json_decode($config_json, true);
}

// 2. Fetch WooCommerce product dynamically with robust fallback lookups
$product = null;
$product_id = 0;
$price_html = '';
$add_to_cart_url = '#';

if (function_exists('wc_get_product')) {
    // Stage 1: Try exact product_id if set in JSON config
    $config_product_id = isset($config['product_id']) ? intval($config['product_id']) : 0;
    if ($config_product_id > 0) {
        $product = wc_get_product($config_product_id);
    }

    // Stage 2: Try slug lookup with variations
    if (!$product && !empty($config['product_slug'])) {
        $slug_variations = [
            sanitize_title($config['product_slug']),                  // exact slug
            'la-ultima-niebla-la-amortajada',                         // standard sanitized
            'la-ultima-niebla-amortajada',                            // without "la"
            'la-ultima-niebla',                                       // first title only
            'la-amortajada'                                           // second title only
        ];
        
        foreach (array_unique($slug_variations) as $slug) {
            $posts = get_posts([
                'name'        => $slug,
                'post_type'   => 'product',
                'post_status' => 'any', // Include draft/private for testing
                'posts_per_page' => 1
            ]);
            if (!empty($posts)) {
                $product = wc_get_product($posts[0]->ID);
                break;
            }
        }
    }

    // Stage 3: Try lookup by exact title
    if (!$product && !empty($config['book_title'])) {
        $posts = get_posts([
            'title'       => $config['book_title'],
            'post_type'   => 'product',
            'post_status' => 'any',
            'posts_per_page' => 1
        ]);
        if (!empty($posts)) {
            $product = wc_get_product($posts[0]->ID);
        }
    }

    // Stage 4: Try lookup by partial title search
    if (!$product) {
        $posts = get_posts([
            's'           => 'La última niebla',
            'post_type'   => 'product',
            'post_status' => 'any',
            'posts_per_page' => 1
        ]);
        if (!empty($posts)) {
            $product = wc_get_product($posts[0]->ID);
        }
    }

    // Process resolved product data
    if ($product && is_a($product, 'WC_Product')) {
        $product_id = $product->get_id();
        $price_html = $product->get_price_html();
        
        // WooCommerce native URL to add to cart and redirect directly to the cart page
        $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/carrito');
        $add_to_cart_url = add_query_arg('add-to-cart', $product_id, $cart_url);
    }
}

// Fallback if product is not found in WooCommerce (for mock or draft stages)
if (!$product_id && !empty($config['product_id'])) {
    $product_id = intval($config['product_id']);
    $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/carrito');
    $add_to_cart_url = add_query_arg('add-to-cart', $product_id, $cart_url);
}

get_header();
?>

<!-- Custom CSS for Storytelling Animations & Brutalist Touches -->
<style>
    /* Custom Scroll Transitions */
    .reveal-item {
        opacity: 0;
        transform: translateY(35px);
        transition: opacity 0.8s cubic-bezier(0.25, 1, 0.5, 1), transform 0.8s cubic-bezier(0.25, 1, 0.5, 1);
        will-change: transform, opacity;
    }
    
    .reveal-item.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-img-container {
        position: relative;
        overflow: hidden;
    }

    .reveal-img-container::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: var(--color-brown);
        transform: scaleY(1);
        transform-origin: top;
        transition: transform 1s cubic-bezier(0.77, 0, 0.175, 1);
        z-index: 10;
        pointer-events: none;
    }

    .reveal-img-container.visible::after {
        transform: scaleY(0);
    }

    /* Brutalist Custom Styling overrides */
    .story-hero-title {
        font-size: clamp(2.5rem, 8vw, 5.5rem);
        line-height: 0.95;
    }

    .story-section-title {
        font-size: clamp(1.8rem, 4vw, 3rem);
        line-height: 1.1;
    }

    .brutal-shadow-hover {
        transition: transform 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .brutal-shadow-hover:hover {
        transform: translate(-4px, -4px);
        box-shadow: 8px 8px 0px var(--color-brown);
    }

    .brutal-shadow-hover:active {
        transform: translate(2px, 2px);
        box-shadow: 2px 2px 0px var(--color-brown);
    }

    /* Paragraph custom formatting */
    .story-paragraph p {
        margin-bottom: 1.5rem;
        font-family: var(--font-body);
        font-size: 1.125rem;
        line-height: 1.8;
    }

    .story-paragraph p strong, .story-paragraph p em {
        color: var(--color-brown);
    }
</style>

<main id="main-content" class="bg-cream text-brown min-h-screen overflow-x-hidden font-body" role="main">

    <?php if (current_user_can('manage_options') && !$product_id): ?>
        <!-- Admin Notice for Product Mapping Debugging -->
        <div class="container mx-auto px-4 pt-6 max-w-4xl">
            <div class="bg-amber/20 text-brown border-2 border-brown p-4 font-sans text-xs md:text-sm font-bold flex flex-col md:flex-row items-center gap-3 shadow-[4px_4px_0px_var(--color-brown)]">
                <i class="fa-solid fa-triangle-exclamation text-lg text-brown" aria-hidden="true"></i>
                <div class="flex-1">
                    <strong>Aviso de Administrador:</strong> WooCommerce no pudo encontrar ningún producto con el slug <code>la-ultima-niebla-la-amortajada</code>. El botón de compra no funcionará correctamente (ID: 0). Por favor, asegúrate de que el slug del producto sea exacto en tu catálogo de WooCommerce, o edita <code>featured-book-config.json</code> para definir el ID numérico correcto (ej. <code>"product_id": 1234</code>).
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- =============================================
         HERO SECTION: THE STORY STARTS
         ============================================= -->
    <header class="relative pt-12 pb-24 md:py-32 border-b-3 border-brown">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left: Header copy -->
                <div class="lg:col-span-7 flex flex-col justify-center order-2 lg:order-1 reveal-item" id="hero-left-content">
                    <span class="inline-block px-3 py-1 font-sans text-xs font-bold uppercase tracking-widest bg-amber border-2 border-brown text-brown mb-6 self-start">
                        <?php echo esc_html(isset($config['hero']['label']) ? $config['hero']['label'] : 'Destacado'); ?>
                    </span>
                    
                    <h1 class="story-hero-title font-serif font-extrabold text-brown tracking-tighter mb-6">
                        <?php echo wp_kses_post(isset($config['hero']['title']) ? $config['hero']['title'] : get_the_title()); ?>
                    </h1>

                    <h2 class="font-sans text-xl md:text-2xl font-semibold text-brown/85 border-l-4 border-amber pl-4 mb-8 leading-snug">
                        <?php echo esc_html(isset($config['hero']['subtitle']) ? $config['hero']['subtitle'] : ''); ?>
                    </h2>

                    <div class="text-lg text-brown/75 leading-relaxed mb-8 max-w-2xl font-body">
                        <?php echo wp_kses_post(isset($config['hero']['intro']) ? $config['hero']['intro'] : ''); ?>
                    </div>

                    <!-- Scroll Trigger Button -->
                    <a href="#amores-fallidos" class="btn btn-secondary brutal-shadow-hover self-start mt-2">
                        <?php esc_html_e('Entrar en la historia', 'refugios'); ?>
                        <i class="fa-solid fa-arrow-down ml-2" aria-hidden="true"></i>
                    </a>
                </div>

                <!-- Right: Cover image layout -->
                <div class="lg:col-span-5 flex justify-center order-1 lg:order-2">
                    <div class="relative w-full max-w-xs md:max-w-sm reveal-img-container shadow-brutal border-3 border-brown bg-cream transition-transform duration-300 hover:scale-[1.02]">
                        <?php 
                        $hero_cover = isset($config['hero']['cover_image']) ? $config['hero']['cover_image'] : '';
                        if ($hero_cover): 
                        ?>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/' . $hero_cover); ?>" 
                                 alt="<?php echo esc_attr(isset($config['book_title']) ? $config['book_title'] : 'Portada de Libro'); ?>"
                                 class="w-full h-auto block"
                                 style="object-fit: contain !important; height: auto !important; max-height: none !important; aspect-ratio: auto !important;"
                                 loading="eager">
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </header>


    <!-- =============================================
         CHAPTERS LOOP
         ============================================= -->
    <?php 
    $chapters = isset($config['chapters']) ? $config['chapters'] : [];
    if (!empty($chapters)):
        foreach ($chapters as $index => $chapter):
            $is_left = isset($chapter['image_position']) && $chapter['image_position'] === 'left';
            $image_url = isset($chapter['image']) ? get_template_directory_uri() . '/' . $chapter['image'] : '';
    ?>
        <section id="<?php echo esc_attr($chapter['id']); ?>" 
                 class="py-20 md:py-28 border-b-3 border-brown bg-cream relative">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                    
                    <!-- Chapter Image -->
                    <div class="lg:col-span-5 <?php echo $is_left ? 'order-1' : 'order-1 lg:order-2'; ?> flex justify-center">
                        <div class="relative w-full max-w-md reveal-img-container border-3 border-brown shadow-brutal bg-cream">
                            <?php if ($image_url): ?>
                                <img src="<?php echo esc_url($image_url); ?>" 
                                     alt="<?php echo esc_attr($chapter['title']); ?>"
                                     class="w-full h-auto block filter grayscale hover:grayscale-0 transition-all duration-500"
                                     style="object-fit: contain !important; height: auto !important; max-height: none !important; aspect-ratio: auto !important;"
                                     loading="lazy">
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Chapter Text Content -->
                    <div class="lg:col-span-7 <?php echo $is_left ? 'order-2' : 'order-2 lg:order-1'; ?> flex flex-col justify-center reveal-item">
                        <span class="font-sans text-xs font-bold uppercase tracking-widest text-amber mb-4">
                            <?php printf(esc_html__('Capítulo %d', 'refugios'), $index + 1); ?>
                        </span>
                        
                        <h2 class="story-section-title font-serif font-extrabold text-brown tracking-tight mb-8">
                            <?php echo wp_kses_post($chapter['title']); ?>
                        </h2>

                        <div class="story-paragraph text-brown/80">
                            <?php echo wp_kses_post(wpautop($chapter['content'])); ?>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    <?php 
        endforeach;
    endif; 
    ?>


    <!-- =============================================
         IMMERSE CALL TO ACTION (CTA)
         ============================================= -->
    <section id="cta-seccion" class="py-24 bg-brown text-cream relative overflow-hidden">
        <div class="container mx-auto px-4 max-w-4xl relative z-10">
            
            <div class="box-brutal border-3 border-cream bg-brown p-8 md:p-12 shadow-[6px_6px_0px_var(--color-amber)] reveal-item flex flex-col items-center text-center">
                
                <span class="px-3 py-1 font-sans text-xs font-bold uppercase tracking-widest bg-amber text-brown border-2 border-cream mb-6">
                    <?php esc_html_e('Llévate la historia a casa', 'refugios'); ?>
                </span>

                <h2 class="font-serif text-3xl md:text-5xl font-bold tracking-tight text-cream mb-4">
                    <?php echo esc_html(isset($config['book_title']) ? $config['book_title'] : 'La última niebla / La amortajada'); ?>
                </h2>

                <h3 class="font-sans text-md font-semibold text-amber uppercase tracking-wider mb-8">
                    <?php echo esc_html(isset($config['author_name']) ? $config['author_name'] : 'María Luisa Bombal'); ?>
                </h3>

                <div class="text-cream/80 text-lg leading-relaxed text-center max-w-2xl mb-10 font-body">
                    <?php echo wp_kses_post(isset($config['cta']['description']) ? $config['cta']['description'] : ''); ?>
                </div>

                <!-- Display Price and Checkout Link if Product is Active -->
                <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 mt-4">
                    <?php if ($price_html): ?>
                        <div class="font-serif text-3xl md:text-4xl font-extrabold text-amber bg-cream border-2 border-cream text-brown px-6 py-2">
                            <?php echo wp_kses_post($price_html); ?>
                        </div>
                    <?php endif; ?>

                    <a href="<?php echo esc_url($add_to_cart_url); ?>" 
                       class="btn btn-primary btn-teal border-2 border-cream text-brown font-bold tracking-wider py-4 px-8 text-md brutal-shadow-hover transition-all duration-150"
                       style="background-color: var(--color-amber); box-shadow: 4px 4px 0px #ffffff;"
                       id="featured-buy-cta">
                        <i class="<?php echo esc_attr(isset($config['cta']['icon']) ? $config['cta']['icon'] : 'fa-solid fa-basket-shopping'); ?> mr-2" aria-hidden="true"></i>
                        <?php echo esc_html(isset($config['cta']['button_text']) ? $config['cta']['button_text'] : 'Comprar libro'); ?>
                    </a>
                </div>

            </div>

        </div>
        
        <!-- Brutalist background accent shapes (pure HTML/CSS) -->
        <div class="absolute top-0 left-0 w-24 h-24 border-r-3 border-b-3 border-cream/10 pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-32 h-32 border-l-3 border-t-3 border-cream/10 pointer-events-none"></div>
    </section>

</main>


<!-- =============================================
     SCROLL REVEAL INTERSECTION OBSERVER SCRIPT
     ============================================= -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Intersection Observer for standard reveal elements (text, buttons, headers)
    const revealItems = document.querySelectorAll(".reveal-item");
    
    const revealCallback = function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
                observer.unobserve(entry.target); // Trigger once
            }
        });
    };

    const revealObserver = new IntersectionObserver(revealCallback, {
        root: null,
        threshold: 0.12,
        rootMargin: "0px 0px -50px 0px"
    });

    revealItems.forEach(item => {
        revealObserver.observe(item);
    });

    // 2. Intersection Observer for Image containers with a slide reveal cover
    const revealImages = document.querySelectorAll(".reveal-img-container");

    const imgCallback = function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
                observer.unobserve(entry.target);
            }
        });
    };

    const imgObserver = new IntersectionObserver(imgCallback, {
        root: null,
        threshold: 0.15,
        rootMargin: "0px 0px -60px 0px"
    });

    revealImages.forEach(img => {
        imgObserver.observe(img);
    });
});
</script>

<?php
get_footer();
