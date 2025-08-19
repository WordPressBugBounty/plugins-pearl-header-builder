<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Outputs js variables for angular.js
 */
function stm_output_vars() {
	global $wp_filesystem;

	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	$stm_hb_save_name     = stm_hb_save_name();
	$stored_theme_options = get_option( $stm_hb_save_name, array() );
	$theme_options        = stm_theme_options_array();

	$theme_options = stm_set_theme_options_pairs( $theme_options, $stored_theme_options );

	$delete_args = array();
	$default_hb  = (string) stm_hb_default_name();
	$current_hb  = (string) stm_hb_current_hb();

	$hb_param_raw = filter_input( INPUT_GET, 'hb', FILTER_UNSAFE_RAW );
	$hb_param     = is_string( $hb_param_raw ) ? sanitize_title( wp_unslash( $hb_param_raw ) ) : '';

	if ( '' !== $hb_param && $hb_param !== $default_hb ) {
		$delete_args = array(
			'page'                => 'stm_header_builder',
			'hb'                  => $hb_param,
			'delete_hb'           => true,
			'stm_hb_action_nonce' => wp_create_nonce( 'stm_hb_action_nonce' ),
		);

		/* translators: %s Header Name */
		$current_header = sprintf( __( 'Delete "%s"', 'pearl-header-builder' ), $current_hb );
	}

	$hbs = array(
		'construction'   => esc_html__( 'Construction', 'pearl-header-builder' ),
		'beauty'         => esc_html__( 'Beauty', 'pearl-header-builder' ),
		'transportation' => esc_html__( 'Transportation', 'pearl-header-builder' ),
		'medical'        => esc_html__( 'Medical', 'pearl-header-builder' ),
		'healthcoach'    => esc_html__( 'Healthcoach', 'pearl-header-builder' ),
		'music'          => esc_html__( 'Music', 'pearl-header-builder' ),
		'charity'        => esc_html__( 'Charity', 'pearl-header-builder' ),
		'rental'         => esc_html__( 'Rental', 'pearl-header-builder' ),
		'church'         => esc_html__( 'Church', 'pearl-header-builder' ),
		'viral'          => esc_html__( 'Viral', 'pearl-header-builder' ),
		'restaurant'     => esc_html__( 'Restaurant', 'pearl-header-builder' ),
		'personal_blog'  => esc_html__( 'Personal_blog', 'pearl-header-builder' ),
	);

	ob_start();
	?>
	<script type="text/javascript">
		var ngAppPath = "<?php echo esc_url( STM_HB_URL . 'includes/angular_app/' ); ?>";
		var ngAssets = "<?php echo esc_url( STM_HB_URL . 'assets/admin/assets/img' ); ?>";
		var ngImportHBs = <?php echo wp_json_encode( $hbs ); ?>;
		var ngAdminUrl = "<?php echo esc_url( admin_url() ); ?>";
		var ngThemePath = "<?php echo esc_url( get_template_directory_uri() . '/' ); ?>";
		<?php if ( ! empty( $delete_args ) ) : ?>
		var ngDeleteUrl = "<?php echo esc_url( add_query_arg( $delete_args, admin_url() ) ); ?>";
		var ngCurrentHb = '<?php echo wp_kses_post( $current_header ?? '' ); ?>';
		<?php endif; ?>
		var ngCurrentHbName = '<?php echo wp_kses_post( $current_hb ); ?>';
		var ngCurrentHeader = "<?php echo esc_html( stm_hb_save_name() ); ?>";
		var builderElements = <?php echo wp_json_encode( stm_builder_elements() ); ?>;
		var builderPathElement = <?php echo wp_json_encode( stm_builder_elements() ); ?>;
		var ngDefaultOptions = <?php echo wp_json_encode( $theme_options ); ?>;
		var ngGoogleFonts = <?php echo wp_json_encode( stm_hb_google_fonts_array() ); ?>;
		var stmMenus = <?php echo wp_json_encode( stm_get_menus() ); ?>;
		var stmPages = <?php echo wp_json_encode( stm_get_pages() ); ?>;
		var themePath = <?php echo wp_json_encode( get_template_directory() ); ?>;
		<?php stm_icons_set(); ?>
	</script>
	<?php

	$inline_js = str_replace(
		array( '<script type="text/javascript">', '<script>', '</script>' ),
		'',
		ob_get_clean()
	);

	wp_add_inline_script( 'pearl_theme_options_app', $inline_js, 'before' );
}

