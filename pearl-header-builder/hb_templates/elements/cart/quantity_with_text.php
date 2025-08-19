<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

$count = absint( WC()->cart->get_cart_contents_count() );
?>

<span class="cart__quantity-item">
	<?php
	printf(
		esc_html(
			// translators: %d: number of items in the cart.
			_n(
				'%d item in Cart',
				'%d items in Cart',
				$count,
				'pearl-header-builder'
			)
		),
		esc_html( $count )
	);
	?>
</span>
