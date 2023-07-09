<?php

defined( 'ABSPATH' ) || die( 'Cheatin’ uh?' );

/**
 * @var array                           $data
 * @var WRIO_Page $page
 */
?>
<div class="wrio-servers">
    <div>
        <label for="wrio-change-optimization-server">
			<?php _e( 'Select optimization server:', 'robin-image-optimizer' ); ?>
            <span><?php _e( 'Please, find the list of available servers for image optimization below. If the server has a state “Down”, it means that the server is not available, and you should choose another one. “Stable” means that the server is available and you can use it.', 'robin-image-optimizer' ); ?></span>
        </label>
		<?php
		$server = WRIO_Plugin::app()->getPopulateOption( 'image_optimization_server', 'server_1' );
		?>
        <select id="wrio-change-optimization-server" class="factory-dropdown factory-from-control-dropdown form-control">
            <option value="server_1" <?php selected( $server, 'server_1' ); ?>>
				<?php echo __( 'Server 1 - image size limit up to 5 MB', 'robin-image-optimizer' ); ?>
            </option>
            <option value="server_2" <?php selected( $server, 'server_2' ); ?>>
				<?php echo __( 'Server 2 - beta', 'robin-image-optimizer' ); ?>
            </option>
            <option value="server_5" <?php selected( $server, 'server_5' ); ?>>
		        <?php echo __( 'Premium - no limits', 'robin-image-optimizer' ); ?>
            </option>
        </select>
        <div class="wrio-server-status-wrap">
            <span><strong><?php _e( 'Status', 'robin-image-optimizer' ) ?>:</strong></span>
            <span class="wrio-server-status wrio-server-check-proccess"> </span>
        </div>
        <div class="wrio-premium-user-balance-wrap" style="<?php echo $server === 'server_1' ? 'display:none;' : '' ?>">
            <span><strong><?php _e( 'Credits', 'robin-image-optimizer' ) ?>:</strong></span>
            <span class="wrio-premium-user-balance wrio-premium-user-balance-check-proccess"
                  data-server="<?= $server; ?>" data-toggle="tooltip"
                  title="<?= __( 'The all images are limited, including thumbnails', 'robin-image-optimizer' ); ?>"> </span>
        </div>
        <div class="wrio-premium-user-update-wrap"
             style="<?php echo in_array( $server, [ 'server_1', 'server_2' ] ) ? 'display:none;' : '' ?>">
            <span><strong><?php _e( 'Next credits update', 'robin-image-optimizer' ) ?>:</strong></span>
            <span class="wrio-premium-user-update wrio-premium-user-update-check-proccess" data-server="<?= $server ?>"
                  data-toggle="tooltip"
                  title="<?= __( 'Date when the limit is topped up', 'robin-image-optimizer' ) ?>"></span>
        </div>
    </div>
</div>
