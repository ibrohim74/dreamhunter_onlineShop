<?php
if ( ! defined( 'WPINC' ) ) {
    die;
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
        <tr>
            <td style="width:20%;"></td>
            <td style="width:70%;">
                <table>
                    <tbody>
                        <tr>
                            <td colspan="3" style="padding-left: 10%;">
                                <img src="<?php echo esc_url($wf_admin_img_path); ?>wf_fw_step2.png" style="width: 60%;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding-left: 27%;">
                                <div class="wt_pklist_field_group_hd_sub"><?php echo __("Address details",'print-invoices-packing-slip-labels-for-woocommerce'); ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                        $settings = array(
                            array(
                                'type'  =>  'wt_sub_head',
                                'class' =>  'wt_pklist_field_group_hd_sub',
                                'ref_id' => 'wt_doc_sub_head_company_address',
                                'col_3' =>  $load_from_woo,
                            ),

                            array(
                                'type'  =>  'wt_text',
                                'label' =>  __("Department/Business unit/Sender name",'print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  =>    "woocommerce_wf_packinglist_sender_name",
                                'class' =>  'woocommerce_wf_packinglist_sender_name',
                                'ref_id'=>  'woocommerce_wf_packinglist_sender_name',
                            ),

                            array(
                                'type'  =>  'wt_text',
                                'label' =>  __("Address line 1",'print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  =>    "woocommerce_wf_packinglist_sender_address_line1",
                                'class' =>  'woocommerce_wf_packinglist_sender_address_line1',
                                'ref_id'=>  'woocommerce_wf_packinglist_sender_address_line1',
                                'mandatory'=>true,
                            ),

                            array(
                                'type'  =>  'wt_text',
                                'label' =>  __("Address line 2",'print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  =>    "woocommerce_wf_packinglist_sender_address_line2",
                                'class' =>  'woocommerce_wf_packinglist_sender_address_line2',
                                'ref_id'=>  'woocommerce_wf_packinglist_sender_address_line2',
                            ),

                            array(
                                'type'  =>  'wt_text',
                                'label' =>  __("City",'print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  =>    "woocommerce_wf_packinglist_sender_city",
                                'class' =>  'woocommerce_wf_packinglist_sender_city',
                                'ref_id'=>  'woocommerce_wf_packinglist_sender_city',
                                'mandatory'=>true,
                            ),

                            array(
                                'type'  =>  'wt_wc_country_dropdown',
                                'label' => __('Country/State','print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  => 'wf_country',
                                'placeholder'   => __( 'Choose a country&hellip;','print-invoices-packing-slip-labels-for-woocommerce'),
                                'mandatory' => true,
                            ),
                            array(
                                'type'  =>  'wt_text',
                                'label' =>  __("Postal code",'print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  =>    "woocommerce_wf_packinglist_sender_postalcode",
                                'class' =>  'woocommerce_wf_packinglist_sender_postalcode',
                                'tooltip'=>  'woocommerce_wf_packinglist_sender_postalcode',
                                'mandatory'=>true,

                            ),

                            array(
                                'type'  =>  'wt_text',
                                'label' =>  __("Contact number",'print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  =>    "woocommerce_wf_packinglist_sender_contact_number",
                                'class' =>  'woocommerce_wf_packinglist_sender_contact_number',
                                'ref_id'=>  'woocommerce_wf_packinglist_sender_contact_number',
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
                            <input type="button" name="" value="Previous" class="wfte_fw_previous button button-secondary" style="margin-right:5px;" data-form-id="wfte_general_step_1">
                            <input type="submit" name="update_admin_settings_form" value="Save" class="button button-primary wfte_general_step_button" data-form-id="2" /></td>
                        <td></td>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>