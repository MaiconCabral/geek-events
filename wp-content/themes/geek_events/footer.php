    </main>

    <footer id="site-footer" class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo">GEEK EVENTS</a>
                <p class="footer-desc"><?php _e('O maior portal de eventos geeks do Brasil.', 'geek-events'); ?></p>
                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="Instagram" target="_blank" rel="noopener">IG</a>
                    <a href="#" class="social-link" aria-label="Twitter/X" target="_blank" rel="noopener">TW</a>
                    <a href="#" class="social-link" aria-label="Discord" target="_blank" rel="noopener">DC</a>
                    <a href="#" class="social-link" aria-label="YouTube" target="_blank" rel="noopener">YT</a>
                </div>
            </div>
            <div class="footer-nav">
                <h3 class="footer-heading"><?php _e('NAVEGUE', 'geek-events'); ?></h3>
                <?php wp_nav_menu([
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'depth'          => 1,
                ]); ?>
            </div>
            <div class="footer-contact">
                <h3 class="footer-heading"><?php _e('CONTATO', 'geek-events'); ?></h3>
                <ul class="footer-contact-list">
                    <li><?php _e('contato@geekevents.com.br', 'geek-events'); ?></li>
                    <li><?php _e('(11) 99999-8888', 'geek-events'); ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bar">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('TODOS OS DIREITOS RESERVADOS.', 'geek-events'); ?></p>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
