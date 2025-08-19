<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( empty( $element['data']['id'] ) ) {
	$element['data']['id'] = 'primary';
}

if ( ! empty( $element['data']['id'] ) ) :


	if ( ! empty( $element['data']['innerColor'] ) || ! empty( $element['data']['innerHoverColor'] ) ) {
		$element_hash = sanitize_title( $element['$$hashKey'] );
		$style        = '';

		if ( ! empty( $element['data']['hoverColor'] ) ) {
			$text_color = $element['data']['hoverColor'];

			$style .= "body .stm-header__hb .stm-header__element.{$element_hash} .stm-navigation > ul > li:hover > a,
			 body .stm-header__hb .stm-header__element.{$element_hash} .stm-navigation > ul > li > a:hover {
                color: {$text_color} !important;
            }";

		}

		if ( ! empty( $element['data']['innerColor'] ) ) {
			$text_color = $element['data']['innerColor'];

			$style .= ".stm-header__hb .stm-header__element.{$element_hash} .stm-navigation > ul > li .sub-menu li a {
                color: {$text_color} !important;
            }"; ?>
			<?php
		}

		if ( ! empty( $element['data']['lineHeight'] ) ) {
			$line_height = $element['data']['lineHeight'];

			$style .= ".stm-header__hb .stm-header__element.{$element_hash} .stm-navigation > ul > li > a {
                line-height: {$line_height}px !important;
            }";
			?>
			<?php
		}


		if ( ! empty( $element['data']['innerHoverColor'] ) ) {
			$text_color = $element['data']['innerHoverColor'];

			$style .= ".stm-header__hb .stm-header__element.{$element_hash} .stm-navigation > ul > li .sub-menu li a:hover {
                color: {$text_color} !important;
            }";
		}

		if ( ! empty( $style ) ) {
			stm_hb_inline_style( $style, $element_hash );
		}
	}

	$classes      = array(
		'stm-navigation',
	);
	$style_string = array();

	$item_classes = array();

	$divider = '';
	$style   = 'default';
	$line    = 'none';
	$fwn     = '';

	if ( ! empty( $element['data'] ) ) {

		$data = $element['data'];

		/*Divider*/
		if ( ! empty( $data['divider'] ) ) {
			$divider = $data['divider'];
		}

		/*Style*/
		if ( ! empty( $data['style'] ) ) {
			$style = $data['style'];
			if ( 'fullwidth' === $data['style'] ) {
				$classes[] = 'stm_hb_tbc';
			}
		}

		/*Line*/
		if ( ! empty( $data['line'] ) ) {
			$line = $data['line'];
		}

		/*Font*/
		$font = 'hf';

		if ( ! empty( $data['font'] ) ) {
			$font = $data['font'];
		}

		if ( 'mf' === $font ) {
			$classes[] = 'main_font';
		} elseif ( 'hf' === $font ) {
			$classes[] = 'heading_font';
		}

		/*Font size*/
		if ( ! empty( $data['fsz'] ) ) {
			$classes[] = 'fsz_' . intval( $data['fsz'] );
		}

		/*Line height*/
		if ( ! empty( $data['lh'] ) ) {
			$style_string['line-height'] = intval( $data['lh'] ) . 'px';
		}

		/*FWN*/
		if ( ! empty( $data['fwn'] ) ) {
			$fwn = $data['fwn'];
		}
	}


	$classes[] = 'stm-navigation__default';
	$classes[] = 'stm-navigation__' . $style;
	$classes[] = 'stm-navigation__' . $line;
	$classes[] = 'stm-navigation__' . $fwn;

	if ( ! empty( $element['data'] ) ) {
		if ( ! empty( $element['data']['divider'] ) ) {
			$classes[] = 'stm-navigation__divider';

			if ( 'icon' === $element['data']['divider'] && ! empty( $element['data']['dividerIcon'] ) ) {
				$divider = "<i class='divider " . esc_attr( $element['data']['dividerIcon'] ) . "'></i>";
			}
			if ( 'symbol' === $element['data']['divider'] && ! empty( $element['data']['dividerSymbol'] ) ) {
				$divider = '<span class="divider">' . esc_html( $element['data']['dividerSymbol'] ) . '</span>';
			}
		}
	}

	$style_string = ! empty( $style_string ) ? stm_hb_array_to_style_string( $style_string ) : '';

	?>

	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
		<?php if ( $style_string ) : ?>
			style="<?php echo esc_attr( $style_string ); ?>"
		<?php endif; ?>
	>
		<?php if ( ! empty( $data ) && 'vertical_left' === $data['style'] ) : ?>
			<div class="stm_mobile__switcher stm_flex_last js_trigger__click" data-toggle="false">
				<span class="stm_hb_mbc"></span>
				<span class="stm_hb_mbc second"></span>
				<span class="stm_hb_mbc"></span>
			</div>
		<?php endif; ?>

		<ul 
		<?php if ( 'vertical_left' === $data['style'] ) : ?>
			class="stm-navigation__vertical"<?php endif; ?>>
			<?php

			$menu_args = array(
				'depth'        => 3,
				'container'    => false,
				'items_wrap'   => '%3$s',
				'link_after'   => $divider,
				'fallback_cb'  => false,
				'stm_megamenu' => true,
			);

			if ( ! empty( intval( $element['data']['id'] ) ) ) {
				if ( is_nav_menu( $element['data']['id'] ) ) {
					$menu_args['menu'] = intval( $element['data']['id'] );
				} else {
					$menu_args['theme_location'] = 'primary';
				}
			} else {
				$menu_args['theme_location'] = $element['data']['id'];
			}

			wp_nav_menu( $menu_args );
			?>
		</ul>


		<?php
		if ( in_array( 'stm-navigation__fullwidth', $classes, true ) ) {
			/* Icon box */
			if ( ! empty( $element['data'] ) ) {
				$icon_box = array(
					'data' => array(
						'icon'         => (string) ( $element['data']['icon'] ?? '' ),
						'title'        => (string) ( $element['data']['title'] ?? '' ),
						'description'  => (string) ( $element['data']['description'] ?? '' ),
						'iconBoxColor' => (string) ( $element['data']['iconBoxColor'] ?? '' ),
					),
				);

				stm_hb_load_element( 'iconbox', array( 'element' => $icon_box ) );
			}
		}
		?>

	</div>

<?php endif; ?>
