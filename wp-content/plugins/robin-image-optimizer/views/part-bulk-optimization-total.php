<?php

defined( 'ABSPATH' ) || die( 'Cheatin’ uh?' );

/**
 * @var array $data
 * @var WRIO_Page $page
 */
?>
<div class="wio-columns wio-page-total">
    <strong><?php _e( 'Total number of images to optimize', 'robin-image-optimizer' ); ?></strong>
    <span class="wio-num" id="wio-total-num" data-toggle="tooltip"
          title="<?= __( 'The total number of images in the media library of this site, including thumbnails that are selected in the plugin settings', 'robin-image-optimizer' ) ?>"></span>
</div>