/**
 * @param $to : theme options array
 * @param $sto :stored theme options array
 * @return mixed
 */
function stm_set_theme_options_pairs( $to, $sto ) {
	foreach ( $to as $mt_key => $mt ) {
		foreach ( $mt['options'] as $st_key => $st ) {
			foreach ( $st['options'] as $ctrl_key => $ctrl ) {
				$to[ $mt_key ]['options'][ $st_key ]['options'][ $ctrl_key ] = stm_parse_control( $ctrl );
				if ( ! empty( $sto[ $ctrl_key ] ) ) {
					$to[ $mt_key ]['options'][ $st_key ]['options'][ $ctrl_key ]['data']['value'] = $sto[ $ctrl_key ];
				} else {
					if ( isset( $ctrl['data']['value'] ) ) {
						$to[ $mt_key ]['options'][ $st_key ]['options'][ $ctrl_key ]['data']['value'] = $ctrl['data']['value'];
					}
				}
			}
		}
	}

	return $to;
}

/**
 * @param $to :theme options array
 * @return array
 */
function stm_get_theme_options_pairs( $to ) {
	$sto   = array();
	$strip = array(
		'copyright',
		'right_text',
	);

	foreach ( $to as $mt_key => $mt ) {
		if ( ! empty( $mt['options'] ) ) {
			foreach ( $mt['options'] as $st_key => $st ) {
				foreach ( $st['options'] as $ctrl_key => $ctrl ) {
					$value            = ( in_array( $ctrl_key, $strip, true ) )
						? stripslashes( $ctrl['data']['value'] )
						: $ctrl['data']['value'];
					$sto[ $ctrl_key ] = $value;
				}
			}
		}
	}

	return $sto;
}

/**
 * @param $to :theme options array
 * @return bool
 */
function stm_update_theme_options( $to ) {
	$theme_options = stm_get_theme_options_pairs( $to );

	delete_transient( 'stm_custom_styles' );

	$stm_hb_save_name = stm_hb_save_name();

	return update_option( $stm_hb_save_name, $theme_options );
}

/**
 * Reset Theme options to defaults
 */
function stm_set_default_to() {
	$default = stm_theme_options_array();

	stm_update_theme_options( $default );
}

/**
 * Hook into theme options and replace any control type with something else
 *
 * @param $control
 * @return mixed|void
 */
function stm_parse_control( $control ) {
	$control_type = $control['type'];
	if ( 'select' === $control_type && ! empty( $control['data']['post_type'] ) ) {
		$post_type = $control['data']['post_type'];
		$choices   = array( 'false' => esc_html__( 'None', 'pearl-header-builder' ) );

		$wp_qargs = array(
			'post_type'      => sanitize_text_field( $post_type ),
			'posts_per_page' => '-1',
			'post_status'    => 'publish',
		);

		$q = new WP_Query( $wp_qargs );

		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();
				$choices[ get_the_ID() ] = get_the_title();
			}
		}

		if ( ! empty( $control['data']['choices'] ) ) {
			$choices = $control['data']['choices'] + $choices;
		};

		$control['data']['choices'] = $choices;
	} elseif ( 'font' === $control_type && function_exists( 'pearl_get_fw' ) ) {
		/*Font weight*/
		$fw                    = pearl_get_fw();
		$control['data']['fw'] = $fw;
	}

	/*Get Export options*/
	if ( ! empty( $control['source'] ) && 'theme_options' === $control['source'] ) {
		$stm_hb_save_name         = stm_hb_save_name();
		$control['data']['value'] = wp_json_encode( get_option( $stm_hb_save_name ) );
	}

	return apply_filters( 'stm_parse_control', $control );
}

