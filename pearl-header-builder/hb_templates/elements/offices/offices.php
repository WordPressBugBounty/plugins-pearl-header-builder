<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( empty( $element ) ) {
	return;
}

if ( ! empty( $element['data'] ) && ! empty( $element['data']['offices'] ) ) :
	$offices = $element['data']['offices'];

	$icon_color = '';
	$text_color = '';

	if ( ! empty( $element['data']['iconColor'] ) ) {
		$icon_color = 'stm_hb_' . $element['data']['iconColor']['value'];
	}

	if ( ! empty( $element['data']['textColor'] ) ) {
		$text_color = 'stm_hb_' . $element['data']['textColor']['value'];
	}
	?>


	<div class="stm-offices">
		<div class="stm-switcher">
			<div
				class="stm-switcher__trigger stm-switcher__trigger_default stm-switcher__trigger_uppercase stm_hb_mbc">
				<?php if ( ! empty( $element['data']['icon'] ) ) : ?>
					<i class="stm-switcher__icon <?php echo esc_attr( $element['data']['icon'] ); ?>"></i>
				<?php endif; ?>
				<span class="stm-switcher__text"><?php echo esc_html( $offices[0]['name'] ); ?></span>
			</div>
			<div class="stm-switcher__list stm_hb_tbc">
				<?php foreach ( $offices as $key => $office ) : ?>
					<div class="stm-switcher__option stm_hb_mbc_h" data-switch="<?php echo intval( $key ); ?>">
						<?php echo esc_html( $office['name'] ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php
		foreach ( $offices as $key => $office ) :
			$hidden = ( 0 === $key ) ? 'active' : 'hidden';
			?>
			<div class="stm-icontext js-switcher js-switcher__<?php echo esc_attr( $hidden ); ?> js-switcher_<?php echo intval( $key ); ?>">
			<?php if ( ! empty( $office['info'] ) ) : ?>
				<?php
				foreach ( $office['info'] as $k => $info ) :
					$_tag = 'div';
					$url  = '';
					if ( ! empty( $info['url'] ) ) {
						$_tag = 'a';
						$url  = ' href="' . esc_url( $info['url'] ) . '"';
					}

					?>
					<<?php echo esc_attr( $_tag . $url ); ?> class="stm-icontext__info  stm-icontext__info_divider no_deco">
						<?php if ( ! empty( $info['icon'] ) ) : ?>
							<i class="stm-icontext__icon 
							<?php
							echo esc_attr( $info['icon'] ) . ' ';
							echo esc_attr( $icon_color );
							?>
							"></i>
						<?php endif; ?>
						<?php if ( ! empty( $info['label'] ) ) : ?>
							<span class="stm-icontext__text <?php echo esc_attr( $text_color ); ?>">
								<?php echo esc_html( $info['label'] ); ?>
							</span>
						<?php endif; ?>
					</<?php echo esc_attr( $_tag ); ?>>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

<?php endif; ?>
