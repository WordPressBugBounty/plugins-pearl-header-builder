<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! empty( $page_id ) ) :
	$_page   = get_post( $page_id );
	$content = $_page->post_content;
	?>

	<div class="modal" id="headerModal<?php echo esc_attr( $page_id ); ?>" tabindex="-1" role="dialog" aria-labelledby="headerModal<?php echo esc_attr( $page_id ); ?>">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-body heading_font">
					<div class="close_popup" data-dismiss="modal"><i class="stmicon-cross2"></i></div>
					<h4><?php echo esc_html( get_the_title( $page_id ) ); ?></h4>
					<div class="stm-content">
						<?php echo do_shortcode( $content ); ?>
					</div>
				</div>

			</div>
		</div>
	</div>

<?php endif; ?>
