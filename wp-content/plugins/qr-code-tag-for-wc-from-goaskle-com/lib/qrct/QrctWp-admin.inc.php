<?php

    if (!is_admin()) {
        die('hacker, eh?');
    } 
    

    if (isset($options['shortcode']['main_color']) && $options['shortcode']['main_color']){
    	$qr_main_color = $options['shortcode']['main_color'];
    } else {
    	$qr_main_color = '';
    }

    if (isset($options['shortcode']['dots_color']) && $options['shortcode']['dots_color']){
    	$qr_dots_color = $options['shortcode']['dots_color'];
    } else {
    	$qr_dots_color = '';
    }    

    //print_r(trim($options['shortcode']['disable_all_wc_emails_except_standard']));
    if (isset($options['shortcode']['hash_order_wc'])){
    	$hash_order_wc = $options['shortcode']['hash_order_wc'];
    } else {
    	$hash_order_wc = 0;
    }
    
    if (isset($options['shortcode']['disable_all_wc_emails_except_standard'])){
    	$disable_all_wc_emails_except_standard  = $options['shortcode']['disable_all_wc_emails_except_standard'];
    } else {
    	$disable_all_wc_emails_except_standard  = 0;
    }

    if (isset($options['shortcode']['disable_auto_wc'])){
    	$disable_auto_wc = $options['shortcode']['disable_auto_wc'];
    } else {
    	$disable_auto_wc = 0;
    }        
    
    if (isset($options['shortcode']['disable_pages_auto_wc'])){
    	$disable_pages_auto_wc = $options['shortcode']['disable_pages_auto_wc'];
    } else {
    	$disable_pages_auto_wc = 0;
    }

    if (isset($options['shortcode']['wc_data_template'])){
    	$wc_data_template = $options['shortcode']['wc_data_template'];
    } else {
    	$wc_data_template = '';
    }

