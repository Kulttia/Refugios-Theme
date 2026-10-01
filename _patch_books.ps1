# Patch: replace book-card article block in front-page.php
$path = "front-page.php"
$raw  = [System.IO.File]::ReadAllText($path, [System.Text.Encoding]::UTF8)

# Old block marker: starts at '<article class="book-card"' ends just before '<?php' + whitespace + 'endforeach'
$old  = @'
                        <article class="book-card" id="product-<?php echo esc_attr($product_id); ?>">

                            <?php if ($is_featured || $is_sale): ?>
                                <span class="book-card__badge">
                                    <?php echo $is_sale
                ? esc_html__('Oferta', 'refugios')
                : esc_html__('Destacado', 'refugios');
?>
                                </span>
                            <?php
        endif; ?>

                            <a href="<?php echo esc_url($permalink); ?>"
                               class="book-card__image"
                               tabindex="-1"
                               aria-hidden="true">
                                <img src="<?php echo esc_url($img_url); ?>"
                                     alt="<?php echo esc_attr($title); ?>"
                                     loading="lazy"
                                     width="400" height="600">
                            </a>

                            <div class="book-card__body">
                                <?php if ($cat): ?>
                                    <span class="book-card__category"><?php echo esc_html($cat); ?></span>
                                <?php
        endif; ?>

                                <h3 class="book-card__title">
                                    <a href="<?php echo esc_url($permalink); ?>">
                                        <?php echo esc_html($title); ?>
                                    </a>
                                </h3>

                                <div class="book-card__footer">
                                    <div class="book-card__price">
                                        <?php echo wp_kses_post($price); ?>
                                    </div>
                                    <a href="<?php echo esc_url($permalink); ?>"
                                       class="book-card__btn">
                                        <?php esc_html_e('Ver libro', 'refugios'); ?>
                                    </a>
                                </div>
                            </div>

                        </article><!-- .book-card -->
'@

$new  = @'
                        <article class="book-card book-card--text" id="product-<?php echo esc_attr($product_id); ?>">
                            <div class="book-card__body">
                                <?php if ($is_featured || $is_sale): ?>
                                    <span class="book-card__badge book-card__badge--inline">
                                        <?php echo $is_sale ? esc_html__( 'Oferta', 'refugios' ) : esc_html__( 'Destacado', 'refugios' ); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($cat): ?>
                                    <span class="book-card__category"><?php echo esc_html($cat); ?></span>
                                <?php endif; ?>
                                <h3 class="book-card__title">
                                    <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                                </h3>
                                <?php $desc = $product->get_short_description() ?: $product->get_description(); if ($desc): ?>
                                    <p class="book-card__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($desc), 18, '...')); ?></p>
                                <?php endif; ?>
                                <div class="book-card__footer">
                                    <div class="book-card__price"><?php echo wp_kses_post($price); ?></div>
                                    <a href="<?php echo esc_url($permalink); ?>" class="book-card__btn"><?php esc_html_e('Ver libro','refugios'); ?></a>
                                </div>
                            </div>
                        </article><!-- .book-card -->
'@

# Normalize line endings for comparison
$oldNorm = $old -replace "`r`n", "`n"
$rawNorm = $raw -replace "`r`n", "`n"

if ($rawNorm.Contains($oldNorm)) {
    $newNorm = $rawNorm.Replace($oldNorm, ($new -replace "`r`n", "`n"))
    # Restore CRLF
    $newCrlf = $newNorm -replace "(?<!`r)`n", "`r`n"
    [System.IO.File]::WriteAllText($path, $newCrlf, [System.Text.Encoding]::UTF8)
    Write-Host "SUCCESS: book-card block replaced."
} else {
    Write-Host "NOT FOUND: block not matched. Checking for partial..."
    if ($rawNorm.Contains('book-card__image')) {
        Write-Host "Image block IS present in file."
    } else {
        Write-Host "Image block NOT present in file."
    }
}
