<?php
/*
* Template Name: Home Page
*
*/
?>
<?php get_header(); ?>
<?php  while (have_posts()):the_post(); ?>
    <div id="main">
        <div id="content">
        <h1>Choose Package</h1>
            <?php
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 3,
                'order' => 'ASC'
            );

            $query = new WP_Query( $args );

            if ($query->have_posts()) :
                while ($query->have_posts()) :
                    $query->the_post();
                    $product = wc_get_product( $query->post->ID );?>

                <div class="column">

                    <a class="img-holder" href="<?php echo get_the_permalink($product->ID)?>">
                        <h3><?php the_title();?></h3>
                    </a>
                </div>
            <?php endwhile;?>

            <?php endif;

            wp_reset_query(); // Remember to reset
            ?>

        </div>
    </div>
<?php endwhile;?>
<?php get_footer(); ?>
