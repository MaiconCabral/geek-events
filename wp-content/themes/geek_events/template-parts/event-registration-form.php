<?php
$meta = Geek_Events_Helpers::get_event_meta(get_the_ID());
$available = Geek_Events_Helpers::get_available_tickets(get_the_ID());
$is_open = !in_array($meta['status'], ['encerrado', 'cancelado'], true);
$is_soldout = $available <= 0;
?>

<div class="sidebar-card registration-card">
    <h3 class="sidebar-card-title"><?php _e('PARTICIPAR', 'geek-events'); ?></h3>

    <?php if (!$is_open) : ?>
        <div class="registration-blocked">
            <span class="registration-blocked-icon">🚫</span>
            <p><?php _e('Este evento já foi encerrado.', 'geek-events'); ?></p>
        </div>

    <?php elseif ($is_soldout) : ?>
        <div class="registration-blocked">
            <span class="registration-blocked-icon">🎫</span>
            <p><?php _e('INGRESSOS ESGOTADOS', 'geek-events'); ?></p>
        </div>

    <?php else : ?>

        <form id="registration-form" class="registration-form" novalidate>
            <?php wp_nonce_field('geek_events_register_nonce', 'registration_nonce'); ?>
            <input type="hidden" name="event_id" value="<?php the_ID(); ?>">

            <div class="form-group">
                <label for="reg-name" class="form-label"><?php _e('NOME *', 'geek-events'); ?></label>
                <input type="text" id="reg-name" name="name" class="form-input" required placeholder="<?php esc_attr_e('Seu nome completo', 'geek-events'); ?>">
            </div>

            <div class="form-group">
                <label for="reg-email" class="form-label"><?php _e('E-MAIL *', 'geek-events'); ?></label>
                <input type="email" id="reg-email" name="email" class="form-input" required placeholder="<?php esc_attr_e('seu@email.com', 'geek-events'); ?>">
            </div>

            <div class="form-group">
                <label for="reg-phone" class="form-label"><?php _e('TELEFONE *', 'geek-events'); ?></label>
                <input type="tel" id="reg-phone" name="phone" class="form-input" required placeholder="<?php esc_attr_e('(11) 99999-8888', 'geek-events'); ?>">
            </div>

            <div class="form-group">
                <label for="reg-quantity" class="form-label"><?php _e('QUANTIDADE DE INGRESSOS', 'geek-events'); ?></label>
                <div class="quantity-wrapper">
                    <button type="button" class="quantity-btn" data-dir="down">−</button>
                    <input type="number" id="reg-quantity" name="quantity" class="form-input quantity-input" value="1" min="1" max="<?php echo $available; ?>" data-available="<?php echo $available; ?>">
                    <button type="button" class="quantity-btn" data-dir="up">+</button>
                </div>
                <span class="form-hint"><?php printf(__('%d ingresso(s) disponível(is)', 'geek-events'), $available); ?></span>
            </div>

            <div id="registration-message" class="registration-message" style="display:none;"></div>

            <button type="submit" class="btn btn-primary btn-submit" id="registration-submit">
                <?php _e('CONFIRMAR PRESENÇA', 'geek-events'); ?>
            </button>

            <p class="form-footer-text"><?php _e('Seu cadastro ficará pendente de confirmação.', 'geek-events'); ?></p>
        </form>

    <?php endif; ?>
</div>
