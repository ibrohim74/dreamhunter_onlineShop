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
                                <img src="<?php echo esc_url($wf_admin_img_path); ?>wf_fw_step1.png" style="width: 60%;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding-left: 27%;">
                                <div class="wt_pklist_field_group_hd_sub"><?php echo __("Company details",'print-invoices-packing-slip-labels-for-woocommerce'); ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                        $settings = array(
                            array(
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

                            array(
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

                            array(
                                'type'  =>  'wt_text',
                                'label' =>  __("Tax ID",'print-invoices-packing-slip-labels-for-woocommerce'),
                                'name'  =>    "woocommerce_wf_packinglist_sender_vat",
                                'class' => 'woocommerce_wf_packinglist_sender_vat',
                                'ref_id'=>  'woocommerce_wf_packinglist_sender_vat',
                                'help_text'=>__("Specify your company tax ID. For e.g., you may enter as VAT: GB123456789, GSTIN:0948745 or ABN:51 824 753 556", 'print-invoices-packing-slip-labels-for-woocommerce'),
                            )
                        );

                        $settings = Wf_Woocommerce_Packing_List::add_fields_to_settings($settings);
                        if(class_exists('WT_Form_Field_Builder_PRO_Documents')){
                            $Form_builder = new WT_Form_Field_Builder_PRO_Documents();
                        }else{
                            $Form_builder = new WT_Form_Field_Builder();
                        }
                        $Form_builder->generate_form_fields($settings);
                        ?>
                        <tr>
                            <td></td>
                            <td><input type="submit" name="update_admin_settings_form" value="Save" class="button button-primary wfte_general_step_button" data-form-id="1" /></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>