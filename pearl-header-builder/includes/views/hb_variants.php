<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

$hbs = stm_get_hb_variants();
$hb  = stm_hb_save_name();

$current_hb = ( ! empty( $hbs[ $hb ] ) ) ? $hb : '';
update_option( 'stm_last_edited_hb', $current_hb );
?>

<div class="manage-menus">
	<form method="get" action="<?php echo esc_url( add_query_arg( array( 'page' => 'pearl_header_builder' ), admin_url( 'admin.php' ) ) ); ?>">
		<label for="select-menu-to-edit" class="selected-menu">
			<?php esc_html_e( 'Select a header to edit:', 'pearl-header-builder' ); ?>
		</label>
		<input type="hidden" name="page" value="stm_header_builder">
		<select name="hb" id="select-menu-to-edit">
			<?php foreach ( $hbs as $value => $name ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $hb ); ?>>
					<?php echo wp_kses_post( $name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<span class="submit-btn"><input type="submit" class="button" value="<?php esc_html_e( 'Select', 'pearl-header-builder' ); ?>"></span>
		<span class="add-new-menu-action">
			<?php
				printf(
					/* translators: %s: URL to create a new header */
					wp_kses_post( __( 'or <a href="%s">create a new header</a>.', 'pearl-header-builder' ) ),
					esc_url(
						add_query_arg(
							array(
								'hb'   => $hb,
								'page' => 'pearl_header_builder',
							),
							admin_url( 'admin.php' )
						)
					)
				);
				?>
		</span>
	</form>

	<form method="get" action="<?php echo esc_url( esc_url( add_query_arg( array( 'page' => 'pearl_header_builder' ), admin_url( 'admin.php' ) ) ) ); ?>">
		<div class="stm_hb_add_new">
			<input name="hb" type="text" placeholder="<?php esc_attr_e( 'Enter header name', 'pearl-header-builder' ); ?>"/>
			<input type="hidden" name="page" value="stm_header_builder">
			<input type="hidden" name="hb_action" value="stm_hb_add_header">
			<?php wp_nonce_field( 'stm_hb_add_nonce', 'stm_hb_add_nonce' ); ?>
			<button class="md-raised md-primary md-button md-ink-ripple" type="submit">
				<?php esc_html_e( 'Add new header', 'pearl-header-builder' ); ?>
			</button>
		</div>
	</form>

	<label class="stm_to_get_hb_code__label">
		<?php
		printf(
			/* translators: %s: Shortcode to the header template */
			wp_kses_post( __( 'Insert shortcode or action to display your builded header in <a href="%s" target="_blank">header template</a>', 'pearl-header-builder' ) ),
			wp_kses_post( add_query_arg( array( 'file' => 'header.php' ), admin_url( 'theme-editor.php' ) ) )
		);
		?>
	</label>

	<div class="stm_to_get_hb_code">
		<pre><?php echo esc_html( "<!--Header builder BEGIN-->\n<?php do_action('stm_hb', array('header' => '{$hb}')); ?>\n<!--Header builder END-->" ); ?></pre>
		<?php esc_html_e( 'OR', 'pearl-header-builder' ); ?>
		<pre><?php echo esc_html( "<!--Header builder BEGIN-->\n<?php echo do_shortcode(\"[stm_hb header='{$hb}']\"); ?>\n<!--Header builder END-->" ); ?></pre>
	</div>
</div>
