<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images/';
?>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id);?>">
    <form method="post" class="wf_settings_form wf_general_settings_form">
        <input type="hidden" value="main" class="wf_settings_base" />
        <input type="hidden" value="wf_save_settings" class="wf_settings_action" />
        <input type="hidden" value="wt_main_general" name="wt_tab_name" class="wt_tab_name" />
        <p><?php _e("The company name and the address details from this section will be used as the sender address in the invoice and other related documents.","print-invoices-packing-slip-labels-for-woocommerce");?></p>
        <?php
        // Set nonce:
        if (function_exists('wp_nonce_field'))
        {
            wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
        }
        $tooltip_conf=Wf_Woocommerce_Packing_List_Admin::get_tooltip_configs('load_default_address');  
        $load_from_woo = sprintf(
            '<a class="wf_pklist_load_address_from_woo %1$s" %2$s>
            <span class="dashicons dashicons-admin-page"></span>%3$s</a>',
        $tooltip_conf['class'],
        $tooltip_conf['text'],
        __('Load from WooCommerce','print-invoices-packing-slip-labels-for-woocommerce')
        );
        ?>
        <table class="wf-form-table">
            <tbody>
                <?php
                    $settings_arr['general_company_details'] = array(
                        'wt_sub_head_company_details' => array(
                            'type'  =>  'wt_sub_head',
                            'class' =>  'wt_pklist_field_group_hd_sub',
                            'label' =>  __("Company details",'print-invoices-packing-slip-labels-for-woocommerce'),
                        ),

                        'woocommerce_wf_packinglist_companyname' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Name",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_companyname",
                            'class' => 'woocommerce_wf_packinglist_companyname',
                            'tooltip'=> true,
                            'help_text' => sprintf('%1$s <b>%2$s</b> %3$s <b>%4$s</b>.',
                                            __("To include the keyed in name to the Invoice, ensure to select","print-invoices-packing-slip-labels-for-woocommerce"),
                                            __("Company name","print-invoices-packing-slip-labels-for-woocommerce"),
                                            __("from","print-invoices-packing-slip-labels-for-woocommerce"),
                                            __("Invoice > Customize > Company Logo / Name","print-invoices-packing-slip-labels-for-woocommerce"))
                        ),

                        'woocommerce_wf_packinglist_logo' => array(
                            'type'  =>  "wt_uploader",
                            'label' =>  __("Logo",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>  "woocommerce_wf_packinglist_logo",
                            'id'    =>  "woocommerce_wf_packinglist_logo",
                            'help_text'=> sprintf('%1$s <b>%2$s</b>. %3$s .',
                                        __("To include the uploaded image as logo to the invoice, ensure to select Company logo from","print-invoices-packing-slip-labels-for-woocommerce"),
                                        __("Invoice > Customize > Company Logo / Name","print-invoices-packing-slip-labels-for-woocommerce"),
                                        __("Recommended size is 150×50px","print-invoices-packing-slip-labels-for-woocommerce")
                                    ),
                             'tooltip'=> true,
                        ),

                        'woocommerce_wf_packinglist_sender_vat' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Company Tax ID",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_vat",
                            'class' => 'woocommerce_wf_packinglist_sender_vat',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_vat',
                            'help_text'=>__("Specify your company tax ID. For e.g., you may enter as VAT: GB123456789, GSTIN:0948745 or ABN:51 824 753 556", 'print-invoices-packing-slip-labels-for-woocommerce'),
                        ),
                        'woocommerce_wf_packinglist_footer' => array(
                            'type'  =>  'wt_textarea',
                            'label' =>  __("Footer",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_footer",
                            'class' => 'woocommerce_wf_packinglist_footer',
                        ),

                        'wt_doc_hr_line_1' => array(
                            'type' => 'wt_hr_line',
                            'class' => 'wf_field_hr',
                            'ref_id' => 'wt_doc_hr_line_1',
                        )
                    );
                    $settings_arr['general_address_details'] = array(
                        'wt_sub_head_address_details' => array(
                            'type'  =>  'wt_sub_head',
                            'class' =>  'wt_pklist_field_group_hd_sub',
                            'label' =>  __("Address details",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'ref_id' => 'wt_doc_sub_head_company_address',
                            'col_3' =>  $load_from_woo,
                            'tooltip'=> true,
                        ),

                        'woocommerce_wf_packinglist_sender_name' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Department/Business unit/Sender name",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_name",
                            'class' =>  'woocommerce_wf_packinglist_sender_name',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_name',
                        ),

                        'woocommerce_wf_packinglist_sender_address_line1' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Address line 1",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_address_line1",
                            'class' =>  'woocommerce_wf_packinglist_sender_address_line1',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_address_line1',
                            'mandatory'=>true,
                        ),

                        'woocommerce_wf_packinglist_sender_address_line2' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Address line 2",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_address_line2",
                            'class' =>  'woocommerce_wf_packinglist_sender_address_line2',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_address_line2',
                        ),

                        'woocommerce_wf_packinglist_sender_city' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("City",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_city",
                            'class' =>  'woocommerce_wf_packinglist_sender_city',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_city',
                            'mandatory'=>true,
                        ),

                        'wf_country' => array(
                            'type'  =>  'wt_wc_country_dropdown',
                            'label' => __('Country/State','print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  => 'wf_country',
                            'placeholder'   => __( 'Choose a country&hellip;','print-invoices-packing-slip-labels-for-woocommerce'),
                            'mandatory' => true,
                        ),

                        'woocommerce_wf_packinglist_sender_postalcode' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Postal code",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_postalcode",
                            'class' =>  'woocommerce_wf_packinglist_sender_postalcode',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_postalcode',
                            'mandatory'=>true,
                        ),

                        'woocommerce_wf_packinglist_sender_contact_number' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Contact number",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_contact_number",
                            'class' =>  'woocommerce_wf_packinglist_sender_contact_number',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_contact_number',
                        ),

                        'wt_doc_hr_line_1' => array(
                            'type' => 'wt_hr_line',
                            'class' => 'wf_field_hr',
                            'ref_id' => 'wt_doc_hr_line_1',
                        ));
                    $settings_arr['advanced_option'] = array(
                        'wt_doc_sub_head_company_info' => array(
                            'type'  =>  'wt_sub_head',
                            'class' =>  'wt_pklist_field_group_hd_sub',
                            'label' =>  __("Advanced options",'print-invoices-packing-slip-labels-for-woocommerce'),
                            // 'heading_number' => 1,
                            'ref_id' => 'wt_doc_sub_head_company_info'
                        ),

                       'woocommerce_wf_state_code_disable' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Display state name","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'woocommerce_wf_state_code_disable',
                            'name' => 'woocommerce_wf_state_code_disable',
                            'value' => "yes",
                            'checkbox_fields' => array('yes'=> __("Enable to show state name in addresses","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "woocommerce_wf_state_code_disable",
                            'col' => 3,
                            'tooltip' => true,
                        ),

                        'woocommerce_wf_packinglist_preview' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Preview before printing","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'woocommerce_wf_packinglist_preview',
                            'name' => 'woocommerce_wf_packinglist_preview',
                            'value' => "enabled",
                            'checkbox_fields' => array('enabled'=> __("Preview documents before printing","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "woocommerce_wf_packinglist_preview",
                            'col' => 3,
                            'tooltip' => true
                        ),

                        'woocommerce_wf_add_rtl_support' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Enable RTL support","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'woocommerce_wf_add_rtl_support',
                            'name' => 'woocommerce_wf_add_rtl_support',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("RTL support for documents","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "woocommerce_wf_add_rtl_support",
                            'col' => 3,
                            'help_text' => sprintf('%1$s <a href="https://wordpress.org/plugins/mpdf-addon-for-pdf-invoices/">%2$s</a>.',
                                __("For better RTL integration in PDF documents, please use our","print-invoices-packing-slip-labels-for-woocommerce"),
                                __("mPDF add-on","print-invoices-packing-slip-labels-for-woocommerce")),
                        ),
                    );
                    if(is_array($pdf_libs) && count($pdf_libs)>1)
                    {
                        $pdf_libs_form_arr=array();
                        foreach ($pdf_libs as $key => $value)
                        {
                            $pdf_libs_form_arr[$key]=(isset($value['title']) ? $value['title'] : $key);
                        }
                        $settings_arr['advanced_option']['active_pdf_library']=array(
                            'type'  =>  "wt_radio",
                            'label' =>  __("PDF library",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>  "active_pdf_library",
                            'radio_fields'  =>  $pdf_libs_form_arr,
                            'tooltip' => true,
                        );
                    }

                    $is_tax_enabled=wc_tax_enabled();
                    $tax_not_enabled_info='';
                    $incl_tax_img = '<br><br><img src="'.$wf_admin_img_path.'/incl_tax.png" alt="include tax img" width="100%" style="background: #f0f0f1;padding: 10px;">'; 
                    $excl_tax_img = '<br><br><img src="'.$wf_admin_img_path.'/excl_tax.png" alt="include tax img" width="100%" style="background: #f0f0f1;padding: 10px;">'; 
                    if(!$is_tax_enabled)
                    {
                        $tax_not_enabled_info.='<br>';
                        $tax_not_enabled_info.=sprintf(__('%sNote:%s You have not enabled tax option in WooCommerce. If you need to apply tax for new orders you need to enable it %s here. %s', 'print-invoices-packing-slip-labels-for-woocommerce'), '<b>', '</b>', '<a href="'.admin_url('admin.php?page=wc-settings').'" target="_blank">', '</a>');
                        $incl_tax_img = ""; 
                        $excl_tax_img = "";
                    }
                    $settings_arr['advanced_option']['woocommerce_wf_generate_for_taxstatus'] = array(
                        'type'  => 'wt_radio',
                        'label' => __('Display price in the product table', 'print-invoices-packing-slip-labels-for-woocommerce'),
                        'name'  => 'woocommerce_wf_generate_for_taxstatus',
                        'id'    => 'woocommerce_wf_generate_for_taxstatus',
                        'class' => 'woocommerce_wf_generate_for_taxstatus',
                        'attr'=>($is_tax_enabled ? '' : "disabled"),
                        'after_form_field'=>($is_tax_enabled ? '<a href="'.admin_url('admin.php?page=wc-settings&tab=tax').'" class="" target="_blank" style="text-align:center;">'.__('WooCommerce tax settings', 'print-invoices-packing-slip-labels-for-woocommerce').'<span class="dashicons dashicons-external"></span></a>' : ""),
                            'radio_fields'  =>  array(
                                'ex_tax'=>__('Exclude tax','print-invoices-packing-slip-labels-for-woocommerce'),
                                'in_tax'=>__('Include tax','print-invoices-packing-slip-labels-for-woocommerce'),
                            ),
                        'help_text_conditional'=>array(
                            array(
                                'help_text'=>__('All price columns displayed will be inclusive of tax.', 'print-invoices-packing-slip-labels-for-woocommerce').$tax_not_enabled_info.$incl_tax_img,
                                'condition'=>array(
                                    array('field'=>'woocommerce_wf_generate_for_taxstatus', 'value'=>'in_tax')
                                )
                            ),
                            array(
                                'help_text'=>__('All price columns displayed will be exclusive of tax.', 'print-invoices-packing-slip-labels-for-woocommerce').$tax_not_enabled_info.$excl_tax_img,
                                'condition'=>array(
                                    array('field'=>'woocommerce_wf_generate_for_taxstatus', 'value'=>'ex_tax')
                                )
                            )
                        ),
                    );
                    $settings_arr = Wf_Woocommerce_Packing_List::add_fields_to_settings($settings_arr,'general',"","");
                    if(class_exists('WT_Form_Field_Builder_PRO_Documents')){
                        $Form_builder = new WT_Form_Field_Builder_PRO_Documents();
                    }else{
                        $Form_builder = new WT_Form_Field_Builder();
                    }
                    foreach($settings_arr as $settings){
                        $Form_builder->generate_form_fields($settings);
                    }
                ?>
            </tbody>
        </table>
        <?php 
            include "admin-settings-save-button.php";
        ?>
    </form>
</div>