<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( empty( $element ) ) {
	return;
}

if ( 'wpml' === $element['value'] ) {
	$element['dropdown'] = pearl_get_wpml_langs();
}

$element_id = stm_hb_random();

if ( ! empty( $element['dropdown'] ) ) {
	$dropdown = stm_hb_get_dropdown( $element['dropdown'] );
}

if ( ! empty( $dropdown ) ) : ?>
	<div class="dropdown">
		<?php if ( ! empty( $dropdown['first'] ) ) : ?>
			<div class="dropdown-toggle" id="<?php echo esc_attr( $element_id ); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<?php echo esc_html( $dropdown['first']['label'] ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $dropdown['others'] ) ) : ?>
			<ul class="dropdown-list stm_hb_tbc" aria-labelledby="<?php echo esc_attr( $element_id ); ?>">
				<?php foreach ( $dropdown['others'] as $key => $value ) : ?>
					<li>
						<a href="<?php echo esc_url( $value['url'] ); ?>" class="stm-switcher__option">
							<?php echo esc_html( $value['label'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
<?php endif; ?>
