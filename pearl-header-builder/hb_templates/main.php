<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

$hb       = $this->header_name;
$elements = stm_hb_get_option( 'header_builder', false, $hb );

$parts = stm_hb_parts();
$rows  = $parts['rows'];
$cells = $parts['cells'];

$row_wrapper  = 'stm-header__row_color stm-header__row_color_';
$row_base     = 'stm-header__row stm-header__row_';
$cell_base    = 'stm-header__cell stm-header__cell_';
$element_base = 'stm-header__element';

ob_start();
?>

<script type="text/javascript">
	var stm_sticky = '<?php echo esc_js( stm_hb_get_option( 'header_sticky', '', 'stm_hb_settings' ) ); ?>';
</script>

<?php
$inline_js = str_replace(
	array( '<script type="text/javascript">', '<script>', '</script>' ),
	'',
	ob_get_clean()
);

wp_add_inline_script( 'stm_hb_scripts', $inline_js, 'before' );
?>

<div class="stm-header stm-header__hb" id="stm_<?php echo esc_attr( $hb ); ?>">
	<?php foreach ( $rows as $row ) : ?>
		<?php
		if ( ! empty( $elements[ $row ] ) ) :
			$count_elements_in_row = 0;
			foreach ( $elements[ $row ] as $element_num ) {
				$count_elements_in_row++;
			}
			?>

			<div class="<?php echo esc_attr( apply_filters( 'stm_hb_row_class', esc_attr( $row_wrapper . $row ), $row ) ); ?> elements_in_row_<?php echo esc_attr( $count_elements_in_row ); ?>">
				<div class="container">
					<div class="<?php echo esc_attr( $row_base . $row ); ?>">
						<?php
						foreach ( $cells as $cell ) :
							if ( ! empty( $elements[ $row ][ $cell ] ) ) :
								?>
								<div class="<?php echo esc_attr( $cell_base . $cell ); ?>">
									<?php
									foreach ( $elements[ $row ][ $cell ] as $key => $element ) :
										$custom_css  = sanitize_title( $element['$$hashKey'] );
										$custom_css .= stm_hb_element_style( $element );

										$_type = ! empty( $element['view_template'] ) ? $element['view_template'] : $element['type'];

										$element['pearl_header_builder'] = $hb;
										?>
										<div class="<?php echo esc_attr( $element_base . ' ' . $custom_css ); ?>">
											<?php stm_hb_load_element( $_type, array( 'element' => $element ) ); ?>
										</div>
									<?php endforeach; ?>
								</div>
								<?php
							endif;
						endforeach;
						?>
					</div>
				</div>
			</div>
			<?php
		endif;
	endforeach;
	?>
</div>

<?php
/* Mobile Page */
stm_hb_load_element( 'mobile_header', array( 'hb' => $hb ), 'mobile', 'mobile' ); ?>
