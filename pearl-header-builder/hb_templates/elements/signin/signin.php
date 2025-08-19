<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>

<?php if ( class_exists( 'WooCommerce' ) ) : ?>
	<div class="stm_woo__signin">
		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="no_deco">
				<i class="fa fa-user stm_mgr_8"></i>
				<?php esc_html_e( 'My account', 'pearl-header-builder' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="no_deco">
				<i class="fa fa-user stm_mgr_8"></i>
				<?php esc_html_e( 'Login / Register', 'pearl-header-builder' ); ?>
			</a>
		<?php endif; ?>
	</div>
<?php endif; ?>
