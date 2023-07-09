<!-- DC ready -->
<style type="text/css">
body, html{margin:0px; padding:0px; font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;}
.clearfix::after{ display:block; clear:both; content:""; }

.wfte_invoice-main{ color:#202020; font-size:12px; font-weight:400; box-sizing:border-box; width:100%; padding:30px 0px; background:#fff; height:auto; border:solid 1px #000;margin:10px 0px;}
.wfte_invoice-main *{ box-sizing:border-box;}


.wfte_row{ width:100%; display:block; }
.wfte_col-1{ width:100%; display:block;}
.wfte_col-2{ width:50%; display:block;}
.wfte_col-3{ width:33%; display:block;}
.wfte_col-4{ width:25%; display:block;}
.wfte_col-6{ width:30%; display:block;}
.wfte_col-7{ width:69%; display:block;}

.wfte_padding_left_right{ padding:0px 30px; }
.wfte_hr{ height:1px; background:transparent; border-top: 1px solid #000;border-bottom: none;border-left: none;border-right: none;}

.wfte_company_logo_img_box{ margin-bottom:10px; }
.wfte_company_logo_img{ width:150px; max-width:100%; }
.wfte_company_name{ font-size:24px; font-weight:bold; }
.wfte_company_logo_extra_details{ font-size:12px; margin-top:3px;}
.wfte_barcode{ margin-top:5px;}
.wfte_invoice_data div span:last-child, .wfte_extra_fields span:last-child{ font-weight:bold; }

.wfte_invoice_data{ line-height:16px; font-size:12px; }
.wfte_shipping_address{ width:95%;}
.wfte_billing_address{ width:95%; }
.wfte_address-field-header{ font-weight:bold; font-size:12px; color:#000; padding:3px; padding-left:0px;}
.wfte_addrss_field_main{ padding-top:15px;}

.wfte_product_table{ width:100%; border-collapse:collapse; margin:0px; }
.wfte_payment_summary_table_body .wfte_right_column{ text-align:left; }
.wfte_payment_summary_table{ margin-bottom:10px; }
.wfte_product_table_head_bg{ background:#f4f4f4; }
.wfte_table_head_color{ color:#2e2e2e; }

.wfte_product_table_head{}
.wfte_product_table_head th{ height:36px; padding:0px 5px; font-size:.75rem; text-align:start; line-height:10px; font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;}
.wfte_product_table_body td, .wfte_payment_summary_table_body td{ font-size:12px; line-height:16px;}
.wfte_product_table_body td{ padding:7px 5px; border-bottom:solid 1px #dddee0; text-align:start;}
.wfte_product_table .wfte_right_column{ width:20%;}
.wfte_payment_summary_table .wfte_left_column{ width:60%; }
.wfte_product_table_body .product_td b{ font-weight:normal; }

.wfte_payment_summary_table_body td{ padding:5px 5px; border:none;}

.wfte_product_table_payment_total td{ font-size:13px; color:#000; height:28px;}
.wfte_product_table_payment_total td:nth-child(3){ font-weight:bold; }

/* for mPdf */
.wfte_invoice_data{ border:solid 0px #fff; }
.wfte_invoice_data td, .wfte_extra_fields td{ font-size:12px; padding:0px; font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif; line-height:14px;}
.wfte_invoice_data tr td:nth-child(2), .wfte_extra_fields tr td:nth-child(2){ font-weight:bold; }

.wfte_signature{ width:100%; height:auto; min-height:60px; padding:5px 0px;}
.wfte_signature_label{ font-size:12px; }
.wfte_image_signature_box{ display:inline-block;}
.wfte_return_policy{width:100%; height:auto; margin-top:5px; }
.wfte_footer{height:auto; margin-top:5px;}

.wfte_received_seal{ position:absolute; z-index:10; margin-top:0px; margin-left:0px; width:130px; border-radius:5px; font-size:22px; height:40px; line-height:28px; border:solid 5px #00ccc5; color:#00ccc5; font-weight:900; text-align:center; transform:rotate(0deg); opacity:.5; }

.float_left{ float:left; }
.float_right{ float:right; }
.wfte_product_table_category_row td{ padding:10px 5px;}
.wfte_col-1, .wfte_col-2, .wfte_col-3, .wfte_col-4, .wfte_col-5, .wfte_col-6, .wfte_col-7, .wfte_company_logo, .wt_pklist_dc_editable_selected {
    min-height: 35px;
}
</style>
<div class="wfte_rtl_main wfte_invoice-main wfte_main wfte_custom_shipping_size">
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right clearfix">
        <div class="wfte_col-4 float_left">
            <div class="wfte_from_address wfte_template_element" data-wfte_name="from_address" data-hover-id="from_address">
                <div class="wfte_address-field-header wfte_from_address_label">__[From:]__</div>
                <div class="wfte_from_address_val">[wfte_from_address]</div>
            </div>
        </div>
        <div class="wfte_col-2 float_left"></div>
        <div class="wfte_col-4 float_right">
            <div class="wfte_shipping_details wfte_invoice_data" data-wfte_name="invoice_data">
                <div class="wfte_order_number wfte_template_element" data-hover-id="order_number">
                    <span class="wfte_order_number_label">__[Order No:]__</span> [wfte_order_number]
                </div>
                <div class="wfte_weight wfte_template_element" data-hover-id="weight">
                    <span class="wfte_weight_label">__[Weight:]__</span> [wfte_weight]
                </div>
                <div class="wfte_ship_date wfte_template_element" data-hover-id="ship_date">
                    <span class="wfte_ship_date_label">__[Ship Date:]__</span> [wfte_ship_date]
                </div>
                <div class="wfte_order_item_meta">[wfte_order_item_meta]</div>
                [wfte_extra_fields]
                [wfte_additional_data]
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right clearfix">
        <div class="wfte_col-3 float_left" style="height: 1px;"></div>
        <div class="wfte_col-3 float_left">
            <div class="wfte_shipping_address wfte_template_element" data-wfte_name="shipping_address" data-hover-id="shipping_address">
                <div class="wfte_address-field-header wfte_shipping_address_label">__[To:]__</div>
                <div class="wfte_shipping_address_val">[wfte_shipping_address]</div>
            </div>
            <div class="wfte_tel wfte_hidden wfte_template_element" data-hover-id="tel">
                <span class="wfte_tel_label">__[Tel:]__</span>
                <span>[wfte_tel]</span>
            </div>
            <div class="wfte_email wfte_hidden wfte_template_element" data-hover-id="email">
                <span class="wfte_email_label">__[Email:]__</span>
                <span>[wfte_email]</span>
            </div>
        </div>
        <div class="wfte_col-3 float_right" style="height: 1px;"></div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right clearfix" style="margin-top:10px;">
        <div class="wfte_col-3 float_left" style="height: 1px;"></div>
        <div class="wfte_col-3 float_left" style="text-align: left;">
            <div class="wfte_invoice_number wfte_template_element" data-hover-id="invoice_number" style="font-size: 14px;">
                <span class="wfte_invoice_number_label">__[Tracking number:]__</span>
                <span>[wfte_invoice_number]</span>
            </div>
            <div class="wfte_barcode wfte_template_element" data-wfte_name="barcode" data-hover-id="barcode">
                <img src="[wfte_barcode_url]" style="">
            </div>
        </div>
        <div class="wfte_col-3 float_right" style="height: 1px;"></div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right clearfix">
        <div class="wfte_col-1 float_right">
            <div class="wfte_footer wfte_text_left clearfix wfte_template_element" data-wfte_name="footer" data-hover-id="footer">
            [wfte_footer]
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>