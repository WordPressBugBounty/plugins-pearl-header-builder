<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! empty( $element['value'] ) ) :
	$fwn = ( ! empty( $element['data']['fwn'] ) ) ? $element['data']['fwn'] : 'fwn'; ?>
	<div class="stm-shortcode <?php echo esc_attr( $fwn ); ?>">
		<?php echo do_shortcode( $element['value'] ); ?>
	</div>
<?php endif; ?>