function stm_icons_set() {
	global $wp_filesystem;

	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	$icons = array();

	/*Fontawesome*/
	$fa     = stm_fontawesome_list();
	$fa_tmp = array();
	foreach ( $fa as $key => $value ) {
		$fa_tmp[] = $key;
	}
	$icons['FontAwesome'] = $fa_tmp;

	$custom_fonts = get_option( 'stm_fonts' );
	$wp_uploads   = wp_upload_dir();
	$base_url     = $wp_uploads['baseurl'];

	if ( ! empty( $custom_fonts ) ) {
		foreach ( $custom_fonts as $custom_font ) {
			$json_file         = $base_url . '/' . $custom_font['folder'] . '/selection.json';
			$custom_icons_json = json_decode( $wp_filesystem->get_contents( $json_file ), true );
			$custom_icons      = array();

			if ( ! empty( $custom_icons_json ) ) {
				$set_name   = $custom_icons_json['metadata']['name'];
				$set_prefix = $custom_icons_json['preferences']['fontPref']['prefix'];
				foreach ( $custom_icons_json['icons'] as $icon ) {
					$custom_icons[] = $set_prefix . $icon['properties']['name'];
				}

				if ( ! empty( $custom_icons ) ) {
					$icons[ $set_name ] = $custom_icons;
				}
			}
		}
	}

	echo 'var stm_icons = ' . wp_json_encode( apply_filters( 'stm_hb_icons_set', $icons ) ) . ';';
}

function stm_save_hb_settings(): void {
	if ( ! is_user_logged_in() || ! current_user_can( 'edit_theme_options' ) ) {
		wp_send_json_error(
			array( 'message' => __( 'You are not allowed to perform this action.', 'pearl-header-builder' ) ),
			403
		);
	}

	check_ajax_referer( 'admin_ajax_nonce', 'nonce' );

	$raw = filter_input_array( INPUT_POST, FILTER_UNSAFE_RAW );

	if ( empty( $raw ) ) {
		wp_send_json_error(
			array( 'message' => __( 'No data received.', 'pearl-header-builder' ) ),
			400
		);
	}

	unset( $raw['action'], $raw['nonce'] );

	$payload = map_deep(
		$raw,
		static function ( $v ) {
			return is_string( $v ) ? sanitize_text_field( $v ) : $v;
		}
	);

	$updated = stm_update_theme_options( $payload );

	if ( $updated ) {
		wp_send_json_success(
			array( 'message' => __( 'Settings saved.', 'pearl-header-builder' ) )
		);
	}

	wp_send_json_success(
		array( 'message' => __( 'Nothing to save.', 'pearl-header-builder' ) )
	);
}
add_action( 'wp_ajax_stm_hb_save_settings', 'stm_save_hb_settings' );

function stm_hb_add_new() {
	$hb_action = isset( $_GET['hb_action'] ) ? sanitize_key( wp_unslash( $_GET['hb_action'] ) ) : '';
	if ( '' === $hb_action ) {
		return;
	}

	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$nonce = isset( $_GET['stm_hb_add_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['stm_hb_add_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'stm_hb_add_nonce' ) ) {
		wp_die( esc_html__( 'Invalid nonce. Action not allowed.', 'pearl-header-builder' ) );
	}

	$hb_raw    = isset( $_GET['hb'] ) ? sanitize_text_field( wp_unslash( $_GET['hb'] ) ) : '';
	$delete_hb = isset( $_GET['delete_hb'] ) ? sanitize_text_field( wp_unslash( $_GET['delete_hb'] ) ) : '';

	if ( '' !== $hb_raw && '' === $delete_hb ) {
		$new_hb_name = sanitize_text_field( $hb_raw );
		$new_hb_slug = sanitize_title( $hb_raw );

		$variants      = stm_get_hb_variants();
		$variants_name = stm_hb_variants_name();

		if ( ! isset( $variants[ $new_hb_slug ] ) ) {
			$variants[ $new_hb_slug ] = $new_hb_name;
		}

		update_option( $variants_name, $variants );
	}
}
add_action( 'admin_init', 'stm_hb_add_new' );

