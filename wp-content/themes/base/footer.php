        </div><!-- .site-content -->

	<footer id="colophon" class="site-footer" role="contentinfo">



        <div class="site-info">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php //printf( __( 'Proudly powered by base_theme', 'base_theme' )); ?></a>
		</div><!-- .site-info -->

        <?php
            $args = array(
                'theme_location' => 'footer-menu',
                'menu' => '',
                'container' => 'nav',
                'container_class' => '',
                'container_id' => '',
                'menu_class' => 'main-menu',
                'menu_id' => 'main-menu',
                'echo' => true,
                'fallback_cb' => 'wp_page_menu',
                'before' => '',
                'after' => '',
                'link_before' => '',
                'link_after' => '',
                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'depth' => 0,
                'walker' => new CustomWalkerNavMenu
            );
            if (has_nav_menu('footer-menu')) {
                wp_nav_menu($args);
            }
        ?>


	</footer><!-- .site-footer -->

</div><!-- .wrapper -->

<?php wp_footer(); ?>

</body>
</html>
