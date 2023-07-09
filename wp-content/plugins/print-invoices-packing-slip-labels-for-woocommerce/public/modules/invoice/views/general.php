<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id); ?>">
    <style type="text/css">
    .wf_inv_num_frmt_hlp_btn{ cursor:pointer; }
    .wf_inv_num_frmt_hlp table thead th{ font-weight:bold; text-align:left; }
    .wf_inv_num_frmt_hlp table tbody td{ text-align:left; }
    .wf_inv_num_frmt_hlp .wf_pklist_popup_body{min-width:300px; padding:20px;}
    .wf_inv_num_frmt_append_btn{ cursor:pointer; }
    </style>
    <form method="post" class="wf_settings_form">
        <input type="hidden" value="invoice" class="wf_settings_base" />
        <input type="hidden" value="wf_save_settings" class="wf_settings_action" />
        <input type="hidden" value="wt_invoice_general" name="wt_tab_name" class="wt_tab_name" />
        <p><?php _e('Configure the general settings required for the invoice.','print-invoices-packing-slip-labels-for-woocommerce');?></p>
        <?php
        // Set nonce:
        if (function_exists('wp_nonce_field'))
        {
            wp_nonce_field('wf-update-invoice-'.WF_PKLIST_POST_TYPE);
        }
        $date_frmt_tooltip=__('Click to append with existing data','print-invoices-packing-slip-labels-for-woocommerce');
        $order_statuses_customer = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
        $order_statuses_for_customer = array();
        foreach($order_statuses_customer as $cus_ord_status){
            if(isset($order_statuses[$cus_ord_status])){
                $order_statuses_for_customer[$cus_ord_status] = $order_statuses[$cus_ord_status];
            }
        }

        // Get an instance of the WC_emails Object 
        $wc_emails = WC()->mailer();
        $emails_array = $wc_emails->get_emails();
        $new_order_email = $emails_array['WC_Email_New_Order'];
        $new_order_recipient = $new_order_email->recipient;
        ?>
        <table class="wf-form-table">
            <tbody>
                <?php
                    $settings_arr['invoice_general_general'] = array(

                        'woocommerce_wf_enable_invoice' => array(
                            'type' => 'wt_toggle_checkbox',
                            'id' => 'woocommerce_wf_enable_invoice',
                            'class' => 'wt_document_module_enable',
                            'name' => 'woocommerce_wf_enable_invoice',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("Enable to print, download, and mail invoices.","print-invoices-packing-slip-labels-for-woocommerce")),
                            'label' => array(
                                'text' => __('Enable Invoice',"print-invoices-packing-slip-labels-for-woocommerce"),
                                'style' => "font-weight:bold;",
                            ),
                            'tooltip' => true,
                            'col' => 3,
                        ),

                        'wt_inv_gen_hr_line_1' => array(
                            'type' => 'wt_hr_line',
                            'class' => is_plugin_active('wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php') ? 'wf_field_hr' : 'wf_field_hr wf_field_hr_hide',
                            'ref_id' => 'wt_hr_line_1'
                        ),

                        'wt_sub_head_inv_gen_general' => array(
                            'type' => 'wt_sub_head',
                            'class' => 'wt_pklist_field_group_hd_sub',
                            'label' => __("General",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'heading_number' => 1,
                            'ref_id' => 'wt_sub_head_1'
                        ),

                        'woocommerce_wf_orderdate_as_invoicedate' => array(
                            'type' => 'wt_radio',
                            'label' => __("Invoice date","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => '',
                            'class' => 'woocommerce_wf_orderdate_as_invoicedate',
                            'name' => 'woocommerce_wf_orderdate_as_invoicedate',
                            'value' => '',
                            'radio_fields' => array(
                                    'Yes'=>__('Order date','print-invoices-packing-slip-labels-for-woocommerce'),
                                    'No'=>__('Invoiced date','print-invoices-packing-slip-labels-for-woocommerce')
                                ),
                            'col' => 3,
                            'tooltip' => true,
                            'alignment' => 'horizontal_with_label',
                            'ref_id' => 'woocommerce_wf_orderdate_as_invoicedate',
                        ),

                        'woocommerce_wf_generate_for_orderstatus' => array(
                            'type' => 'wt_select2_checkbox',
                            'label' => __("Automate invoice creation","print-invoices-packing-slip-labels-for-woocommerce"),
                            'name' => 'woocommerce_wf_generate_for_orderstatus',
                            'id' => 'woocommerce_wf_generate_for_orderstatus_st',
                            'value' => $order_statuses,
                            'checkbox_fields' => $order_statuses,
                            'class' => 'woocommerce_wf_generate_for_orderstatus',
                            'col' => 3,
                            'placeholder' => __("Choose order status","print-invoices-packing-slip-labels-for-woocommerce"),
                            'help_text' => __("Automatically creates invoices for selected order statuses.","print-invoices-packing-slip-labels-for-woocommerce"),
                            'alignment' => 'vertical_with_label',
                            'ref_id' => 'woocommerce_wf_generate_for_orderstatus',
                        ),

                        'woocommerce_wf_add_invoice_in_admin_mail' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Attach invoice PDF to admin email","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'woocommerce_wf_add_invoice_in_admin_mail',
                            'name' => 'woocommerce_wf_add_invoice_in_admin_mail',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("Enable to attach invoice PDF to the new order email notification","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "woocommerce_wf_add_invoice_in_admin_mail",
                            'col' => 3,
                        ),

                        'woocommerce_wf_add_invoice_in_customer_mail' => array(
                            'type' => 'wt_select2_checkbox',
                            'label' => __("Attach invoice pdf in customer email","print-invoices-packing-slip-labels-for-woocommerce"),
                            'name' => 'woocommerce_wf_add_invoice_in_customer_mail',
                            'id' => 'woocommerce_wf_add_invoice_in_customer_mail_st',
                            'value' => $order_statuses_for_customer,
                            'checkbox_fields' => $order_statuses_for_customer,
                            'class' => 'woocommerce_wf_add_invoice_in_customer_mail',
                            'col' => 3,
                            'placeholder' => __("Choose order status","print-invoices-packing-slip-labels-for-woocommerce"),
                            'help_text' => __("Invoice in PDF format will be attached with the order email for chosen order statuses.","print-invoices-packing-slip-labels-for-woocommerce"),
                            'alignment' => 'vertical_with_label',
                            'ref_id' => 'woocommerce_wf_add_invoice_in_customer_mail',
                        ),

                        'wf_woocommerce_invoice_show_print_button' => array(
                            'type' => 'wt_multi_checkbox',
                            'label' => __("Show print invoice button for customers","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => '',
                            'class' => 'wf_woocommerce_invoice_show_print_button',
                            'name' => 'wf_woocommerce_invoice_show_print_button',
                            'value' => '',
                            'checkbox_fields' => array(
                                'order_listing' => __('My account - Order lists page','print-invoices-packing-slip-labels-for-woocommerce'),
                                'order_details' => __('My account - Order details page', 'print-invoices-packing-slip-labels-for-woocommerce'),
                                'order_email' => __('Order email','print-invoices-packing-slip-labels-for-woocommerce'),
                            ),
                            'col' => 3,
                            'alignment' => 'vertical_with_label',
                            'tooltip' => true
                        ),

                        'wt_inv_gen_hr_line_2' => array(
                            'type' => 'wt_hr_line',
                            'class' => 'wf_field_hr',
                            'ref_id' => 'wt_hr_line_2',
                        ));
                    
                    $settings_arr['invoice_general_invoice_number'] = array(
                        'wt_sub_head_inv_gen_inv_no' => array(
                            'type' => 'wt_sub_head',
                            'class' => 'wt_pklist_field_group_hd_sub',
                            'label' => __("Invoice number",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'heading_number' => 2,
                            'ref_id' => 'wt_sub_head_4'
                        ),

                        'woocommerce_wf_invoice_as_ordernumber' => array(
                            'type' => 'wt_radio',
                            'label' => __("Set invoice number using","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => '',
                            'class' => 'invoice_preview_assert',
                            'name' => 'woocommerce_wf_invoice_as_ordernumber',
                            'value' => '',
                            'radio_fields' => array(
                                    'Yes'=>__('Woocommerce order number','print-invoices-packing-slip-labels-for-woocommerce'),
                                    'No'=>__('Custom number series','print-invoices-packing-slip-labels-for-woocommerce')
                                ),
                            'form_toggler'=>array(
                                'type'=>'parent',
                                'target'=>'wwpl_custom_inv_no',
                            ),
                            'col' => 3,
                            'end_col_call_back' => 'invoice_number_preview',
                            'module_base' => $this->module_base,
                            'tooltip' => true,
                            'alignment' => 'horizontal_with_label',
                            'ref_id' => 'woocommerce_wf_orderdate_as_invoicedate',
                        ),

                        'woocommerce_wf_invoice_start_number' => array(
                            'type' => 'wt_invoice_start_number_text_input',
                            'label' => __('Invoice Start Number','print-invoices-packing-slip-labels-for-woocommerce'),
                            'name' => 'woocommerce_wf_invoice_start_number',
                            'tr_id' => 'woocommerce_wf_invoice_start_number_tr',
                            'form_toggler'=>array(
                                'type'=>'child',
                                'val'=>'No',
                                'id'=>'wwpl_custom_inv_no',
                                'lvl' => 2,
                            ),
                        ),

                        'woocommerce_wf_invoice_number_format' => array(
                            'type'=>'wt_select_dropdown',
                            'label'=>__("Invoice number format", 'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'=>"woocommerce_wf_invoice_number_format",
                            'select_dropdown_fields'=>array(
                                '[number]'=>__('[number]', 'print-invoices-packing-slip-labels-for-woocommerce'),
                                '[number][suffix]'=>__('[number][suffix]', 'print-invoices-packing-slip-labels-for-woocommerce'),
                                '[prefix][number]'=>__('[prefix][number]', 'print-invoices-packing-slip-labels-for-woocommerce'),
                                '[prefix][number][suffix]'=>__('[prefix][number][suffix]', 'print-invoices-packing-slip-labels-for-woocommerce'),
                            ),
                            'class' => 'invoice_preview_assert',
                            'id' => 'woocommerce_wf_invoice_number_format',
                            'help_text' => __("Allows setting a combination of the prefix, number, and/or suffix as your invoice number.","print-invoices-packing-slip-labels-for-woocommerce"),
                        ),

                        'woocommerce_wf_invoice_number_prefix' => array(
                            'type' => 'wt_text',
                            'label'=>__("Prefix",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'=>"woocommerce_wf_invoice_number_prefix",
                            'help_text'=>sprintf(__("Use any of the %s date formats %s or alphanumeric characters.", 'print-invoices-packing-slip-labels-for-woocommerce'), '<a class="wf_inv_num_frmt_hlp_btn" data-wf-trget="woocommerce_wf_invoice_number_prefix">', '</a>'),
                            'class' => 'invoice_preview_assert',
                            'tooltip' => true,
                        ),
                        
                        'woocommerce_wf_invoice_number_postfix' => array(
                            'type' => 'wt_text',
                            'label'=>__("Suffix",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'=>"woocommerce_wf_invoice_number_postfix",
                            'help_text'=>sprintf(__("Use any of the %s date formats %s or alphanumeric characters.", 'print-invoices-packing-slip-labels-for-woocommerce'), '<a class="wf_inv_num_frmt_hlp_btn" data-wf-trget="woocommerce_wf_invoice_number_postfix">', '</a>'),
                            'class' => 'invoice_preview_assert',
                            'tooltip' => true,
                        ),
                        
                        'woocommerce_wf_invoice_padding_number' => array(
                            'type'=>'wt_number',
                            'label'=>__("Invoice number length",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'=>"woocommerce_wf_invoice_padding_number",
                            'attr'=>'min="0"',
                            'help_text'=>__("Indicates the total length of the invoice number, excluding the length of prefix and suffixes. If the length of the generated invoice number is less than the provided, it will be padded with '0'. This setting can be experimented through the preview section. <br> E.g if you specify 7 as invoice length and your invoice number is 8009, it will be represented as 0008009 in the respective documents.","print-invoices-packing-slip-labels-for-woocommerce"),
                            'class' => 'invoice_preview_assert',
                            'id' => 'woocommerce_wf_invoice_padding_number',
                            'tooltip' => true,
                        ),

                        'wt_inv_gen_hr_line_3' => array(
                            'type' => 'wt_hr_line',
                            'class' => 'wf_field_hr',
                            'ref_id' => 'wt_hr_line_4',
                        ));
                        
                    $settings_arr['invoice_general_invoice_details'] = array( 
                        'wt_sub_head_inv_gen_others' => array(
                            'type' => 'wt_sub_head',
                            'class' => 'wt_pklist_field_group_hd_sub',
                            'label' => __("Invoice details",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'heading_number' => 3,
                            'ref_id' => 'wt_sub_head_2',
                        ),

                        'wf_invoice_contactno_email' => array(
                            'type'=>"wt_additional_fields",
                            'label'=>__("Order meta fields", 'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'=>'wf_'.$this->module_base.'_contactno_email',
                            'module_base' => $this->module_base,
                            'ref_id' => 'wt_additional_fields_invoice',
                            'help_text' => __("Select/add order meta to display additional information related to the order on the invoice.","print-invoices-packing-slip-labels-for-woocommerce"),
                        ),

                        'woocommerce_wf_packinglist_logo' => array(
                            'type'=>"wt_uploader",
                            'label'=>__("Custom logo for invoice",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'=>"woocommerce_wf_packinglist_logo",
                            'id'=>"woocommerce_wf_packinglist_logo",
                            'help_text' => __("If left blank, default to the logo from General settings. Ensure to select company logo from ‘Invoice > Customize > Company Logo’ to reflect on the invoice. Recommended size is 150×50px.","print-invoices-packing-slip-labels-for-woocommerce"),
                        ),

                        'wt_inv_gen_hr_line_4' => array(
                            'type' => 'wt_hr_line',
                            'class' => 'wf_field_hr',
                            'ref_id' => 'wt_hr_line_3',
                        ));


                     $settings_arr['invoice_general_others'] = array( 
                        'wt_sub_head_inv_gen_adv' => array(
                            'type' => 'wt_sub_head',
                            'class' => 'wt_pklist_field_group_hd_sub',
                            'label' => __("Others",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'heading_number' => 4,
                            'ref_id' => 'wt_sub_head_3'
                        ),

                        'wf_woocommerce_invoice_prev_install_orders' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Generate invoices for existing orders","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'wf_woocommerce_invoice_prev_install_orders',
                            'name' => 'wf_woocommerce_invoice_prev_install_orders',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("Enable to create invoice for orders generated before plugin installation","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "wf_woocommerce_invoice_prev_install_orders",
                            'col' => 3,
                        ),

                        'wf_woocommerce_invoice_free_orders' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Generate invoices for free orders","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'wf_woocommerce_invoice_free_orders',
                            'name' => 'wf_woocommerce_invoice_free_orders',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("Enable to create invoices for free orders","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "wf_woocommerce_invoice_free_orders",
                            'col' => 3,
                        ),

                        'wf_woocommerce_invoice_free_line_items' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Display free line items in the invoice","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'wf_woocommerce_invoice_free_line_items',
                            'name' => 'wf_woocommerce_invoice_free_line_items',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("Include free(priced as 0) line items in the invoice","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "wf_woocommerce_invoice_free_line_items",
                            'col' => 3,
                            'help_text' => __('Enable to create invoices for free orders.','print-invoices-packing-slip-labels-for-woocommerce'),
                            'ref_id' => 'wf_woocommerce_invoice_free_line_items',
                        ),

                        'woocommerce_wf_custom_pdf_name' => array(
                            'type' => 'wt_select_dropdown',
                            'label' => __("PDF name format","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => "",
                            'name' => "woocommerce_wf_custom_pdf_name",
                            'value' => "",
                            'select_dropdown_fields' => array(
                                    '[prefix][order_no]'=>__('[prefix][order_no]', 'print-invoices-packing-slip-labels-for-woocommerce'),
                                    '[prefix][invoice_no]'=>__('[prefix][invoice_no]', 'print-invoices-packing-slip-labels-for-woocommerce'),
                                ),
                            'class' => "",
                            'col' => 3,
                            'help_text' => __("Select a name format for PDF invoice that includes invoice/order number.","print-invoices-packing-slip-labels-for-woocommerce"),
                            'ref_id' => 'woocommerce_wf_custom_pdf_name',
                        ),

                        'woocommerce_wf_custom_pdf_name_prefix' => array(
                            'type' => "wt_text",
                            'label' => __("Custom PDF name prefix", 'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name' => 'woocommerce_wf_custom_pdf_name_prefix',
                            'help_text'=>__("Input a custom prefix for ‘PDF name format’ that will appear at the beginning of the name. Defaulted to ‘Invoice_’.",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'ref_id' => 'woocommerce_wf_custom_pdf_name_prefix',
                        ),                        
                    );
                    
                    $settings_arr = Wf_Woocommerce_Packing_List::add_fields_to_settings($settings_arr,$target_id,$template_type,$this->module_id);

                    if(class_exists('WT_Form_Field_Builder_PRO_Documents')){
                        $Form_builder = new WT_Form_Field_Builder_PRO_Documents();
                    }else{
                        $Form_builder = new WT_Form_Field_Builder();
                    }

                    $h_no = 1;
                    foreach($settings_arr as $settings){
                        foreach($settings as $k => $this_setting){
                            if(isset($this_setting['type']) && "wt_sub_head" === $this_setting['type']){
                                $settings[$k]['heading_number'] = $h_no;
                                $h_no++;
                            }
                        }
                        $Form_builder->generate_form_fields($settings, $this->module_id);
                    }
                ?>
            </tbody>
        </table>
        <div class="wf_inv_num_frmt_hlp wf_pklist_popup">
            <div class="wf_pklist_popup_hd">
                <span style="line-height:40px;" class="dashicons dashicons-calendar-alt"></span> <?php _e('Date formats','print-invoices-packing-slip-labels-for-woocommerce');?>
                <div class="wf_pklist_popup_close">X</div>
            </div>
            <div class="wf_pklist_popup_body">
                <table class="wp-list-table widefat striped">
                    <thead>
                        <tr>
                            <th><?php _e('Format','print-invoices-packing-slip-labels-for-woocommerce');?></th><th><?php _e('Output','print-invoices-packing-slip-labels-for-woocommerce');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[F]</a></td>
                            <td><?php echo date('F'); ?></td>
                        </tr>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[dS]</a></td>
                            <td><?php echo date('dS'); ?></td>
                        </tr>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[M]</a></td>
                            <td><?php echo date('M'); ?></td>
                        </tr>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[m]</a></td>
                            <td><?php echo date('m'); ?></td>
                        </tr>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[d]</a></td>
                            <td><?php echo date('d'); ?></td>
                        </tr>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[D]</a></td>
                            <td><?php echo date('D'); ?></td>
                        </tr>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[y]</a></td>
                            <td><?php echo date('y'); ?></td>
                        </tr>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[Y]</a></td>
                            <td><?php echo date('Y'); ?></td>
                        </tr>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[d/m/y]</a></td>
                            <td><?php echo date('d/m/y'); ?></td>
                        </tr>
                        <tr>
                            <td><a class="wf_inv_num_frmt_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[d-m-Y]</a></td>
                            <td><?php echo date('d-m-Y'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php 
            include plugin_dir_path( WF_PKLIST_PLUGIN_FILENAME )."admin/views/admin-settings-save-button.php";
        ?>
    </form>
</div>
<?php 
    //settings form fields
    do_action('wf_pklist_module_settings_form');
?>