function stm_hb_delete() {
	$delete_flag = isset( $_GET['delete_hb'] ) ? sanitize_text_field( wp_unslash( $_GET['delete_hb'] ) ) : '';
	if ( '' === $delete_flag ) {
		return;
	}

	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$nonce = isset( $_GET['stm_hb_action_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['stm_hb_action_nonce'] ) ) : '';
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'stm_hb_action_nonce' ) ) {
		wp_die( esc_html__( 'Invalid nonce. Action not allowed.', 'pearl-header-builder' ) );
	}

	$hb_name_raw  = (string) stm_hb_save_name();
	$default_name = (string) stm_hb_default_name();

	$hb_name = sanitize_key( $hb_name_raw );

	if ( '' === $hb_name || sanitize_key( $default_name ) === $hb_name ) {
		return;
	}

	$variants = stm_get_hb_variants();
	if ( ! is_array( $variants ) ) {
		$variants = array();
	}

	$variants_name = stm_hb_variants_name();

	if ( array_key_exists( $hb_name, $variants ) ) {
		unset( $variants[ $hb_name ] );
		update_option( $variants_name, $variants );
	}

	delete_option( $hb_name );
}
add_action( 'admin_init', 'stm_hb_delete', 0 );

/**
 * Get default Header Builder option name or label.
 *
 * @param bool $return_key
 * @return string|array
 */
function stm_hb_default_name( bool $return_key = true ) {
	$option_key   = 'stm_hb_settings';
	$option_label = esc_html__( 'Default Header', 'pearl-header-builder' );

	if ( $return_key ) {
		return $option_key;
	}

	return array( $option_key => $option_label );
}

function stm_hb_save_prefix() {
	return 'stm_hb_';
}

/**
 * Resolve current Header Builder option key (slug) to save/use.
 *
 * @return string
 */
function stm_hb_save_name(): string {
	$default_key = (string) stm_hb_default_name();

	$hb_get_raw = filter_input( INPUT_GET, 'hb', FILTER_UNSAFE_RAW );
	if ( is_string( $hb_get_raw ) && '' !== $hb_get_raw ) {
		return sanitize_title( wp_unslash( $hb_get_raw ) );
	}

	$hb_post_raw = filter_input( INPUT_POST, 'hb', FILTER_UNSAFE_RAW );
	if ( is_string( $hb_post_raw ) && '' !== $hb_post_raw ) {
		return sanitize_title( wp_unslash( $hb_post_raw ) );
	}

	return $default_key;
}

function stm_hb_current_hb( $slug = '' ) {
	if ( empty( $slug ) ) {
		$slug = stm_hb_save_name();
	}

	$variants = stm_get_hb_variants();

	return $variants[ $slug ] ?? '';
}

function stm_hb_variants_name() {
	return 'stm_hb_variants';
}

function stm_hb_variants() {
	include STM_HB_DIR . 'includes/views/hb_variants.php';
}

function stm_get_hb_variants() {
	$hb = stm_hb_default_name( false );

	$variants_name = stm_hb_variants_name();
	$variants      = get_option( $variants_name, array() );

	return array_merge( $hb, $variants );
}

function stm_hb_get_thumbnail() {
	check_ajax_referer( 'admin_ajax_nonce', 'nonce' );

	$url = '';
	if ( ! empty( $_GET ) && ! empty( $_GET['image_id'] ) ) {
		$id  = intval( $_GET['image_id'] );
		$url = wp_get_attachment_image_url( $id );
	}

	echo esc_url( $url );

	exit;
}
add_action( 'wp_ajax_stm_hb_get_thumbnail', 'stm_hb_get_thumbnail' );

