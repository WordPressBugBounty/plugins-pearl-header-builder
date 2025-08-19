<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! empty( $element['data'] ) ) :

	$btn_c = array( 'btn btn_primary btn_solid stm_hb_mbc stm_hb_sbc_h' );

	$icon = '';
	$text = '';
	if ( ! empty( $element['data']['icon'] ) ) {
		$icon    = $element['data']['icon'];
		$btn_c[] = 'stm-button_icon';
	}

	$text = ( ! empty( $element['data']['text'] ) ) ? $element['data']['text'] : '';

	$target = 'headerModal' . $element['value'];
	?>

	<a href="#"
		data-toggle="modal"
		data-target="#<?php echo esc_attr( $target ); ?>"
		class="stm-header-popup__button <?php echo esc_attr( implode( ' ', $btn_c ) ); ?>">
		<i class="stm-button__icon <?php echo esc_attr( $icon ); ?>"></i>
		<span class="stm-button__text"><?php echo esc_html( $text ); ?></span>
	</a>


	<!--Popup itself-->
	<?php
	if ( ! empty( $element['value'] ) ) :
		stm_hb_load_element( 'popup', array( 'page_id' => $element['value'] ), 'modal' );
		wp_add_inline_script(
			'stm_hb_scripts',
			"(function(){
					var id = 'headerModal" . esc_js( $element['value'] ) . "';
					function movePopup(){
						var popup = document.getElementById(id);
						if (popup && document.body) {
							document.body.appendChild(popup);
						}
					}
					if (document.readyState === 'loading') {
						document.addEventListener('DOMContentLoaded', movePopup);
					} else {
						movePopup();
					}
				})();"
		);
	endif;

endif;
