    </main>

    <footer id="site-footer">
        <div class="container">
            <nav id="footer-nav">
                <?php wp_nav_menu(['theme_location' => 'footer', 'menu_class' => 'footer-menu']); ?>
            </nav>
            <div class="site-info">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('Todos os direitos reservados.', 'geek-events'); ?></p>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