function stm_hb_update_custom_styles_admin() {
	check_ajax_referer( 'admin_ajax_nonce', 'nonce' );

	delete_transient( 'stm_custom_styles' );
}
add_action( 'wp_ajax_stm_hb_update_custom_styles_admin', 'stm_hb_update_custom_styles_admin' );

function stm_hb_export_header() {
	check_ajax_referer( 'admin_ajax_nonce', 'nonce' );

	$layout_name = isset( $_GET['layout_name'] ) ? sanitize_text_field( wp_unslash( $_GET['layout_name'] ) ) : '';
	$layout_slug = isset( $_GET['layout'] ) ? sanitize_title( wp_unslash( $_GET['layout'] ) ) : '';

	$hb                = get_option( $layout_slug, array() );
	$hb['stm_hb_slug'] = $layout_slug;
	$hb['stm_hb_name'] = $layout_name;

	header( 'Content-disposition: attachment; filename=' . $layout_slug . '.json' );
	header( 'Content-type: application/json' );
	echo wp_json_encode( $hb );
	exit();
}
add_action( 'wp_ajax_stm_hb_export_header', 'stm_hb_export_header' );

function stm_hb_import_header() {
	check_ajax_referer( 'admin_ajax_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		die;
	}

	$error = false;

	$r = array();

	$layout_slug = isset( $_GET['layout'] ) ? sanitize_title( wp_unslash( $_GET['layout'] ) ) : '';
	$json_file   = STM_HB_DIR . 'includes/import/' . $layout_slug . '.json';

	global $wp_filesystem;

	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	if ( file_exists( $json_file ) ) {
		$json_file   = json_decode( $wp_filesystem->get_contents( $json_file ), true );
		$layout_name = $json_file['stm_hb_name'];

		$option_exists = get_option( $layout_slug, '' );

		$stm_hb_variants_name = stm_hb_variants_name();
		$stm_hb_variants      = get_option( $stm_hb_variants_name, array() );

		/*If header already exists*/
		if ( ! empty( $stm_hb_variants[ $layout_slug ] ) ) {
			$r['status']  = 'error';
			$r['message'] = esc_html__( 'Header with this name already exists', 'pearl-header-builder' );
			wp_send_json( $r );
			exit;
		} else {
			$stm_hb_variants[ $layout_slug ] = $layout_name;
			update_option( $stm_hb_variants_name, $stm_hb_variants );
		}

		/*If option with this name already exists*/
		if ( empty( $option_exists ) ) {
			update_option( $layout_slug, $json_file );
			$args = array(
				'page' => 'stm_header_builder',
				'hb'   => $layout_slug,
			);

			$r['message'] = esc_html__( 'Header imported, reloading page', 'pearl-header-builder' );
			$r['url']     = add_query_arg( $args, admin_url() );
			$r['status']  = 'success';

		} else {
			$r['status']  = 'error';
			$r['message'] = esc_html__( 'Option with this name already exists', 'pearl-header-builder' );
		}
	}

	wp_send_json( $r );

	wp_die();
}
add_action( 'wp_ajax_stm_hb_import_header', 'stm_hb_import_header' );


