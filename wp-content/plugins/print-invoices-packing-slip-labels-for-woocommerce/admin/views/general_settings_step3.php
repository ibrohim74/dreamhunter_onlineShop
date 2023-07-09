<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<table class="wf-form-table">
    <tbody>
        <tr>
            <td style="width:20%;"></td>
            <td style="width:70%;">
                <table>
                    <tbody>
                        <tr>
                            <td colspan="3" style="padding-left: 10%;">
                                <img src="<?php echo esc_url($wf_admin_img_path); ?>wf_fw_step3.png" style="width: 60%;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding-left: 27%;">
                                <div class="wt_pklist_field_group_hd_sub"><?php echo __("Advanced options",'print-invoices-packing-slip-labels-for-woocommerce'); ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                        $settings = array(
                            array(
                                'type'  =>  'wt_textarea',
                                'label' =>  __("Footer",'print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  =>    "woocommerce_wf_packinglist_footer",
                                'class' => 'woocommerce_wf_packinglist_footer',
                            ),

                            array(
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

                            array(
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

                            array(
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
                            $settings[]=array(
                                'type'  =>  "wt_radio",
                                'label' =>  __("PDF library",'print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  =>  "active_pdf_library",
                                'radio_fields'  =>  $pdf_libs_form_arr,
                                'tooltip' => true
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
                        $settings[] = array(
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
                        $settings = Wf_Woocommerce_Packing_List::add_fields_to_settings($settings);
                        if(class_exists('WT_Form_Field_Builder_PRO_Documents')){
                            $Form_builder = new WT_Form_Field_Builder_PRO_Documents();
                        }else{
                            $Form_builder = new WT_Form_Field_Builder();
                        }
                        $Form_builder->generate_form_fields($settings);
                        ?>
                        <td></td>
                        <td>
                            <input type="button" name="" value="Previous" class="wfte_fw_previous button button-secondary" style="margin-right:5px;" data-form-id="wfte_general_step_2">
                            <input type="submit" name="update_admin_settings_form" value="Save" class="button button-primary wfte_general_step_button" data-form-id="3"/></td>
                        <td></td>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>