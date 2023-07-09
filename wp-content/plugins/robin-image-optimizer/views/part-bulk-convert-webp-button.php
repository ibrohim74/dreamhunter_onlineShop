<?php

defined( 'ABSPATH' ) || die( 'Cheatin’ uh?' );

/**
 * @var array $data
 * @var WRIO_Page $page
 */

$cron_running = WRIO_Plugin::app()->getPopulateOption( 'conversion_cron_running', false );
$process_running = WRIO_Plugin::app()->getPopulateOption( $data['scope'] . '_webp_process_running', false );

if ( ! $cron_running || $cron_running != $data['scope'] ) {
	$cron_running = false;
}

if ( ! $process_running || $process_running != $data['scope'] . '_webp' ) {
	$process_running = false;
}

$button_classes = [
	'wio-optimize-button'
];

$button_name = __( 'Convert to WebP', 'robin-image-optimizer' );

if ( $cron_running || $process_running ) {
	$button_classes[] = 'wrio-cron-mode wio-running';
	$button_name      = $process_running ? __( 'Stop conversation', 'robin-image-optimizer' ) : __( 'Stop schedule conversation', 'robin-image-optimizer' );
} else {
	$button_name = __( 'Convert to WebP', 'robin-image-optimizer' );
}

?>
<button type="button" id="wrio-start-conversion" class="<?php echo join( ' ', $button_classes ); ?>">
	<?php echo esc_attr( $button_name ); ?>
</button>