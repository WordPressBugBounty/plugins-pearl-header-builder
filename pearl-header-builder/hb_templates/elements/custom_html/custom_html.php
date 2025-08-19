<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! empty( $element['value'] ) ) :
	$fwn = ( ! empty( $element['data']['fwn'] ) ) ? $element['data']['fwn'] : 'fwn';
	?>
	<div class="stm-text <?php echo esc_attr( $fwn ); ?>">
		<?php echo wp_kses_post( $element['value'] ); ?>
	</div>
<?php endif; ?>
