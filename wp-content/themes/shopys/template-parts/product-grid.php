<?php
/**
 * Premium Product Grid Shortcode
 * Usage: [premium_products limit="12" columns="4" filter="true" cart="true" pagination_type="normal" category="" filter_tabs=""]
 *
 * @package Shopys
 */

function premium_product_grid_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'limit'           => 12,
        'columns'         => 4,
        'category'        => '',
        'filter_tabs'     => '',
        'orderby'         => 'date',
        'order'           => 'DESC',
        'filter'          => 'false',
        'cart'            => 'true',
        'pagination_type' => 'normal',
    ), $atts, 'premium_products' );

    $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => intval( $atts['limit'] ),
        'paged'          => $paged,
        'orderby'        => sanitize_text_field( $atts['orderby'] ),
        'order'          => sanitize_text_field( $atts['order'] ),
    );

    // Category filter — drives which products are loaded
    if ( ! empty( $atts['category'] ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy'         => 'product_cat',
                'field'            => 'slug',
                'terms'            => array_map( 'trim', explode( ',', $atts['category'] ) ),
                'include_children' => true,
            ),
        );
    }

    // Get categories for filter tab buttons
    $has_filter_tabs = ! empty( $atts['filter_tabs'] );
    $cat_args = array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true, // only show tabs that have at least 1 product
    );

    if ( $has_filter_tabs ) {
        $cat_args['slug'] = array_map( 'trim', explode( ',', $atts['filter_tabs'] ) );
        // Bust object cache so stale counts don't affect results
        wp_cache_delete( 'get_terms', 'terms' );
        delete_transient( 'wc_product_loop' );
        // Force WooCommerce to recount so hide_empty is accurate
        if ( function_exists( 'wc_update_product_cat_counts' ) ) {
            wc_update_product_cat_counts();
        }
    } elseif ( ! empty( $atts['category'] ) ) {
        $cat_args['slug'] = array_map( 'trim', explode( ',', $atts['category'] ) );
    }

    $product_categories = get_terms( $cat_args );

    $query = new WP_Query( $args );

    ob_start();
    ?>
    <div class="ppg-container">

        <?php if ( $atts['filter'] === 'true' && ! is_wp_error( $product_categories ) && ! empty( $product_categories ) ) : ?>
        <div class="ppg-filter-bar">
            <button class="ppg-filter-btn active" data-category="all">
                <span class="ppg-filter-icon">🏪</span> All
            </button>
            <?php foreach ( $product_categories as $cat ) : ?>
            <button class="ppg-filter-btn" data-category="<?php echo esc_attr( $cat->slug ); ?>">
                <?php echo esc_html( $cat->name ); ?>
                <span class="ppg-filter-count"><?php echo esc_html( $cat->count ); ?></span>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( $query->have_posts() ) : ?>

        <?php if ( $atts['pagination_type'] === 'infinite' ) : ?>
        <div class="ppg-infinite-status"
             data-page="1"
             data-max="<?php echo esc_attr( $query->max_num_pages ); ?>"
             data-category="<?php echo esc_attr( $atts['category'] ); ?>"
             data-limit="<?php echo esc_attr( $atts['limit'] ); ?>"
             data-orderby="<?php echo esc_attr( $atts['orderby'] ); ?>"
             data-order="<?php echo esc_attr( $atts['order'] ); ?>"></div>
        <?php endif; ?>

        <div class="ppg-grid ppg-cols-<?php echo esc_attr( $atts['columns'] ); ?>">
        <?php while ( $query->have_posts() ) : $query->the_post();
            $product = wc_get_product( get_the_ID() );
            if ( ! $product ) continue;

            $thumb_id   = $product->get_image_id();
            $thumb_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';
            $cats       = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'slugs' ) );
            $cat_data   = implode( ' ', ( ! is_wp_error( $cats ) ? $cats : array() ) );
            $regular    = (float) $product->get_regular_price();
            $sale       = (float) $product->get_sale_price();
            $pct        = ( $product->is_on_sale() && $regular > 0 ) ? round( ( ( $regular - $sale ) / $regular ) * 100 ) : 0;
            $sku        = $product->get_sku();
            $short_desc = $product->get_short_description();
            if ( empty( $short_desc ) ) {
                $short_desc = $product->get_description();
            }
        ?>
            <div class="ppg-card" data-categories="<?php echo esc_attr( $cat_data ); ?>">

                <?php if ( $product->is_on_sale() || ! $product->is_in_stock() ) : ?>
                <div class="ppg-badges">
                    <?php if ( $product->is_on_sale() ) : ?>
                        <span class="ppg-badge ppg-badge-sale">-<?php echo esc_html( $pct ); ?>%</span>
                    <?php endif; ?>
                    <?php if ( ! $product->is_in_stock() ) : ?>
                        <span class="ppg-badge ppg-badge-oos"><?php esc_html_e( 'Sold Out', 'shopys' ); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <a href="<?php echo esc_url( get_permalink() ); ?>" class="ppg-image-link">
                    <div class="ppg-image-wrapper">
                        <?php if ( $thumb_url ) : ?>
                            <img class="ppg-image" src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy" />
                        <?php else : ?>
                            <div class="ppg-no-image">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <path d="m21 15-5-5L5 21"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>

                <div class="ppg-info">
                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="ppg-title-link">
                        <h3 class="ppg-title"><?php echo esc_html( $product->get_name() ); ?></h3>
                    </a>

                    <div class="ppg-price-row">
                        <?php if ( $product->is_on_sale() ) : ?>
                            <span class="ppg-price-regular"><?php echo wc_price( $product->get_regular_price() ); ?></span>
                            <span class="ppg-price-sale"><?php echo wc_price( $product->get_sale_price() ); ?></span>
                        <?php else : ?>
                            <span class="ppg-price-current"><?php echo $product->get_price_html(); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ( $short_desc ) :
                        $desc_clean = wp_strip_all_tags( $short_desc );
                        $specs = preg_split( '/[.\n]+/', $desc_clean, -1, PREG_SPLIT_NO_EMPTY );
                    ?>
                    <div class="ppg-specs">
                        <?php if ( ! empty( $specs ) ) : ?>
                        <ul class="ppg-specs-list">
                            <?php foreach ( $specs as $spec ) :
                                $spec = trim( $spec );
                                if ( ! empty( $spec ) && strlen( $spec ) > 2 ) : ?>
                                <li><?php echo esc_html( $spec ); ?></li>
                            <?php endif; endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( $sku ) : ?>
                    <div class="ppg-sku">
                        <span class="ppg-sku-separator"></span>
                        <?php esc_html_e( 'Code', 'shopys' ); ?> : <?php echo esc_html( $sku ); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( $atts['cart'] === 'true' ) : ?>
                    <div class="ppg-actions">
                        <?php woocommerce_template_loop_add_to_cart(); ?>
                    </div>
                    <?php else : ?>
                    <!-- <div class="ppg-actions">
                        <a href="<?php echo esc_url( get_permalink() ); ?>" class="button ppg-view-btn">
                            <?php esc_html_e( 'View Product', 'shopys' ); ?>
                        </a>
                    </div> -->
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        </div>

        <?php if ( $query->max_num_pages > 1 ) : ?>
        <div class="ppg-pagination" data-type="<?php echo esc_attr( $atts['pagination_type'] ); ?>">
            <?php if ( $atts['pagination_type'] === 'infinite' ) : ?>
                <div class="ppg-infinite-loader">
                    <div class="aps-spinner ppg-spinner"></div>
                    <span class="ppg-infinite-text">Loading more...</span>
                </div>
            <?php endif; ?>
            <div class="ppg-pagination-links <?php echo $atts['pagination_type'] === 'infinite' ? 'ppg-hidden' : ''; ?>">
                <?php echo paginate_links( array(
                    'total'     => $query->max_num_pages,
                    'current'   => $paged,
                    'prev_text' => '&larr; Prev',
                    'next_text' => 'Next &rarr;',
                ) ); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php else : ?>
        <div class="ppg-no-products">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <p><?php esc_html_e( 'No products found.', 'shopys' ); ?></p>
        </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'premium_products', 'premium_product_grid_shortcode' );