function stm_hb_import_header_file() {
	check_ajax_referer( 'admin_ajax_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error(
			array( 'message' => esc_html__( 'You are not allowed to perform this action.', 'pearl-header-builder' ) ),
			403
		);
	}

	if ( ! isset( $_POST['layout'] ) ) {
		wp_send_json_error(
			array( 'message' => esc_html__( 'Missing layout parameter.', 'pearl-header-builder' ) ),
			400
		);
	}

	$layout_key = sanitize_key( wp_unslash( $_POST['layout'] ) );
	if ( '' === $layout_key ) {
		wp_send_json_error(
			array( 'message' => esc_html__( 'Invalid layout key.', 'pearl-header-builder' ) ),
			400
		);
	}

	if ( ! isset( $_FILES['file'] ) || ! is_array( $_FILES['file'] ) ) {
		wp_send_json_error(
			array( 'message' => esc_html__( 'No file uploaded.', 'pearl-header-builder' ) ),
			400
		);
	}

	$tmp_name = '';
	if ( isset( $_FILES['file']['tmp_name'] ) ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$candidate = $_FILES['file']['tmp_name'];

		if ( is_string( $candidate ) && is_uploaded_file( $candidate ) ) {
			$tmp_name = $candidate;
		}
	}

	$file = isset( $_FILES['file'] ) ? array(
		'name'     => isset( $_FILES['file']['name'] ) ? sanitize_file_name( $_FILES['file']['name'] ) : '',
		'type'     => isset( $_FILES['file']['type'] ) ? sanitize_mime_type( $_FILES['file']['type'] ) : '',
		'tmp_name' => $tmp_name,
		'error'    => isset( $_FILES['file']['error'] ) ? absint( $_FILES['file']['error'] ) : '',
		'size'     => isset( $_FILES['file']['size'] ) ? absint( $_FILES['file']['size'] ) : '',
	) : null;

	if ( ! empty( $file['error'] ) ) {
		$error_code = (int) $file['error'];
		$message    = esc_html__( 'Upload failed.', 'pearl-header-builder' );

		switch ( $error_code ) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$message = esc_html__( 'The uploaded file exceeds the maximum allowed size.', 'pearl-header-builder' );
				break;
			case UPLOAD_ERR_PARTIAL:
				$message = esc_html__( 'The uploaded file was only partially uploaded.', 'pearl-header-builder' );
				break;
			case UPLOAD_ERR_NO_FILE:
				$message = esc_html__( 'No file was uploaded.', 'pearl-header-builder' );
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$message = esc_html__( 'Missing a temporary folder on the server.', 'pearl-header-builder' );
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$message = esc_html__( 'Failed to write file to disk.', 'pearl-header-builder' );
				break;
			case UPLOAD_ERR_EXTENSION:
				$message = esc_html__( 'A PHP extension stopped the file upload.', 'pearl-header-builder' );
				break;
		}

		wp_send_json_error( array( 'message' => $message ), 400 );
	}

	$original_name = isset( $file['name'] ) ? (string) $file['name'] : '';
	$tmp_name      = isset( $file['tmp_name'] ) ? (string) $file['tmp_name'] : '';

	if ( '' === $original_name || '' === $tmp_name || ! is_readable( $tmp_name ) ) {
		wp_send_json_error(
			array( 'message' => esc_html__( 'Invalid uploaded file.', 'pearl-header-builder' ) ),
			400
		);
	}

	$checked = wp_check_filetype_and_ext( $tmp_name, $original_name );

	$ext_from_name  = strtolower( (string) pathinfo( $original_name, PATHINFO_EXTENSION ) );
	$ext_from_check = isset( $checked['ext'] ) ? strtolower( (string) $checked['ext'] ) : '';

	$ext_ok = ( 'json' === $ext_from_name ) || ( 'json' === $ext_from_check );

	if ( ! $ext_ok ) {
		wp_send_json_error(
			array( 'message' => esc_html__( 'Please, upload a valid JSON file.', 'pearl-header-builder' ) ),
			400
		);
	}

	$data = wp_json_file_decode(
		$tmp_name,
		array(
			'associative' => true,
			'depth'       => 512,
		)
	);

	if ( is_wp_error( $data ) || ! is_array( $data ) ) {
		wp_send_json_error(
			array( 'message' => esc_html__( 'Uploaded JSON is invalid or malformed.', 'pearl-header-builder' ) ),
			400
		);
	}

	$updated = update_option( $layout_key, $data, false );

	if ( ! $updated ) {
		wp_send_json_success(
			array(
				'message' => esc_html__( 'Layout imported (no changes detected).', 'pearl-header-builder' ),
				'updated' => false,
				'option'  => $layout_key,
			)
		);
	}

	wp_send_json_success(
		array(
			'message' => esc_html__( 'Layout imported successfully.', 'pearl-header-builder' ),
			'updated' => true,
			'option'  => $layout_key,
		)
	);
}
add_action( 'wp_ajax_stm_hb_import_header_file', 'stm_hb_import_header_file' );
