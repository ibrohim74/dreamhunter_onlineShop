<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images/';
?>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id);?>">
    <p><?php _e("The company name and the address details from this section will be used as the sender address in the invoice and other related documents.","print-invoices-packing-slip-labels-for-woocommerce");?></p>
    <form method="post" class="wf_settings_form wf_settings_form_wizard">
        <input type="hidden" value="main" class="wf_settings_base" />
        <input type="hidden" value="wf_save_settings" class="wf_settings_action" />
        <?php
        // Set nonce:
        if (function_exists('wp_nonce_field'))
        {
            wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
        }
        ?>
        <div id="wfte_general_step_1" class="wfte_fw_stpes">
            <?php include $wf_admin_view_path.'general_settings_step1.php'; ?>
        </div>
        <div id="wfte_general_step_2" class="wfte_fw_stpes">
            <?php include $wf_admin_view_path.'general_settings_step2.php'; ?>
        </div>
        <div id="wfte_general_step_3" class="wfte_fw_stpes">
            <?php include $wf_admin_view_path.'general_settings_step3.php'; ?>
        </div>
    </form>
</div>