?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2><?php echo __('QR Code Tag Settings', 'qr-code-tag-for-wc-from-goaskle-com'); ?></h2>
       
    <form method="post" name="options" target="_self">
       <h3><?php echo __('Code Generation', 'qr-code-tag-for-wc-from-goaskle-com'); ?></h3>
       <style>
           body .hide_it_goaskle{
                display: none;
            }
            body .show_it_goaskle{
                display: table-row;
            }
            body .goaskle_qr_code_settings .button.wp-color-result,body .goaskle_qr_code_settings .wp-picker-container{
                width: 100%;
            }
       </style>
       <script>
       jQuery( document ).ready(function() {
               jQuery( 'input[name=qrct_from_goaskle_com_sc_main_color]' ).wpColorPicker();
               jQuery( 'input[name=qrct_from_goaskle_com_sc_dots_color]' ).wpColorPicker();               

           jQuery('input[name=qrct_from_goaskle_com_sc_disable_all_wc_emails_except_standard]').on('change', function(){
               //console.log(this.checked);
               if (this.checked){
                   jQuery('.hide_it_goaskle').addClass('show_it_goaskle');
                   jQuery('.show_it_goaskle').removeClass('hide_it_goaskle');
jQuery('input[name=qrct_from_goaskle_com_sc_new_order_wc_emails_enable]').prop('checked',true);
jQuery('input[name=qrct_from_goaskle_com_sc_failed_order_wc_emails_enable]').prop('checked',true);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_on_hold_order_wc_emails_enable]').prop('checked',true);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_processing_order_wc_emails_enable]').prop('checked',true);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_completed_order_wc_emails_enable]').prop('checked',true);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_refunded_order_wc_emails_enable]').prop('checked',true);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_partially_refunded_order_wc_emails_enable]').prop('checked',true);
jQuery('input[name=qrct_from_goaskle_com_sc_cancelled_order_wc_emails_enable]').prop('checked',true);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_invoice_wc_emails_enable]').prop('checked',true);
               } else {
                   jQuery('.show_it_goaskle').addClass('hide_it_goaskle');
                   jQuery('.hide_it_goaskle').removeClass('show_it_goaskle');
jQuery('input[name=qrct_from_goaskle_com_sc_new_order_wc_emails_enable]').prop('checked',false);
jQuery('input[name=qrct_from_goaskle_com_sc_failed_order_wc_emails_enable]').prop('checked',false);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_on_hold_order_wc_emails_enable]').prop('checked',false);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_processing_order_wc_emails_enable]').prop('checked',false);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_completed_order_wc_emails_enable]').prop('checked',false);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_refunded_order_wc_emails_enable]').prop('checked',false);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_partially_refunded_order_wc_emails_enable]').prop('checked',false);
jQuery('input[name=qrct_from_goaskle_com_sc_cancelled_order_wc_emails_enable]').prop('checked',false);
jQuery('input[name=qrct_from_goaskle_com_sc_customer_invoice_wc_emails_enable]').prop('checked',false);                   
               }
           })
       })
       </script>
    <table class="form-table">
    <tr valign="top">
    <th scope="row"><label for="qrct_from_goaskle_com_generator"><?php echo __('Generator', 'qr-code-tag-for-wc-from-goaskle-com'); ?></label></th>
    <td>
        <select name="qrct_from_goaskle_com_generator" style="width:150px;">
                            <option value="google" <?php if ($options['global']['generator'] == 'google') echo 'selected="selected"'; ?> /><?php echo __('Google Chart API', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="lib" <?php if ($options['global']['generator'] == 'lib') echo 'selected="selected"'; ?> /><?php echo __('QR Code Lib', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
        </select>
           </td>
    </tr>
    <tr valign="top">
    <th scope="row"><label for="qrct_from_goaskle_com_imagetype"><?php echo __('Image Type', 'qr-code-tag-for-wc-from-goaskle-com'); ?></label></th>
    <td>
        <select name="qrct_from_goaskle_com_imagetype" style="width:60px;">
                            <option value="gif" <?php if ($options['global']['ext'] == 'gif' ) echo 'selected="selected"'; ?> /><?php echo __('GIF', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="png" <?php if ($options['global']['ext'] == 'png' ) echo 'selected="selected"'; ?> /><?php echo __('PNG', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="jpg" <?php if ($options['global']['ext'] == 'jpg' ) echo 'selected="selected"'; ?> /><?php echo __('JPG', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
        </select>
           </td>
    </tr>
    </table>
       
       <h3><?php echo __('Default Options', 'qr-code-tag-for-wc-from-goaskle-com'); ?></h3>
    <p><?php echo __('This will be the default values of the QR Code Tag Shortcode, Tooltip and Widget.', 'qr-code-tag-for-wc-from-goaskle-com'); ?></p>
    <table width="100%" cellspacing="0" id="inactive-plugins-table" class="widefat goaskle_qr_code_settings">
      <thead><tr>
        <th width="300"><b><?php echo __('Field', 'qr-code-tag-for-wc-from-goaskle-com'); ?></b></th>
        <th width="100"><?php echo __('Shortcode', 'qr-code-tag-for-wc-from-goaskle-com'); ?></th>
        <th width="100"><?php echo __('Tooltip', 'qr-code-tag-for-wc-from-goaskle-com'); ?></th>
        <th width="100"><?php echo __('Widget', 'qr-code-tag-for-wc-from-goaskle-com'); ?></th>
        <th><?php echo __('Description', 'qr-code-tag-for-wc-from-goaskle-com'); ?></th>
      </tr></thead>
      
<!-- 06 03 2023 goaskle.com colors-->      
      <tr>
        <td width="100"><?php echo __('Main color', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" width="100"><input type="text" name="qrct_from_goaskle_com_sc_main_color" style="width:100%;" value="<?php echo sanitize_hex_color($qr_main_color); ?>" /></td>
        <td><?php echo __('The main color with which the main part of the QR code will be formed. ( Please clear all kinds of cache in Wordpress after saving your settings. use CTRL+F5 for clear cache of your browser for Windows users and ⌘ Command + Option + E for MAC users or Safari > Empty Cache )<br><br>(Thanks to Gonzalo for the Idea) and to me for realization. You can always thank me by adding a review for this plugin ( <a href="https://wordpress.org/support/plugin/qr-code-tag-for-wc-from-goaskle-com/reviews/" target="_blank">Post review</a> )', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>

      <tr>
        <td width="100"><?php echo __('Margin color', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" width="100"><input type="text" name="qrct_from_goaskle_com_sc_dots_color" style="width:100%;" value="<?php echo sanitize_hex_color($qr_dots_color); ?>" /></td>
        <td><?php echo __('Margin color around the QR code ( If you have set its size greater than 0 in the settings. Please clear all kinds of cache in Wordpress after saving your settings )', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>            
            
    <!-- 18 02 2023 goaskle.com adding ticket sellers. unique hash for every order plus validation by qr code -->
      <tr>
        <td><?php echo __('Enable unique Hash creation for every Woocommerce order (for ticket sellers). Plus hash validation. Plus activation of hash', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_enable_hash_order_wc"  value="<?php echo esc_html($hash_order_wc); ?>" <?php if ($hash_order_wc == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable this feature if you want to add a unique hash code to every Woocommerce order. You can use this for example to start selling tickets within Woocommerce. By scanning the QR code, you can check the validity of the unique hash for every order and check the data in the admin console (edit order mode).<br>The recommended template to be used is: <code>{wc_website}/?hash-gaqr={hash_gaqr}</code><br><br><b>Steps:</b><br>1. Enable this feature<br>2. Use the recommended template for QR code<br><br><b>Features:</b><br>1. Customers can validate the QR code using any QR app<br>2. Administrators can validate, activate or deactivate the QR code using any QR app or link (in edit order mode). Edit hash. View a list of all QR code activations of each order (edit order mode)<br><br>(Thanks to Marc de Jong for the Idea) and to me for realization.<br>You can always thank me by adding a review for this plugin ( <a href="https://wordpress.org/support/plugin/qr-code-tag-for-wc-from-goaskle-com/reviews/" target="_blank">Post review</a> ) or use the <a href="https://goaskle.com/en/donation/" target="_blank">Donation link</a>', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>      </tr>
      
    <!-- 25 08 2022 goaskle.com adding manually edited template for woocommerce data in qr code -->
      <tr>
        <td><?php echo __('Disable for Woocommerce (Auto)', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_disable_auto_wc"  value="<?php echo esc_html($disable_auto_wc); ?>" <?php if ($disable_auto_wc == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Disable Auto QR code Generating for Woocommerce ( Shortcode mode will continue working )', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
<!-- 17 02 2023 goaskle.com adding checkbox for manually block qr code on all pages of woocommerce and keep on emails only -->      
      <tr>
        <td><?php echo __('Disable for all Woocommerce Pages (auto) and keep QR code in emails enabled', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_disable_for_pages_auto_wc"  value="<?php echo esc_html($disable_pages_auto_wc); ?>" <?php if ($disable_pages_auto_wc == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Disable Auto QR code Generating for Woocommerce PAGES only ( For ex. Thank you page and etc. Shortcode mode will continue working and QR code for Woocommerce emails too )', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>

       <!-- 02 01 2023 goaskle.com -->
      <tr>
        <td><b><?php echo __('Disable for ALL Woocommerce emails except these:', 'qr-code-tag-for-wc-from-goaskle-com'); ?></b></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_disable_all_wc_emails_except_standard"  value="<?php echo esc_html($disable_all_wc_emails_except_standard); ?>" <?php if ($disable_all_wc_emails_except_standard == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Disable QR code Generating for All Woocommerce emails, except ( Except these ones: new order email, failed order email, order-on-hold email, processing email, complete email, refunded email ) some', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <!-- 11 01 2023 goaskle.com -->      
      <tr <?php if ((int)$disable_all_wc_emails_except_standard === 0 || !isset($options['shortcode']['disable_all_wc_emails_except_standard'])) { echo " class='hide_it_goaskle'"; } else {echo " class='show_it_goaskle'";}; ?> >
        <td><?php echo __('Enable for new orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_new_order_wc_emails_enable"  value="<?php echo esc_html($options['shortcode']['qrct_from_goaskle_com_sc_new_order_wc_emails_enable']); ?>" <?php if ($options['shortcode']['qrct_from_goaskle_com_sc_new_order_wc_emails_enable'] == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable QR code Generating for new orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <tr <?php if ((int)$disable_all_wc_emails_except_standard === 0 || !isset($options['shortcode']['disable_all_wc_emails_except_standard'])) { echo " class='hide_it_goaskle'"; } else {echo " class='show_it_goaskle'";}; ?> >
        <td><?php echo __('Enable for failed orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_failed_order_wc_emails_enable"  value="<?php echo esc_html($options['shortcode']['qrct_from_goaskle_com_sc_failed_order_wc_emails_enable']); ?>" <?php if ($options['shortcode']['qrct_from_goaskle_com_sc_failed_order_wc_emails_enable'] == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable QR code Generating for failed orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <tr <?php if ((int)$disable_all_wc_emails_except_standard === 0 || !isset($options['shortcode']['disable_all_wc_emails_except_standard'])) { echo " class='hide_it_goaskle'"; } else {echo " class='show_it_goaskle'";}; ?> >
          
        <td><?php echo __('Enable for on-hold orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_customer_on_hold_order_wc_emails_enable"  value="<?php echo esc_html($options['shortcode']['qrct_from_goaskle_com_sc_customer_on_hold_order_wc_emails_enable']); ?>" <?php if ($options['shortcode']['qrct_from_goaskle_com_sc_customer_on_hold_order_wc_emails_enable'] == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable QR code Generating for on-hold orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <tr <?php if ((int)$disable_all_wc_emails_except_standard === 0 || !isset($options['shortcode']['disable_all_wc_emails_except_standard'])) { echo " class='hide_it_goaskle'"; } else {echo " class='show_it_goaskle'";}; ?> >
          
        <td><?php echo __('Enable for processing orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_customer_processing_order_wc_emails_enable"  value="<?php echo esc_html($options['shortcode']['qrct_from_goaskle_com_sc_customer_processing_order_wc_emails_enable']); ?>" <?php if ($options['shortcode']['qrct_from_goaskle_com_sc_customer_processing_order_wc_emails_enable'] == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable QR code Generating for processing orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <tr <?php if ((int)$disable_all_wc_emails_except_standard === 0 || !isset($options['shortcode']['disable_all_wc_emails_except_standard'])) { echo " class='hide_it_goaskle'"; } else {echo " class='show_it_goaskle'";}; ?> >
          
        <td><?php echo __('Enable for completed orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_customer_completed_order_wc_emails_enable"  value="<?php echo esc_html($options['shortcode']['qrct_from_goaskle_com_sc_customer_completed_order_wc_emails_enable']); ?>" <?php if ($options['shortcode']['qrct_from_goaskle_com_sc_customer_completed_order_wc_emails_enable'] == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable QR code Generating for completed orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>  
      <tr <?php if ((int)$disable_all_wc_emails_except_standard === 0 || !isset($options['shortcode']['disable_all_wc_emails_except_standard'])) { echo " class='hide_it_goaskle'"; } else {echo " class='show_it_goaskle'";}; ?> >
          
        <td><?php echo __('Enable for refunded orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_customer_refunded_order_wc_emails_enable"  value="<?php echo esc_html($options['shortcode']['qrct_from_goaskle_com_sc_customer_refunded_order_wc_emails_enable']); ?>" <?php if ($options['shortcode']['qrct_from_goaskle_com_sc_customer_refunded_order_wc_emails_enable'] == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable QR code Generating for refunded orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>  
      <tr <?php if ((int)$disable_all_wc_emails_except_standard === 0 || !isset($options['shortcode']['disable_all_wc_emails_except_standard'])) { echo " class='hide_it_goaskle'"; } else {echo " class='show_it_goaskle'";}; ?> >
          
        <td><?php echo __('Enable for partially refunded orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_customer_partially_refunded_order_wc_emails_enable"  value="<?php echo esc_html($options['shortcode']['qrct_from_goaskle_com_sc_customer_partially_refunded_order_wc_emails_enable']); ?>" <?php if ($options['shortcode']['qrct_from_goaskle_com_sc_customer_partially_refunded_order_wc_emails_enable'] == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable QR code Generating for partially refunded orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr> 
      <tr <?php if ((int)$disable_all_wc_emails_except_standard === 0 || !isset($options['shortcode']['disable_all_wc_emails_except_standard'])) { echo " class='hide_it_goaskle'"; } else {echo " class='show_it_goaskle'";}; ?> >
          
        <td><?php echo __('Enable for cancelled orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_cancelled_order_wc_emails_enable"  value="<?php echo esc_html($options['shortcode']['qrct_from_goaskle_com_sc_cancelled_order_wc_emails_enable']); ?>" <?php if ($options['shortcode']['qrct_from_goaskle_com_sc_cancelled_order_wc_emails_enable'] == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable QR code Generating for cancelled orders for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>  
      <tr <?php if ((int)$disable_all_wc_emails_except_standard === 0 || !isset($options['shortcode']['disable_all_wc_emails_except_standard'])) { echo " class='hide_it_goaskle'"; } else {echo " class='show_it_goaskle'";}; ?> >
          
        <td><?php echo __('Enable for customer invoice for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" ><input type="checkbox" name="qrct_from_goaskle_com_sc_customer_invoice_wc_emails_enable"  value="<?php echo esc_html($options['shortcode']['qrct_from_goaskle_com_sc_customer_invoice_wc_emails_enable']); ?>" <?php if ($options['shortcode']['qrct_from_goaskle_com_sc_customer_invoice_wc_emails_enable'] == 1) { echo "checked='checked'"; } else {}; ?> /></td>
        <td><?php echo __('Enable QR code Generating for customer invoice for Woocommerce emails', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>      
<!-- 11 01 2023 goaskle.com -->      
      <tr>
        <td width="100"><?php echo __('VAT Number', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" width="100"><input type="text" name="qrct_from_goaskle_com_sc_vat_number" style="width:100%;" value="<?php echo esc_html($options['shortcode']['vat_number']); ?>" /></td>
        <td><?php echo __('Using for Woocommerce in shortcode mode only', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>

      <tr>
        <td width="100"><?php echo __('Woocommerce Data Template', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td colspan="3" width="100"><textarea placeholder="<?php echo __('Leave empty for automatic template', 'qr-code-tag-for-wc-from-goaskle-com'); ?>" rows="10" name="qrct_from_goaskle_com_sc_wc_data_template" style="width:100%;color:red;" value="<?php echo esc_textarea($wc_data_template); ?>" /><?php echo esc_textarea($wc_data_template); ?></textarea></td>
        <td><?php echo __('<b style="color:red;">!!! CLEAR IT FOR AUTOMATIC DATA TEMPLATE IN WOOCOMMERCE !!!</b> Woocommerce will use this template for the data to generate the QR code. The QR code will be created based on the texts and variables of this template. For example (AUTOMATIC):<br>Ivan Ivanov<br>TOTAL AMOUNT $39.10<br>VAT AMOUNT $5.10<br>VAT NUMBER 1235ABN457<br>DATE 03/11/2021<br>TIME 19:03<br>https://goaskle.com<br>You can easy use your own text, labels and these variables: {order_id}, {full_customer_name}, {total_amount}, {total_amount_no_curr}, {vat_amount}, {vat_number}, {order_date}, {order_time}, {wc_website}, {wc_order_items_name1}, {wc_order_items_qty1}, {wc_order_items_price1}, {wc_order_items_subtot1}, {wc_order_items_name2}, {wc_order_items_qty2}, {wc_order_items_price2}, {wc_order_items_subtot2}, {wc_order_items_template1}, {hash_gaqr}<br><b>EXAMPLE FOR YOUR OWN TEMPLATE:</b><br>Order ID: {order_id}<br>{full_customer_name}<br>TOTAL AMOUNT: {total_amount}<br>VAT AMOUNT: {vat_amount}<br>VAT NUMBER: {vat_number}<br>DATE {order_date}<br>TIME {order_time}<br>{wc_website}<br>{wc_order_items_template1}', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>      
      <tr>
        <td width="100"><?php echo __('Size', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_sc_size" style="width:100%;" value="<?php echo esc_html($options['shortcode']['size']); ?>" /></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_tt_size" style="width:100%;" value="<?php echo esc_html($options['tooltip']['size']); ?>" /></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_wg_size" style="width:100%;" value="<?php echo esc_html($options['widget']['size']); ?>" /></td>
        <td><?php echo __('The size of the generated QRCode image (in pixels). it\'s always a square, so you only need to set one side - to enable the "Best Read Mode" <i>(QR Code Lib only)</i> specify a value lower than 10 (read help below)', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <tr>
        <td width="100"><?php echo __('Enc', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td width="100"><select name="qrct_from_goaskle_com_sc_enc" style="width:100%;">
                            <option value="UTF-8" <?php if ($options['shortcode']['enc'] == 'UTF-8') echo 'selected="selected"'; ?> /><?php echo __('UTF-8', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="Shift_JIS" <?php if ($options['shortcode']['enc'] == 'Shift_JIS') echo 'selected="selected"'; ?> /><?php echo __('Shift_JIS', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="ISO-8859-1" <?php if ($options['shortcode']['enc'] == 'ISO-8859-1') echo 'selected="selected"'; ?> /><?php echo __('ISO-8859-1', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
        </select></td>
        <td width="100"><select name="qrct_from_goaskle_com_tt_enc" style="width:100%;">
                            <option value="UTF-8" <?php if ($options['tooltip']['enc'] == 'UTF-8') echo 'selected="selected"'; ?> /><?php echo __('UTF-8', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="Shift_JIS" <?php if ($options['tooltip']['enc'] == 'Shift_JIS') echo 'selected="selected"'; ?> /><?php echo __('Shift_JIS', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="ISO-8859-1" <?php if ($options['tooltip']['enc'] == 'ISO-8859-1') echo 'selected="selected"'; ?> /><?php echo __('ISO-8859-1', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
        </select></td>
        <td width="100"><select name="qrct_from_goaskle_com_wg_enc" style="width:100%;">
                            <option value="UTF-8" <?php if ($options['widget']['enc'] == 'UTF-8') echo 'selected="selected"'; ?> /><?php echo __('UTF-8', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="Shift_JIS" <?php if ($options['widget']['enc'] == 'Shift_JIS') echo 'selected="selected"'; ?> /><?php echo __('Shift_JIS', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="ISO-8859-1" <?php if ($options['widget']['enc'] == 'ISO-8859-1') echo 'selected="selected"'; ?> /><?php echo __('ISO-8859-1', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
        </select></td>
        <td><?php echo __('Specifies how the output is encoded', 'qr-code-tag-for-wc-from-goaskle-com'); ?>
            <i><?php echo __('(Google Chart API only)', 'qr-code-tag-for-wc-from-goaskle-com'); ?></i>
        <hr></td>
      </tr>
      
      <tr>
        <td width="100"><?php echo __('ECC', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td width="100"><select name="qrct_from_goaskle_com_sc_ecc" style="width:100%;">
                            <option value="L" <?php if ($options['shortcode']['ecc'] == 'L') echo 'selected="selected"'; ?> /><?php echo __('L', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="M" <?php if ($options['shortcode']['ecc'] == 'M') echo 'selected="selected"'; ?> /><?php echo __('M', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="Q" <?php if ($options['shortcode']['ecc'] == 'Q') echo 'selected="selected"'; ?> /><?php echo __('Q', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="H" <?php if ($options['shortcode']['ecc'] == 'H') echo 'selected="selected"'; ?> /><?php echo __('H', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
        </select></td>
        <td width="100"><select name="qrct_from_goaskle_com_tt_ecc" style="width:100%;">
                            <option value="L" <?php if ($options['tooltip']['ecc'] == 'L') echo 'selected="selected"'; ?> /><?php echo __('L', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="M" <?php if ($options['tooltip']['ecc'] == 'M') echo 'selected="selected"'; ?> /><?php echo __('M', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="Q" <?php if ($options['tooltip']['ecc'] == 'Q') echo 'selected="selected"'; ?> /><?php echo __('Q', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="H" <?php if ($options['tooltip']['ecc'] == 'H') echo 'selected="selected"'; ?> /><?php echo __('H', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
        </select></td>
        <td width="100"><select name="qrct_from_goaskle_com_wg_ecc" style="width:100%;">
                            <option value="L" <?php if ($options['widget']['ecc'] == 'L') echo 'selected="selected"'; ?> /><?php echo __('L', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="M" <?php if ($options['widget']['ecc'] == 'M') echo 'selected="selected"'; ?> /><?php echo __('M', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="Q" <?php if ($options['widget']['ecc'] == 'Q') echo 'selected="selected"'; ?> /><?php echo __('Q', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
                            <option value="H" <?php if ($options['widget']['ecc'] == 'H') echo 'selected="selected"'; ?> /><?php echo __('H', 'qr-code-tag-for-wc-from-goaskle-com'); ?></option>
        </select></td>
        
        <td><?php echo __('Error Correction Level (see <a href="http://code.google.com/apis/chart/types.html#ec_level_table" target="_blank">Google Chart API</a>)<br/><strong>L</strong> allows 7% of a QR code to be restored<br/><strong>M</strong> allows 15% of a QR code to be restored<br/><strong>Q</strong> allows 25% of a QR code to be restored<br/><strong>H</strong> allows 30% of a QR code to be restored', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <tr>
        <td width="100"><?php echo __('Version', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_sc_ver" style="width:100%;" value="<?php echo esc_html($options['shortcode']['version']); ?>" /></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_tt_ver" style="width:100%;" value="<?php echo esc_html($options['tooltip']['version']); ?>" /></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_wg_ver" style="width:100%;" value="<?php echo esc_html($options['widget']['version']); ?>" /></td>
        <td><?php echo __('<strong>0-40 (0=auto)</strong>. Before choosing the QR code version, consider what kind of device is used to read your code. The best QR code readers are able to read Version 40 codes, mobile devices may read only up to Version 4 ', 'qr-code-tag-for-wc-from-goaskle-com'); ?><i><?php echo __('(QR Code Lib only)', 'qr-code-tag-for-wc-from-goaskle-com'); ?></i><hr></td>
      </tr>
      <tr>
        <td width="100"><?php echo __('Margin', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_sc_margin" style="width:100%;" value="<?php echo esc_html($options['shortcode']['margin']); ?>" /></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_tt_margin" style="width:100%;" value="<?php echo esc_html($options['tooltip']['margin']); ?>" /></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_wg_margin" style="width:100%;" value="<?php echo esc_html($options['widget']['margin']); ?>" /></td>
        <td><?php echo __('Defines the margin (or blank space) around the QR code (in QR Code pixel size - not actual pixels!)', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <tr>
        <td width="100"><?php echo __('Image Classes', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_sc_imageparam" style="width:100%;" value="<?php echo esc_html($options['shortcode']['imageparam']); ?>" /></td>
        <td width="100">&nbsp;</td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_wg_imageparam" style="width:100%;" value="<?php echo esc_html($options['widget']['imageparam']); ?>" /></td>
        <td><?php echo __('Additional image classes (e.g. <i>qrctimage anynewclass</i>)', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <tr>
        <td width="100"><?php echo __('Link', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_sc_link" style="width:100%;" value="<?php echo esc_html($options['shortcode']['link']); ?>" /></td>
        <td width="100">&nbsp;</td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_wg_link" style="width:100%;" value="<?php echo esc_html($options['widget']['link']); ?>" /></td>
        <td><?php echo __('Defines if the image will have a link:<br/><strong>false</strong> = no link<br/><strong>true</strong> = link to the QR code content<br/><strong>url</strong> = link to the current URL<br><strong>http://</strong> = link to some URL (e.g. <i>http://www.google.com</i>)', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
      <tr>
        <td width="100"><?php echo __('A Tag classes', 'qr-code-tag-for-wc-from-goaskle-com'); ?></td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_sc_atagparam" style="width:100%;" value="<?php echo esc_html($options['shortcode']['atagparam']); ?>" /></td>
        <td width="100">&nbsp;</td>
        <td width="100"><input type="text" name="qrct_from_goaskle_com_wg_atagparam" style="width:100%;" value="<?php echo esc_html($options['widget']['atagparam']); ?>" /></td>
        <td><?php echo __('Additional link classes (e.g. <i>mylinkclass anynewlinkclass</i>)', 'qr-code-tag-for-wc-from-goaskle-com'); ?><hr></td>
      </tr>
    </table>
     <p class="submit"><input type="submit" name="update_options" class="button-primary" value="<?php echo __('Save Changes', 'qr-code-tag-for-wc-from-goaskle-com'); ?>" /> <input type="submit" name="reset_options" value="<?php echo __('Reset Options', 'qr-code-tag-for-wc-from-goaskle-com'); ?>" /></p>
    </form>
    <form method="post" name="options" target="_self">
    <h3><?php echo __('QR Code Cache', 'qr-code-tag-for-wc-from-goaskle-com'); ?></h3>
    <?php
    
        function formatBytes($bytes) 
        {
               $types = array( 'Byte', 'KB', 'MB', 'GB', 'TB' );
            for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
            return( round( $bytes, 2 ) . " " . $types[$i] );
        }
    
        require_once(dirname(__FILE__).'/Qrcode.php');
        $qrcode = new Qrcode_from_Goaskle_Com();
        $cacheFiles = 0;
        $cacheSize = 0;
        $qrcode->cacheState($cacheFiles, $cacheSize, $avgCreationTime);
        $cacheSizeReadable = formatBytes($cacheSize);
        $diskspaceleft = formatBytes(disk_free_space (dirname(__FILE__)));
        echo sprintf(__('%1$d codes cached, using %2$s disk space (%3$s free disk space).', 'qr-code-tag-for-wc-from-goaskle-com'),$cacheFiles,$cacheSizeReadable,$diskspaceleft).'<br>';
        echo sprintf(__('%1$01.4f sec average code creation time.', 'qr-code-tag-for-wc-from-goaskle-com'), $avgCreationTime).'<br>';
    ?>
     <p class="submit"><input type="submit" name="clear_cache" class="button-primary" value="<?php echo __('Clear Cache', 'qr-code-tag-for-wc-from-goaskle-com'); ?>" /></p>
    </form>
    
    <h3><?php echo __('Help', 'qr-code-tag-for-wc-from-goaskle-com'); ?></h3>
    <p style="width: 100%;">
        <code>[qrcodetag_from_goaskle_com]<?php echo __('Your content', 'qr-code-tag-for-wc-from-goaskle-com'); ?>[/qrcodetag_from_goaskle_com]</code>
    <?php
        
        $langPath = dirname(__FILE__).'/../../lang/';
        
        $helpFile = $langPath.'help-'. get_locale() .'.html';
        
       
        if (isset($helpFile) && !file_exists($helpFile)) {
            $helpFile = $langPath.'help.html';
            include ($helpFile); 
        } elseif (isset($helpFile) && file_exists($helpFile)) {
            include ($helpFile); 
        }
         
    ?>
    </p>
    
    
</div>
