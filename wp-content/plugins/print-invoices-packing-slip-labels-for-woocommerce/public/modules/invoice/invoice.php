<?php
/**
 * Invoice section of the plugin
 *
 * @link       
 * @since 2.5.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}

class Wf_Woocommerce_Packing_List_Invoice
{
	public $module_id='';
	public static $module_id_static='';
	public $module_base='invoice';
    public $customizer=null;
    public $is_enable_invoice='';
    public static $return_dummy_invoice_number=false;  //it will return dummy invoice number if force generate is on
	public function __construct()
	{	
		
		$this->module_id=Wf_Woocommerce_Packing_List::get_module_id($this->module_base);
		self::$module_id_static=$this->module_id;
		add_filter('wf_module_default_settings',array($this,'default_settings'),10,2);
		add_filter('wf_module_single_checkbox_fields', array($this, 'single_checkbox_fields'), 10, 3);
		add_filter('wf_module_multi_checkbox_fields', array($this, 'multi_checkbox_fields'), 10, 3);
		add_filter('wf_module_save_multi_checkbox_fields',array($this, 'save_multi_checkbox_fields'), 10, 4);
		add_filter('wf_module_customizable_items',array($this,'get_customizable_items'),10,2);
		add_filter('wf_module_non_options_fields',array($this,'get_non_options_fields'),10,2);
		add_filter('wf_module_non_disable_fields',array($this,'get_non_disable_fields'),10,2);
		
		//hook to add which fiedls to convert
		add_filter('wf_module_convert_to_design_view_html',array($this,'convert_to_design_view_html'),10,3);

		//hook to generate template html
		add_filter('wf_module_generate_template_html',array($this,'generate_template_html'), 10, 6);

		//initializing customizer		
		$this->customizer=Wf_Woocommerce_Packing_List::load_modules('customizer');

		$this->is_enable_invoice=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$this->module_id);
		if("Yes" === $this->is_enable_invoice) /* `print_it` method also have the same checking */
		{	
			// show document details
			add_filter('wt_print_docdata_metabox',array($this, 'add_docdata_metabox'),10,3);

			// show document print/download buttons
			add_filter('wt_print_actions',array($this,'add_print_buttons'),10,4);

			add_filter('wt_print_bulk_actions',array($this,'add_bulk_print_buttons'));
			add_filter('wt_frontend_print_actions',array($this,'add_frontend_print_buttons'),10,3);
			add_filter('wt_pklist_intl_frontend_order_list_page_print_actions', array($this, 'add_frontend_order_list_page_print_buttons'), 10, 3);					
			add_filter('wt_email_print_actions',array($this,'add_email_print_buttons'),10,3);
			add_filter('wt_email_attachments',array($this,'add_email_attachments'),10,4);
			add_action('woocommerce_thankyou',array($this,'generate_invoice_number_on_order_creation'),10,1);
			add_action('woocommerce_order_status_changed',array($this,'generate_invoice_number_on_status_change'),10,3);
		}
		add_action('wt_print_doc',array($this,'print_it'),10,2);

		//add fields to customizer panel
		add_filter('wf_pklist_alter_customize_inputs',array($this,'alter_customize_inputs'),10,3);
		add_filter('wf_pklist_alter_customize_info_text',array($this,'alter_customize_info_text'),10,3);
		
		add_filter('wt_pklist_alter_order_template_html',array($this,'alter_final_template_html'),10,3);
		
		add_action('wt_run_necessary',array($this,'run_necessary'));

		//invoice column and value
		add_filter('manage_edit-shop_order_columns',array($this,'add_invoice_column'),11); /* Add invoice number column to order page */
		add_action('manage_shop_order_posts_custom_column',array($this,'add_invoice_column_value'),11); /* Add value to invoice number column in order page */
		add_action('manage_edit-shop_order_sortable_columns',array($this,'sort_invoice_column'),11);

		add_filter('wt_pklist_alter_tooltip_data',array($this, 'register_tooltips'),1);

		/** 
		* @since 2.6.2 declaring multi select form fields in settings form 
		*/
		add_filter('wt_pklist_intl_alter_multi_select_fields', array($this,'alter_multi_select_fields'), 10, 2);
		
		/** 
		* @since 2.6.2 Declaring validation rule for form fields in settings form 
		*/
		add_filter('wt_pklist_intl_alter_validation_rule', array($this,'alter_validation_rule'), 10, 2);

		/** 
		* @since 2.6.2 Enable PDF preview option
		*/
		add_filter('wf_pklist_intl_customizer_enable_pdf_preview', array($this,'enable_pdf_preview'), 10, 2);

		/* @since 2.6.9 add admin menu */
		add_filter('wt_admin_menu', array($this,'add_admin_pages'),10,1);

		add_action('wt_pklist_auto_generate_invoice_number_module',array($this,'generate_auto_invoice_number'),10);
	}

	/**
	* 	Add admin menu
	*	@since 	2.6.9
	*/
	public function add_admin_pages($menus)
	{
		$menus[]=array(
			'submenu',
			WF_PKLIST_POST_TYPE,
			__('Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
			__('Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
			'manage_woocommerce',
			$this->module_id,
			array($this,'admin_settings_page'),
			'id' => 'invoice',
		);
		return $menus;
	}

	/**
	*  	Admin settings page
	*	@since 	2.6.9
	*/
	public function admin_settings_page()
	{
		$order_statuses = wc_get_order_statuses();
		$wf_generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus', $this->module_id);
		wp_enqueue_script('wc-enhanced-select');
		wp_enqueue_style('woocommerce_admin_styles',WC()->plugin_url().'/assets/css/admin.css');
		wp_enqueue_media();
		if(!class_exists('Wf_Woocommerce_Packing_List_Pro_Common_Func')){
			wp_enqueue_script($this->module_id,plugin_dir_url( __FILE__ ).'assets/js/main.js',array('jquery'),WF_PKLIST_VERSION);
		}
		if(!is_plugin_active('wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php') && isset($_GET['page']) && "wf_woocommerce_packing_list_invoice" === $_GET['page']){
			wp_enqueue_script($this->module_id.'-pro-cta-banner',plugin_dir_url( __FILE__ ).'assets/js/pro-cta-banner.js',array('jquery'),WF_PKLIST_VERSION);
		}
		wp_enqueue_script($this->module_id.'-common',plugin_dir_url( __FILE__ ).'assets/js/common.js',array('jquery'),WF_PKLIST_VERSION);
		$params=array(
			'nonces' => array(
	            'main'=>wp_create_nonce($this->module_id),
	        ),
	        'ajax_url' => admin_url('admin-ajax.php'),
	        'order_statuses' => $order_statuses,
	        'module_base' => $this->module_base,
	        'msgs'=>array(
	        	'enter_order_id'=>__('Please enter order number','print-invoices-packing-slip-labels-for-woocommerce'),
	        	'generating'=>__('Generating','print-invoices-packing-slip-labels-for-woocommerce'),
	        	'error'=>__('Error','print-invoices-packing-slip-labels-for-woocommerce'),
	        )
		);
		$common_js_params = array(
			'order_statuses' => $order_statuses,
	        'module_base' => $this->module_base,
		);
		wp_localize_script($this->module_id,$this->module_id,$params);
		wp_localize_script($this->module_id.'-common',$this->module_id.'_common_param',$common_js_params);
		do_action('wt_pklist_add_additional_scripts',$this->module_id);
		$the_options=Wf_Woocommerce_Packing_List::get_settings($this->module_id);

	    //initializing necessary modules, the argument must be current module name/folder
	    if(!is_null($this->customizer))
		{
			$this->customizer->init($this->module_base);
		}

		$template_type = $this->module_base;
		include_once WF_PKLIST_PLUGIN_PATH.'/admin/views/premium_extension_listing.php';
		include(plugin_dir_path( __FILE__ ).'views/invoice-admin-settings.php');
	}

	/**
	* 	Enable PDF preview
	*	@since 	2.6.2
	*/
	public function enable_pdf_preview($status, $template_type)
	{
		if($template_type === $this->module_base)
		{
			$status=true;	
		}
		return $status;
	}

	/**
	* 	Declaring validation rule for form fields in settings form
	* 	@since 2.6.2
	* 	@since 4.0.5	Added the field `woocommerce_wf_add_invoice_in_customer_mail`
	* 	
	*/
	public function alter_validation_rule($arr, $base_id)
	{
		if($base_id === $this->module_id)
		{
			$arr=array(
	        	'woocommerce_wf_generate_for_orderstatus'=>array('type'=>'text_arr'),
	        	'woocommerce_wf_attach_'.$this->module_base=>array('type'=>'text_arr'),
	        	'wf_'.$this->module_base.'_contactno_email'=>array('type'=>'text_arr'),
	        	'wf_woocommerce_invoice_show_print_button'=>array('type'=>'text_arr'),
	        	'woocommerce_wf_Current_Invoice_number'=>array('type'=>'int'),
				'woocommerce_wf_invoice_start_number'=>array('type'=>'int'),
				'woocommerce_wf_invoice_padding_number'=>array('type'=>'int'),
				'wf_woocommerce_invoice_show_print_button'=>array('type'=>'text_arr'),
				'woocommerce_wf_add_invoice_in_customer_mail'=>array('type'=>'text_arr'),
			);

		}
		return $arr;
	}

	/**
	*	Declaring multi select form fields in settings form 
	* 	@since 2.6.2
	* 	@since 4.0.5 Added the field `woocommerce_wf_add_invoice_in_customer_mail`
	* 	
	*/
	public function alter_multi_select_fields($arr, $base_id)
	{
		if($base_id === $this->module_id)
		{
			$arr=array(
				'wf_'.$this->module_base.'_contactno_email'=>array(),
	        	'woocommerce_wf_generate_for_orderstatus'=>array(),
	        	'woocommerce_wf_attach_'.$this->module_base=>array(),
	        	'wf_woocommerce_invoice_show_print_button'=>array(),
	        	'woocommerce_wf_add_invoice_in_customer_mail' => array(),
	        );
		}
		return $arr;
	}

	/**
	* 	@since 2.5.8
	* 	Hook the tooltip data to main tooltip array
	*/
	public function register_tooltips($tooltip_arr)
	{
		include(plugin_dir_path( __FILE__ ).'data/data.tooltip.php');
		$tooltip_arr[$this->module_id]=$arr;
		return $tooltip_arr;
	}


	/**
	* Adding received seal filters and other options
	*	@since 	2.5.5
	*/
	public function alter_final_template_html($html,$template_type,$order)
	{
		if($template_type === $this->module_base)
		{ 
			$is_enable_received_seal=true;
			$is_enable_received_seal=apply_filters('wf_pklist_toggle_received_seal',$is_enable_received_seal,$template_type,$order);
			if(true !== $is_enable_received_seal) //hide it
			{
				$html=Wf_Woocommerce_Packing_List_CustomizerLib::addClass('wfte_received_seal',$html,Wf_Woocommerce_Packing_List_CustomizerLib::TO_HIDE_CSS);
			}
		}
		return $html;
	}

	/**
	* Adding received seal extra text
	*	@since 	2.5.5
	*/
	private static function set_received_seal_extra_text($find_replace,$template_type,$html,$order)
	{
		if(false !== strpos($html,'[wfte_received_seal_extra_text]')) //if extra text placeholder exists then only do the process
        {
        	$extra_text='';
        	$find_replace['[wfte_received_seal_extra_text]']=apply_filters('wf_pklist_received_seal_extra_text',$extra_text,$template_type,$order);
		}
		return $find_replace;
	}

	/**
	* Adding customizer info text for received seal
	*	@since 	2.5.5
	*/
	public function alter_customize_info_text($info_text,$type,$template_type)
	{
		if($template_type === $this->module_base)
		{
			if("received_seal" === $type)
			{
				$info_text=sprintf(__('You can control the visibility of the seal according to order status via filters. See filter documentation %s here. %s', 'print-invoices-packing-slip-labels-for-woocommerce'), '<a href="'.admin_url('admin.php?page=wf_woocommerce_packing_list#wf-help#filters').'" target="_blank">', '</a>');
			}
		}
		return $info_text;
	}


	/**
	* Adding received seal customization options to customizer
	*	@since 	2.5.5
	*/
	public function alter_customize_inputs($fields,$type,$template_type)
	{
		if($template_type === $this->module_base)
		{
			if("received_seal" === $type)
			{
				$fields=array(			
					array(
						'label'=>__('Width','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'width',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',			
					), 
					array(
						'label'=>__('Height','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'height',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',
						'float'=>'right',
					),
					array(
						'label'=>__('Text','print-invoices-packing-slip-labels-for-woocommerce'),
						'css_prop'=>'html',
						'trgt_elm'=>$type.'_text',
						'width'=>'49%',					
					), 
					array(
						'label'=>__('Font size','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'font-size',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',
						'float'=>'right',
					),					
					array(
						'label'=>__('Border width','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'border-top-width|border-right-width|border-bottom-width|border-left-width',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',						
					),
					array(
						'label'=>__('Line height','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'line-height',
						'trgt_elm'=>$type,
						'width'=>'49%',
						'float'=>'right',
					),
					array(
						'label'=>__('Opacity','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'select',
						'select_options'=>array(
							'1'=>1,
							'0.9'=>.9,
							'0.8'=>.8,
							'0.7'=>.7,
							'0.6'=>.6,
							'0.5'=>.5,
							'0.4'=>.4,
							'0.3'=>.3,
							'0.2'=>.2,
							'0.1'=>.1,
							'0'=>0,
						),
						'css_prop'=>'opacity',
						'trgt_elm'=>$type,
						'width'=>'49%',
						'event_class'=>'wf_cst_change',						
					),
					array(
						'label'=>__('Border radius','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'border-top-left-radius|border-top-right-radius|border-bottom-left-radius|border-bottom-right-radius',
						'trgt_elm'=>$type,
						'width'=>'49%',
						'float'=>'right',
					),
					array(
						'label'=>__('From left','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'margin-left',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',						
					),
					array(
						'label'=>__('From top','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'margin-top',
						'trgt_elm'=>$type,
						'width'=>'49%',
						'float'=>'right',
					),
					array(
						'label'=>__('Angle','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'rotate',
						'trgt_elm'=>$type,
						'unit'=>'deg',						
					),
					array(
						'label'=>__('Color','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'color',
						'css_prop'=>'border-top-color|border-right-color|border-bottom-color|border-left-color|color',
						'trgt_elm'=>$type,
						'event_class'=>'wf_cst_click',
					)
				);
			}
		}
		return $fields;
	}

	/**
	*	Generate invoice number on order creation, If user set status to generate invoice number
	*	@since 2.5.4
	*	@since 2.8.0 - Added option to not generate the invoice number for free orders
	*
	*/
	public function generate_invoice_number_on_order_creation($order_id)
	{
		if(!$order_id){
        	return;
    	}

    	// Allow code execution only once 
    	if(!get_post_meta($order_id,'_wt_thankyou_action_done',true))
    	{
    		// Get an instance of the WC_Order object
        	$order=wc_get_order($order_id);
        	$status=get_post_status($order_id);

        	$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);
        	$invoice_creation = 1;

        	if("No" === $free_order_enable){
				if(0 === \intval($order->get_total())){
					$invoice_creation = 0;
				}
			}

			if(1 === $invoice_creation){
				$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
				$force_generate=in_array($status,$generate_invoice_for) ? true :false;		
				if(true === $force_generate) //only generate if user set status to generate invoice
				{
					self::generate_invoice_number($order,$force_generate);
				}
			}
        	//add post meta, prevent to fire thankyou hook multiple times
        	add_post_meta($order_id,'_wt_thankyou_action_done',true,true); 
    	}
	}

	/**
	 * @since 2.8.3
	 * Generate the invoice number when order status changes
	 */
	public function generate_invoice_number_on_status_change($order_id,$old_status,$new_status){
		if(!$order_id){
        	return;
    	}
    	$status=get_post_status($order_id);
    	$order=wc_get_order($order_id);

    	$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);
    	$invoice_creation = 1;

    	if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				$invoice_creation = 0;
			}
		}

		if(1 === $invoice_creation){
			$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
			$force_generate=in_array($status,$generate_invoice_for) ? true :false;	
			if(true === $force_generate) //only generate if user set status to generate invoice
			{
				self::generate_invoice_number($order,$force_generate);
			}
		}
	}
	/**
	 *  Items needed to be converted to design view for the customizer screen
	 */
	public function convert_to_design_view_html($find_replace,$html,$template_type)
	{	
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$template_type,false,$template_type);
		if($template_type === $this->module_base && !$is_pro_customizer)
		{
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_billing_address($find_replace,$template_type);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_product_table($find_replace,$template_type,$html);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_charge_fields($find_replace,$template_type,$html);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_other_data($find_replace,$template_type,$html);
			$find_replace['[wfte_received_seal_extra_text]']='';
		}
		return $find_replace;
	}

	/**
	 *  Items needed to be converted to HTML for print/download
	 */
	public function generate_template_html($find_replace,$html,$template_type,$order,$box_packing=null,$order_package=null)
	{	
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$template_type,false,$template_type);
		if($template_type === $this->module_base && !$is_pro_customizer)
		{
			//Generate invoice number while printing invoice
			self::generate_invoice_number($order);

			$find_replace=$this->set_other_data($find_replace,$template_type,$html,$order);

			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_billing_address($find_replace,$template_type,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_product_table($find_replace,$template_type,$html,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_charge_fields($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_other_data($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_order_data($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_fields($find_replace,$template_type,$html,$order);
			$find_replace=self::set_received_seal_extra_text($find_replace,$template_type,$html,$order);
		}
		
		return $find_replace;
	}

	public function run_necessary()
	{
		$this->wf_filter_email_attach_invoice_for_status();
	}

	/**
	* 	@since 2.8.1
	* 	Added the filters to edit the invoice data when refunds
	* 
	*/ 
	public function set_other_data($find_replace, $template_type, $html, $order)
	{	
		add_filter('wf_pklist_alter_item_quantiy', array($this, 'alter_quantity_column'), 1, 5);
		add_filter('wf_pklist_alter_item_total_formated', array($this, 'alter_total_price_column'), 1, 7);
		add_filter('wf_pklist_alter_subtotal_formated', array($this, 'alter_sub_total_row'), 1, 5);
		add_filter('wf_pklist_alter_taxitem_amount',array($this,'alter_extra_tax_row'),1,4);
		add_filter('wf_pklist_alter_total_fee',array($this,'alter_fee_row'),1,5);
		add_filter('wf_pklist_alter_shipping_method',array($this,'alter_shipping_row'),1,4);
		add_filter('wf_pklist_alter_tax_data',array($this,'alter_tax_data'),1,4);

		// Filter for deleted product rows
		add_filter('wf_pklist_alter_item_quantiy_deleted_product',array($this,'alter_quantity_column_deleted_product'),1,4);
		add_filter('wf_pklist_alter_item_total_formated_deleted_product',array($this,'alter_total_price_column_deleted_product'),1,6);

		return $find_replace; 
	}

	public function alter_tax_data($tax_items_total,$tax_items,$order,$template_type)
	{	
		$all_refunds = $order->get_refunds();
		$new_tax = 0;
		if(!empty($all_refunds)){
			// get refund tax from all line items
			foreach($order->get_items() as $item_id => $item){
				if(is_array($tax_items) && count($tax_items)>0)
				{
					foreach($tax_items as $tax_item)
					{
						$tax_rate_id = $tax_item->rate_id;
						$new_tax += $order->get_tax_refunded_for_item($item_id,$tax_rate_id,'line_item');
					}
				}
			}
			// get refund tax from fee and shipping
			foreach($all_refunds as $refund_order){
				if(is_array($tax_items) && count($tax_items)>0)
				{
					foreach($tax_items as $tax_item)
					{
						$tax_rate_id = $tax_item->rate_id;
						// fee details
						$fee_details=$refund_order->get_items('fee');
						if(!empty($fee_details)){
				        	$fee_ord_arr = array();
				        	foreach($fee_details as $fee => $fee_value){
			                    $fee_order_id = $fee;
			                    if(!in_array($fee_order_id,$fee_ord_arr)){
			                    	$fee_taxes = $fee_value->get_taxes();
			                    	$new_tax += abs(isset( $fee_taxes['total'][ $tax_rate_id ] ) ? (float) $fee_taxes['total'][ $tax_rate_id ] : 0);
			                        $fee_ord_arr[] = $fee_order_id;
			                    }
			                }
				        }
				        // shipping details
				        $shipping_details=$refund_order->get_items('shipping');
				        if(!empty($shipping_details)){
				        	$shipping_ord_arr = array();
				        	foreach($shipping_details as $ship => $shipping_value){
			                    $ship_order_id = $ship;
			                    if(!in_array($ship_order_id,$shipping_ord_arr)){
			                    	$shipping_taxes = $shipping_value->get_taxes();
			                    	$new_tax += abs(isset( $shipping_taxes['total'][ $tax_rate_id ] ) ? (float) $shipping_taxes['total'][ $tax_rate_id ] : 0);
			                        $shipping_ord_arr[] = $ship_order_id;
			                    }
			                }
				        }
					}
				}
			}
		}

		if($new_tax > 0){
			$tax_items_total = (float)$tax_items_total - (float)$new_tax;
		}
		return $tax_items_total;
	}
	/**
	*	@since 2.8.1
	*	Alter total price of order item if the item is refunded
	*	
	*/
	public function alter_total_price_column($product_total_formated, $template_type, $product_total, $_product, $order_item, $order,$incl_tax)
	{	
		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			$item_id = $order_item->get_id();
			$new_total=(float)$order->get_total_refunded_for_item($item_id);
			$new_tax = 0;
			if(true === $incl_tax){
				$tax_items = $order->get_tax_totals();
				if(is_array($tax_items) && count($tax_items)>0)
				{
					foreach($tax_items as $tax_item)
					{
						$tax_rate_id = $tax_item->rate_id;
						$new_tax += $order->get_tax_refunded_for_item($item_id,$tax_rate_id,'line_item');
					}
				}
			}
			$new_total += $new_tax;
			if($new_total>0)
			{	
				$old_product_formated = '<strike>'.$product_total_formated.'</strike>';
				$wc_version=WC()->version;
				$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
				$user_currency=get_post_meta($order_id,'_order_currency',true);
				$new_total = (float)$product_total - $new_total;
				$product_total_formated = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_total);
				$product_total_formated = apply_filters('wf_pklist_alter_price_to_negative',$product_total_formated,$template_type,$order);
				$product_total_formated = '<span style="">'.$old_product_formated.' '.$product_total_formated.'</span>';
			}
		}
		return $product_total_formated;
	}

	/**
	*	@since 2.8.3
	*	Alter total price of order item if the item is refunded
	*	
	*/
	public function alter_total_price_column_deleted_product($product_total_formated, $template_type, $product_total, $order_item, $order,$incl_tax)
	{	
		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			$item_id = $order_item->get_id();
			$new_total=(float)$order->get_total_refunded_for_item($item_id);
			$new_tax = 0;
			if($incl_tax == true){
				$tax_items = $order->get_tax_totals();
				if(is_array($tax_items) && count($tax_items)>0)
				{
					foreach($tax_items as $tax_item)
					{
						$tax_rate_id = $tax_item->rate_id;
						$new_tax += $order->get_tax_refunded_for_item($item_id,$tax_rate_id,'line_item');
					}
				}
			}
			$new_total += $new_tax;
			if($new_total>0)
			{	
				$old_product_formated = '<strike>'.$product_total_formated.'</strike>';
				$wc_version=WC()->version;
				$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
				$user_currency=get_post_meta($order_id,'_order_currency',true);
				$new_total = (float)$product_total - $new_total;
				$product_total_formated = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_total);
				$product_total_formated = apply_filters('wf_pklist_alter_price_to_negative',$product_total_formated,$template_type,$order);
				$product_total_formated = '<span style="">'.$old_product_formated.' '.$product_total_formated.'</span>';
			}
		}
		return $product_total_formated;
	}

	/**
	*	@since 2.8.1
	*	Alter quantity of order item if the item is refunded
	*	
	*/
	public function alter_quantity_column($qty, $template_type, $_product, $order_item, $order)
	{
		$item_id = $order_item->get_id();
		$new_qty=$order->get_qty_refunded_for_item($item_id);
		if($new_qty<0)
		{
			$qty='<del>'.$qty.'</del> &nbsp; <ins>'.($qty+$new_qty).'</ins>';
		}
		return $qty;
	}

	/**
	*	@since 2.8.3
	*	Alter quantity of order item if the item is refunded
	*	
	*/
	public function alter_quantity_column_deleted_product($qty, $template_type, $order_item, $order)
	{
		$item_id = $order_item->get_id();
		$new_qty=$order->get_qty_refunded_for_item($item_id);
		if($new_qty<0)
		{
			$qty='<del>'.$qty.'</del> &nbsp; <ins>'.($qty+$new_qty).'</ins>';
		}
		return $qty;
	}

	/**
	*	@since 2.8.2
	*	Alter subtotal row in product table, if any refund
	*	
	*/
	public function alter_sub_total_row($sub_total_formated, $template_type, $sub_total, $order, $incl_tax){
		$wc_version=WC()->version;
		$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
		$user_currency=get_post_meta($order_id,'_order_currency',true);
		$new_total = 0;
		$new_tax = 0;
		$decimal = Wf_Woocommerce_Packing_List_Admin::wf_get_decimal_price($user_currency,$order);

		$incl_tax_text='';
		if(true === $incl_tax){
			$incl_tax_text=Wf_Woocommerce_Packing_List_CustomizerLib::get_tax_incl_text($template_type, $order, 'product_price');
			$incl_tax_text=("" !== $incl_tax_text ? ' ('.$incl_tax_text.')' : $incl_tax_text);
		}
		$sub_total_formated = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$sub_total);
		
		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			foreach($all_refunds as $refund_order){
				foreach ($refund_order->get_items() as $item_id => $item ) {
					
					$new_total += (float)$item->get_subtotal();
					if($incl_tax == true){
						$tax_items = $order->get_tax_totals();
						if(is_array($tax_items) && count($tax_items)>0)
						{
							foreach($tax_items as $tax_item)
							{
								$tax_rate_id = $tax_item->rate_id;
							 	$refund_tax = $item->get_taxes();
		            			$new_tax += isset( $refund_tax['total'][ $tax_rate_id ] ) ? (float) $refund_tax['total'][ $tax_rate_id ] : 0;
							}
						}
					
					}
				}
			}
			$new_total += $new_tax;
			if($new_total < 0){
				$new_total = $sub_total - abs((float)$new_total);
				$new_sub_total_formated = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_total);
				$sub_total_formated = '<span style=""><strike>'.$sub_total_formated.'</strike> '.$new_sub_total_formated.'</span>';
			}
		}
		$sub_total_formated = apply_filters('wf_pklist_alter_price_to_negative',$sub_total_formated,$template_type,$order);
		return $sub_total_formated.$incl_tax_text;
	}

	/**
	*	@since 2.8.2
	*	Alter Individual tax rows in product table, if any refund
	*	
	*/
	public function alter_extra_tax_row($tax_amount, $tax_item, $order, $template_type){
		$wc_version=WC()->version;
		$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
		$user_currency=get_post_meta($order_id,'_order_currency',true);
		$tax_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_taxstatus');
		$incl_tax=in_array('in_tax', $tax_type);
		$new_tax_amount = 0;
		$all_refunds = $order->get_refunds();
		$tax_rate_id = $tax_item->rate_id;
		$shipping = 0;

		if(!empty($all_refunds)){
			foreach($all_refunds as $refund_order){
				foreach($refund_order->get_items() as $refunded_item_id => $refunded_item){
		            $refund_tax = $refunded_item->get_taxes();
		            $new_tax_amount += isset( $refund_tax['total'][ $tax_rate_id ] ) ? (float) $refund_tax['total'][ $tax_rate_id ] : 0;
		        }

		        $fee_details=$refund_order->get_items('fee');
		        if(!empty($fee_details)){
		        	$fee_ord_arr = array();
		        	foreach($fee_details as $fee => $fee_value){
	                    $fee_order_id = $fee;
	                    if(!in_array($fee_order_id,$fee_ord_arr)){
	                    	$fee_taxes = $fee_value->get_taxes();
	                    	$new_tax_amount += isset( $fee_taxes['total'][ $tax_rate_id ] ) ? (float) $fee_taxes['total'][ $tax_rate_id ] : 0;
	                        $fee_ord_arr[] = $fee_order_id;
	                    }
	                }
		        }
		        $shipping_details=$refund_order->get_items('shipping');
		        if(!empty($shipping_details)){
		        	$shipping_ord_arr = array();
		        	foreach($shipping_details as $ship => $shipping_value){
	                    $ship_order_id = $ship;
	                    if(!in_array($ship_order_id,$shipping_ord_arr)){
	                    	$shipping_taxes = $shipping_value->get_taxes();
	                    	$new_tax_amount += isset( $shipping_taxes['total'][ $tax_rate_id ] ) ? (float) $shipping_taxes['total'][ $tax_rate_id ] : 0;
	                        $shipping_ord_arr[] = $ship_order_id;
	                    }
	                }
		        }
		        $refund_id = $wc_version<'2.7.0' ? $refund_order->id : $refund_order->get_id();
			}

			if($new_tax_amount < 0){
				$new_tax_amount = $tax_item->amount - abs((float)$new_tax_amount);
				$new_tax_amount_formatted = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_tax_amount);
				$tax_amount = '<span><strike>'.$tax_amount.'</strike> '.$new_tax_amount_formatted.'</span>';
			}
		}
		return $tax_amount;	
	}

	/**
	*	@since 2.8.2
	*	Alter Fee row in product table, if any refund
	*	
	*/
	public function alter_fee_row($fee_total_amount_formated,$template_type,$fee_total_amount,$user_currency,$order){
		$incl_tax_text = '';
		$tax_display = get_option( 'woocommerce_tax_display_cart' );
		$tax_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_taxstatus');
		$incl_tax=in_array('in_tax', $tax_type);
		$tax_items = $order->get_tax_totals();

		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			$new_fee_total_amount = 0;
			foreach($all_refunds as $refund_order){
				$fee_details=$refund_order->get_items('fee');
				if(!empty($fee_details)){
					$fee_ord_arr = array();
	                foreach($fee_details as $fee => $fee_value){
	                    $fee_order_id = $fee;
	                    if(!in_array($fee_order_id,$fee_ord_arr)){
	                    	$fee_line_total = function_exists('wc_get_order_item_meta') ? wc_get_order_item_meta($fee_order_id,'_line_total',true) : $order->get_item_meta($fee_order_id, '_line_total', true);
	                        $new_fee_total_amount += (float)$fee_line_total;
	                        if($incl_tax){
	                        	foreach($tax_items as $tax_item)
								{	
									$tax_rate_id = $tax_item->rate_id;
									$fee_taxes = $fee_value->get_taxes();
	                        		$new_fee_total_amount += isset( $fee_taxes['total'][ $tax_rate_id ] ) ? (float) $fee_taxes['total'][ $tax_rate_id ] : 0;
								}
	                        }
	                        $fee_ord_arr[] = $fee_order_id;
	                    }
	                }
				}
			}
			if($new_fee_total_amount < 0){
				$new_fee_total_amount = (float)$fee_total_amount - abs((float)$new_fee_total_amount);
				$new_fee_total_amount_formatted = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_fee_total_amount);
				$fee_total_amount_formated = '<span><strike>'.$fee_total_amount_formated.'</strike> '.$new_fee_total_amount_formatted.'</span>';
			}
		}
		return $fee_total_amount_formated;
	}

	/**
	*	@since 2.8.2
	*	Alter Shipping amount row in product table, if any refund
	*	
	*/
	public function alter_shipping_row($shipping, $template_type, $order, $product_table){
		$wc_version=WC()->version;
		$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
		$user_currency=get_post_meta($order_id,'_order_currency',true);
		$incl_tax_text = '';
		$tax_display = get_option( 'woocommerce_tax_display_cart' );
		$tax_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_taxstatus');
		$incl_tax=in_array('in_tax', $tax_type);
		$tax_items = $order->get_tax_totals();

		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			$new_shipping_amount = 0;
			foreach($all_refunds as $refund_order){
				$refund_id = $wc_version<'2.7.0' ? $refund_order->id : $refund_order->get_id();
				$new_shipping_amount += (float) get_post_meta($refund_id,'_order_shipping',true);

				if($incl_tax){
					if(is_array($tax_items) && count($tax_items)>0)
					{
						foreach($tax_items as $tax_item)
						{	
							$tax_rate_id = $tax_item->rate_id;
							$shipping_details=$refund_order->get_items('shipping');
					        if(!empty($shipping_details)){
					        	$shipping_ord_arr = array();
					        	foreach($shipping_details as $ship => $shipping_value){
				                    $ship_order_id = $ship;
				                    if(!in_array($ship_order_id,$shipping_ord_arr)){
				                    	$shipping_taxes = $shipping_value->get_taxes();
				                    	$new_shipping_amount += isset( $shipping_taxes['total'][ $tax_rate_id ] ) ? (float) $shipping_taxes['total'][ $tax_rate_id ] : 0;
				                        $shipping_ord_arr[] = $ship_order_id;
				                    }
				                }
					        }	
						}
					}
				}
			}

			if($new_shipping_amount < 0){
				
				if (($incl_tax === false)) {
					$shipping_total_amount = (float)$order->get_shipping_total();
				}else{
					if(abs($order->get_shipping_tax()) > 0){
						$incl_tax_text=Wf_Woocommerce_Packing_List_CustomizerLib::get_tax_incl_text($template_type, $order, 'product_price');
						$incl_tax_text=($incl_tax_text!="" ? ' ('.$incl_tax_text.')' : $incl_tax_text);
					}
					$shipping_total_amount = (float)$order->get_shipping_total() + (float)$order->get_shipping_tax();
				}
				
				$new_shipping_amount = $shipping_total_amount - abs((float)$new_shipping_amount);
				$old_shipping_amount_formatted = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$shipping_total_amount);
				$new_shipping_total_amount_formatted = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_shipping_amount);
				$shipping = '<span><strike>'.$old_shipping_amount_formatted.'</strike> '.$new_shipping_total_amount_formatted.'</span>'.$incl_tax_text;
				$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_shipped_via', '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $order->get_shipping_method() ) . '</small>', $order );
			}
		}
		return $shipping;
	}

    protected static function get_orderdate_timestamp($order_id)
    {
    	$order_date=get_the_date('Y-m-d h:i:s A',$order_id);
		return strtotime($order_date);
    }

    /**
	* Get invoice date
	* @since 2.5.4
	* @return mixed
	*/
    public static function get_invoice_date($order_id,$date_format,$order)
    {
    	$invoice_date=get_post_meta($order_id,'_wf_invoice_date',true);
    	if($invoice_date)
    	{
    		return (empty($invoice_date) ? '' : date_i18n($date_format,$invoice_date));
    	}else
    	{
    		if(self::$return_dummy_invoice_number)
	    	{
	    		return date_i18n($date_format);
	    	}else
	    	{
	    		return '';
	    	}
    	}
    }

    public static function generate_invoice_number($order, $force_generate=true,$free_ord="") 
    {	
    	$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    $wf_invoice_id = get_post_meta($order_id, 'wf_invoice_number', true);
	    if((empty($wf_invoice_id)) && ("set" !== $free_ord)){
	    	$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',self::$module_id_static);
			if("No" === $free_order_enable){
				if(0 === \intval($order->get_total())){
					return '';
				}
			}
	    }

	    if(class_exists('Wf_Woocommerce_Packing_List_Sequential_Number'))
	    {
	    	return Wf_Woocommerce_Packing_List_Sequential_Number::generate_sequential_number($order, self::$module_id_static, array('number'=>'wf_invoice_number', 'date'=>'wf_invoice_date', 'enable'=>'woocommerce_wf_enable_invoice'), $force_generate);
	    }else
	    {
	    	return '';
	    }
	}

	/**
	 * Function to add "Invoice" column in order listing page
	 *
	 * @since    2.5.0
	 */
	public function add_invoice_column($columns)
	{
		$columns['Invoice']=__('Invoice','print-invoices-packing-slip-labels-for-woocommerce');
        return $columns;
	}

	/**
	 * Function to add value in "Invoice" column
	 *
	 * @since    2.5.0
	 */
	public function add_invoice_column_value($column)
	{
		global $post, $woocommerce, $the_order;
		if("Invoice" === $column)
		{
			$order=wc_get_order($post->ID);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
			$force_generate=in_array(get_post_status($order_id),$generate_invoice_for) ? true :false;	
			$wf_invoice_id = get_post_meta($order_id, 'wf_invoice_number', true);
			echo $wf_invoice_id;
		}
	}

	public function sort_invoice_column($columns){
		$columns['Invoice'] = __('Invoice','print-invoices-packing-slip-labels-for-woocommerce');
    	return $columns;
	}

	/**
	 * @since 3.0.5
	 * [Fix] - Function to generate invoice number in ascending order by order date
	 */ 
	public function generate_auto_invoice_number()
	{
		$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
		if(!empty($generate_invoice_for)){
			$empty_invoice_order_ids = Wf_Woocommerce_Packing_List_Admin::get_order_ids_for_invoice_number_generation($this->module_id);
			if(!empty($empty_invoice_order_ids)){
				foreach($empty_invoice_order_ids as $this_order_id){
					$order_id = (int)$this_order_id;
					$order=wc_get_order($order_id);
					$wf_invoice_id = get_post_meta($order_id,'wf_invoice_number',true);
				    if(empty($wf_invoice_id))
				    {
				    	$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
						$force_generate=in_array(get_post_status($order_id),$generate_invoice_for) ? true :false;
						self::generate_invoice_number($order,$force_generate);
				    }
				}
				update_option('invoice_empty_count',0);
			}
		}else{
			update_option('invoice_empty_count',0);
		}
	}

	/**
	 * removing status other than generate invoice status
	 * @since 	2.5.0
	 * @since 	2.5.3 [Bug fix] array intersect issue when order status is empty 	
	 */
	private function wf_filter_email_attach_invoice_for_status()
	{
		$the_options=Wf_Woocommerce_Packing_List::get_settings($this->module_id);
		$email_attach_invoice_for_status=$the_options['woocommerce_wf_attach_invoice'];
		$generate_for_orderstatus=$the_options['woocommerce_wf_generate_for_orderstatus'];
		$generate_for_orderstatus=!is_array($generate_for_orderstatus) ? array() : $generate_for_orderstatus;
		$email_attach_invoice_for_status=!is_array($email_attach_invoice_for_status) ? array() : $email_attach_invoice_for_status;
		$the_options['woocommerce_wf_attach_invoice']=array_intersect($email_attach_invoice_for_status,$generate_for_orderstatus);
		Wf_Woocommerce_Packing_List::update_settings($the_options,$this->module_id);
	}

	public function get_customizable_items($settings,$base_id)
	{
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$this->module_base,false,$this->module_base);
		if($base_id === $this->module_id)
		{
			$only_pro_html='<span style="color:red;"> ('.__('Pro version','print-invoices-packing-slip-labels-for-woocommerce').')</span>';
			$only_pro_addon_html='<span style="color:red;"> ('.__('Pro Add-on','print-invoices-packing-slip-labels-for-woocommerce').')</span>';
			//these fields are the classname in template Eg: `company_logo` will point to `wfte_company_logo`
			
			$settings = array(
				'doc_title'=>__('Document title','print-invoices-packing-slip-labels-for-woocommerce'),
				'company_logo'=>__('Company Logo / Name','print-invoices-packing-slip-labels-for-woocommerce'),
				//'barcode_disabled'=>__('Barcode','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'invoice_number'=>__('Invoice Number','print-invoices-packing-slip-labels-for-woocommerce'),
				'order_number'=>__('Order Number','print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_date'=>__('Invoice Date','print-invoices-packing-slip-labels-for-woocommerce'),
				'order_date'=>__('Order Date','print-invoices-packing-slip-labels-for-woocommerce'),
				'product_table'=>__('Product Table','print-invoices-packing-slip-labels-for-woocommerce'),
				'from_address'=>__('From Address','print-invoices-packing-slip-labels-for-woocommerce'),
				'billing_address'=>__('Billing Address','print-invoices-packing-slip-labels-for-woocommerce'),
				'shipping_address'=>__('Shipping Address','print-invoices-packing-slip-labels-for-woocommerce'),
				'email'=>__('Email Field','print-invoices-packing-slip-labels-for-woocommerce'),
				'tel'=>__('Tel Field','print-invoices-packing-slip-labels-for-woocommerce'),
				//'shipping_method'=>__('Shipping Method','print-invoices-packing-slip-labels-for-woocommerce'),
				'received_seal'=>__('Payment received stamp','print-invoices-packing-slip-labels-for-woocommerce'),
			);

			$template_type=$this->module_base;
			$show_qrcode_placeholder = apply_filters('wt_pklist_show_qrcode_placeholder_in_template',false,$template_type);
			$settings['barcode'] = __('Barcode','print-invoices-packing-slip-labels-for-woocommerce');
			if(!$show_qrcode_placeholder){
				$settings['footer'] = __('Footer','print-invoices-packing-slip-labels-for-woocommerce');
				$settings['qrcode_disabled'] = __('QR code','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_addon_html;
			}else{
				if(!$is_pro_customizer){
					$settings['barcode'] = __('Barcode / QR code','print-invoices-packing-slip-labels-for-woocommerce');
				}
				$settings['footer'] = __('Footer','print-invoices-packing-slip-labels-for-woocommerce');
			}
			

			$pro_features = array(
				'tracking_number_disabled'=>__('Tracking Number','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'product_table_subtotal_disabled'=>__('Subtotal','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'product_table_shipping_disabled'=>__('Shipping','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'product_table_cart_discount_disabled'=>__('Cart Discount','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'product_table_order_discount_disabled'=>__('Order Discount','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'product_table_total_tax_disabled'=>__('Total Tax','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'product_table_fee_disabled'=>__('Fee','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'product_table_coupon_disabled'=>__('Coupon info','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'product_table_payment_method_disabled'=>__('Payment Method','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'product_table_payment_total_disabled'=>__('Total','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
			);
			

			$settings = array_merge($settings, $pro_features);

			//these fields are the classname in template Eg: `company_logo` will point to `wfte_company_logo`
			return $settings;
		}
		return $settings;
	}

	/*
	* These are the fields that have no customizable options, Just on/off
	* 
	*/
	public function get_non_options_fields($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{	
			$template_type=$this->module_base;
			$show_qrcode_placeholder = apply_filters('wt_pklist_show_qrcode_placeholder_in_template',false,$template_type);
			if(!$show_qrcode_placeholder){
				return array(
					'barcode',
					'footer',
					'return_policy',
				);
			}else{
				return array(
					'footer',
					'return_policy',
				);
			}
		}
		return $settings;
	}

	/*
	* These are the fields that are switchable
	* 
	*/
	public function get_non_disable_fields($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{
			return array(
				'product_table_payment_summary'
			);
		}
		return $settings;
	}

	/**
	*	Default form fields and their values for invoice settings page
	* 	@since 4.0.5	Added the fields `woocommerce_wf_add_invoice_in_customer_mail`
	* 					`woocommerce_wf_add_invoice_in_admin_mail`
	* 	
	*/ 
	public function default_settings($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{
			$settings = array(
	        	'woocommerce_wf_generate_for_orderstatus'=>array('wc-completed'),
	        	'woocommerce_wf_attach_invoice'=>array(),
	        	'woocommerce_wf_packinglist_logo'=>'',
	        	'woocommerce_wf_add_invoice_in_mail'=>'No',
	        	'woocommerce_wf_add_invoice_in_admin_mail' => 'No',
	        	'woocommerce_wf_packinglist_frontend_info'=>'Yes',
	        	'woocommerce_wf_invoice_number_format'=>"[number]",
				'woocommerce_wf_Current_Invoice_number'=>1,
				'woocommerce_wf_invoice_start_number'=>1,
				'woocommerce_wf_invoice_number_prefix'=>'',
				'woocommerce_wf_invoice_padding_number'=>0,
				'woocommerce_wf_invoice_number_postfix'=>'',
				'woocommerce_wf_invoice_as_ordernumber'=>"Yes",
				'woocommerce_wf_enable_invoice'=>"Yes",
				'woocommerce_wf_add_customer_note_in_invoice'=>"No", //Add customer note
				'woocommerce_wf_packinglist_variation_data'=>'Yes', //Add product variation data
				'wf_'.$this->module_base.'_contactno_email'=>array('contact_number', 'email', 'vat'),
				'woocommerce_wf_orderdate_as_invoicedate'=>"Yes",
				'woocommerce_wf_custom_pdf_name' => '[prefix][order_no]',/* Since 2.8.0 */
				'woocommerce_wf_custom_pdf_name_prefix' => 'Invoice_',/* Since 2.8.0 */
				'wf_woocommerce_invoice_free_orders' => 'Yes',
	        	'wf_woocommerce_invoice_free_line_items' => 'Yes', /* Since 2.8.0 , To display the free line items*/
	        	'wf_woocommerce_invoice_prev_install_orders' => 'No',
	        	'wt_pklist_total_tax_column_display_option' => 'amount',
	        	'wf_woocommerce_invoice_show_print_button' => array('order_listing','order_details','order_email'),
	        	'woocommerce_wf_add_invoice_in_customer_mail' => array(),
			);
			return $settings;
		}else
		{
			return $settings;
		}
	}

	/*
	*	@since v3.0.3 - Changed the radio button fields to checkbox
	*	This function is for getting the values for checkbox fields when they are unchecked,
	*	since the PHP will sent the $_POST the unchecked fields.
	*/
	public function single_checkbox_fields($settings,$base_id,$tab_name){
		if($base_id === $this->module_id)
		{	
			// array of fields with their unchecked values.
			$settings['wt_invoice_general'] = array(
				'woocommerce_wf_enable_invoice'						=> "No",
				'woocommerce_wf_add_'.$this->module_base.'_in_mail'	=> "No",
				'woocommerce_wf_add_invoice_in_admin_mail' 		=> "No",
				'woocommerce_wf_packinglist_frontend_info'			=> "Yes",
				'wf_woocommerce_invoice_prev_install_orders' 		=> "No",
				'wf_woocommerce_invoice_free_orders' 				=> "No",
				'wf_woocommerce_invoice_free_line_items'			=> "No",
			);
		}

		return $settings;
	}

	/*
	*	@since v3.0.5 - Changed the radio button fields to multi checkbox
	*	This function is for getting the values for checkbox fields when they are unchecked,
	*	since the PHP will sent the $_POST the unchecked fields.
	*/
	public function multi_checkbox_fields($settings, $base_id,$tab_name){
		if($base_id === $this->module_id){
			$settings['wt_invoice_general'] = array(
				'wf_'.$this->module_base.'_contactno_email'		=> array(),
				'wf_woocommerce_invoice_show_print_button'		=> array(),
				'woocommerce_wf_generate_for_orderstatus' 		=> array(),
				'woocommerce_wf_add_invoice_in_customer_mail' 	=> array(),
			);
		}
		return $settings;
	}

	public function save_multi_checkbox_fields($result,$key,$fields,$base_id){
		if($base_id === $this->module_id){
			$result = (isset($fields[$key]) && !isset($_POST[$key])) ? $fields[$key] : $result;
		}
		return $result;
	}

	public function add_bulk_print_buttons($actions)
	{
		$actions['print_invoice']=__('Print Invoices','print-invoices-packing-slip-labels-for-woocommerce');
		$actions['download_invoice']=__('Download Invoices','print-invoices-packing-slip-labels-for-woocommerce');
		return $actions;
	}

	/**
	*	Adding print/download options in Order list/detail page
	*	@since 4.0.0 Show the prompt for free orders, when no invoice number for the free order
	*/
	public function add_print_buttons($item_arr, $order, $order_id, $button_location)
	{
		$invoice_number=self::generate_invoice_number($order,false);
		$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);
		$is_show=0;
		$is_show_prompt=1;

		if(in_array(get_post_status($order_id), $generate_invoice_for) || !empty($invoice_number))
        {
        	$is_show_prompt=0;
        	$is_show=1;
		}else
		{
			if(empty($invoice_number))
			{
				$is_show_prompt=1;
				$is_show=1;
			}
		}

		if(empty($invoice_number))
		{
			if("No" === $free_order_enable){
				if(0 === \intval($order->get_total())){
					$is_show_prompt=2;
				}
			}
		}

		if(1 === $is_show)
		{
			//for print button
			$btn_args=array(  
				'action'=>'print_invoice',
				'tooltip'=>__('Print Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_show_prompt'=>$is_show_prompt,
				'button_location'=>$button_location,						
			);

			//for download button
			$btn_args_dw=array(
				'action'=>'download_invoice',
				'tooltip'=>__('Download Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_show_prompt'=>$is_show_prompt,
				'button_location'=>$button_location,
			);

			if($button_location=='detail_page')
			{
				$btn_args['label']=__('Print','print-invoices-packing-slip-labels-for-woocommerce');
				$btn_args_dw['label']=__('Download','print-invoices-packing-slip-labels-for-woocommerce');

				$item_arr['invoice_details_actions']=array(
					'button_type'=>'aggregate',
					'button_key'=>'invoice_actions', //unique if multiple on same page
					'button_location'=>$button_location,
					'action'=>'',
					'label'=>__('Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
					'tooltip'=>__('Print/Download Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
					'is_show_prompt'=>0, //always 0
					'items'=>array(
						'print_invoice' => $btn_args,
						'download_invoice' => $btn_args_dw
					),
				);
			}else
			{
				$btn_args['label']=__('Print Invoice','print-invoices-packing-slip-labels-for-woocommerce');
				$btn_args_dw['label']=__('Download Invoice','print-invoices-packing-slip-labels-for-woocommerce');
				$item_arr[]=$btn_args;
				$item_arr[]=$btn_args_dw;			
			}
		}
		return $item_arr;
	}

	public function add_docdata_metabox($data_arr, $order, $order_id)
	{
		
		$invoice_number=self::generate_invoice_number($order, false);
		if("" !== $invoice_number)
		{
			$data_arr['wf_meta_box_invoice_number']=array(
				'label'=>__('Invoice Number','print-invoices-packing-slip-labels-for-woocommerce'),
				'value'=>$invoice_number,
			);
		}
		return $data_arr;
	}

	/**
	*	@since 2.8.0 - Added option to not generate the invoice number for free orders
	*
	*/
	public function add_email_attachments($attachments, $order, $order_id, $email_class_id)
	{ 		
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);

		if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				return $attachments;
			}
		}

		$attach_pdf_to_customer_email = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_add_invoice_in_customer_mail', $this->module_id);

		if("Yes" === Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_add_'.$this->module_base.'_in_admin_mail', $this->module_id) || !empty($attach_pdf_to_customer_email))
        {
        	/* check order email types */		
			$attach_to_mail_for=array('new_order', 'customer_completed_order', 'customer_invoice', 'customer_on_hold_order', 'customer_processing_order');
			/* check order email types for renewal order */
			$attach_to_mail_for=array_merge($attach_to_mail_for,array('new_renewal_order','customer_renewal_invoice','customer_completed_renewal_order','customer_on_hold_renewal_order','customer_processing_renewal_order'));
			$attach_to_mail_for=apply_filters('wf_pklist_alter_'.$this->module_base.'_attachment_mail_type', $attach_to_mail_for, $order_id, $email_class_id, $order);
			/* To avoid the duplication when using the filter */
			$attach_to_mail_for = array_unique($attach_to_mail_for);
			$is_attah = false;
			
			if(in_array($email_class_id, $attach_to_mail_for)){
				
				/* check order statuses to generate invoice */
				$generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus', $this->module_id);
				if(in_array('wc-'.$order->get_status(), $generate_invoice_for) && in_array('wc-'.$order->get_status(), $attach_pdf_to_customer_email)){
					$is_attach = true;
				}

				/* Attach PDF to admin email when new order is placed with chosen order status */
				if("Yes" === Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_add_'.$this->module_base.'_in_admin_mail', $this->module_id) && "new_order" === $email_class_id && in_array('wc-'.$order->get_status(), $generate_invoice_for)){
	                $is_attach = true;
	                $is_attach = apply_filters('wt_pklist_enable_new_order_email_attachment',$is_attach,$order,$this->module_base);
	            }
			}
			

			if($is_attach) 
			{                             	 
           		if(!is_null($this->customizer))
		        { 
		        	$order_ids=array($order_id);
		        	$pdf_name=$this->customizer->generate_pdf_name($this->module_base,$order_ids);
		        	$this->customizer->template_for_pdf=true;
		        	$html=$this->generate_order_template($order_ids,$pdf_name);
		        	$attachments[]=$this->customizer->generate_template_pdf($html,$this->module_base, $pdf_name, 'attach');
		        }
           	}
        }
        return $attachments;
	}

	/**
	*	@since 2.8.0 - Added option to not generate the invoice number for free orders
	*
	*/
	public function add_email_print_buttons($html,$order,$order_id)
	{	
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);

		if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				return $html;
			}
		}

		$template_type=$this->module_base;
		$show_print_button_pages = apply_filters('wt_pklist_show_hide_print_button_in_pages',true,'order_email',$template_type,$order);
		
		if($show_print_button_pages){
			$show_on_frontend=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_frontend_info',$this->module_id);
			$show_print_button_arr = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_show_print_button',$this->module_id);

			if(('Yes' === $show_on_frontend) && (in_array('order_email',$show_print_button_arr)))
			{
				$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    		$wf_invoice_id = get_post_meta($order_id, 'wf_invoice_number', true);
				$show_print_button_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
		        if("" !== trim($wf_invoice_id) || in_array('wc-'.$order->get_status(),$show_print_button_for))
		        {
		            Wf_Woocommerce_Packing_List::generate_print_button_for_user($order,$order_id,'print_invoice',esc_html__('Print Invoice','print-invoices-packing-slip-labels-for-woocommerce'),true); 
		        }
		    }
		}
	    return $html;
	}
	
	/**
	*	@since 2.8.0 - Added option to not generate the invoice number for free orders
	*
	*/
	public function add_frontend_print_buttons($html,$order,$order_id)
	{	
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);

		if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				return $html;
			}
		}
		$template_type=$this->module_base;
		$show_print_button_pages = apply_filters('wt_pklist_show_hide_print_button_in_pages',true,'order_details',$template_type,$order);
		
		if($show_print_button_pages){
			$show_on_frontend=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_frontend_info',$this->module_id);
			$show_print_button_arr = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_show_print_button',$this->module_id);
			if(('Yes' === $show_on_frontend) && (in_array('order_details',$show_print_button_arr)))
			{
				$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    		$wf_invoice_id = get_post_meta($order_id, 'wf_invoice_number', true);
				$generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
				if("" !== trim($wf_invoice_id) || in_array('wc-'.$order->get_status(),$generate_invoice_for)){
					Wf_Woocommerce_Packing_List::generate_print_button_for_user($order,$order_id,'print_invoice',esc_html__('Print Invoice','print-invoices-packing-slip-labels-for-woocommerce'));
				}
			}
		}
		return $html;
	}
	
	/**
	 * @since 3.0.0 
	 * Show print invoice button on the order listing page - frontend
	 */
	public function add_frontend_order_list_page_print_buttons($wt_actions, $order, $order_id)
	{
		if($this->is_show_frontend_print_button($order))
		{
			$wt_actions[$this->module_base]=array(
				'print'=>__('Print Invoice', 'print-invoices-packing-slip-labels-for-woocommerce'),
			);
		}
		return $wt_actions;
	}

	public function is_show_frontend_print_button($order)
	{	
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);

		if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				return false;
			}
		}
		$template_type=$this->module_base;
		$show_print_button_pages = apply_filters('wt_pklist_show_hide_print_button_in_pages',true,'order_listing',$template_type,$order);
		
		if($show_print_button_pages){
			$show_on_frontend=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_frontend_info',$this->module_id);
			$show_print_button_arr = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_show_print_button',$this->module_id);
			if(('Yes' === $show_on_frontend) && (in_array('order_listing',$show_print_button_arr)))
			{
				$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    		$wf_invoice_id = get_post_meta($order_id, 'wf_invoice_number', true);
				$generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
				if("" !== trim($wf_invoice_id) || in_array('wc-'.$order->get_status(),$generate_invoice_for)){
					return true;
				}
			}
		}
	    return false;
	}

	/**
	* 	Print_window for invoice
	* 	@param $orders : order ids
	*	@param $action : (string) download/preview/print
	*	@since 2.6.2 Added compatibilty preview PDF
	*/    
    public function print_it($order_ids, $action) 
    {
    	$template_type=$this->module_base;
    	if("print_invoice" === $action || "download_invoice" === $action || "preview_invoice" === $action)
    	{   
    		if("Yes" !== $this->is_enable_invoice) /* invoice not enabled so only allow preview option */
    		{
    			if("print_invoice" === $action || "download_invoice" === $action)
    			{
    				return;	
    			}else
    			{
    				if(!Wf_Woocommerce_Packing_List_Admin::check_role_access()) //Check access
	                {
	                	return;
	                }
    			}
    		}
    		if(!is_array($order_ids))
    		{
    			return;
    		}    
	        if(!is_null($this->customizer))
	        {
	        	$pdf_name=$this->customizer->generate_pdf_name($this->module_base, $order_ids);
	        	if("download_invoice" === $action || "preview_invoice" === $action)
	        	{
	        		ob_start();
	        		$this->customizer->template_for_pdf=true;

	        		if("preview_invoice" === $action)
		        	{
		        		$html=$this->customizer->get_preview_pdf_html($this->module_base);
		        		$html=$this->generate_order_template($order_ids, $pdf_name, $html);
		        	}else
		        	{
		        		$html=$this->generate_order_template($order_ids, $pdf_name);
		        	}
		        	$html = Wf_Woocommerce_Packing_List_Admin::qrcode_barcode_visibility($html,$template_type);
		        	$action=str_replace('_'.$this->module_base, '', $action);
	        		$this->customizer->generate_template_pdf($html, $this->module_base, $pdf_name, $action);
	        		ob_end_clean();
	        	}else
	        	{
	        		ob_start();
	        		$html=$this->generate_order_template($order_ids, $pdf_name,"",$action);
	        		$html = Wf_Woocommerce_Packing_List_Admin::qrcode_barcode_visibility($html,$template_type);
	        		ob_end_clean();
	        		echo $html;
	        	}
	        }else
	        {
	        	_e('Customizer module is not active.', 'print-invoices-packing-slip-labels-for-woocommerce');
	        }
	        exit();
    	}
    }
    public function generate_order_template($orders,$page_title,$html="",$action="")
    {
    	$template_type=$this->module_base;
    	if("" === $html)
    	{
    		//taking active template html
    		$html=$this->customizer->get_template_html($template_type);
    	}
    	$style_blocks=$this->customizer->get_style_blocks($html);
    	$html=$this->customizer->remove_style_blocks($html,$style_blocks);
    	$out='';
    	if("" !== $html)
    	{
    		$number_of_orders=count($orders);
			$order_inc=0;
			foreach($orders as $order_id)
			{
				$order_inc++;
				$order=( WC()->version < '2.7.0' ) ? new WC_Order($order_id) : new wf_order($order_id);
				if(count($orders)>1)
				{
					Wf_Woocommerce_Packing_List_Invoice::generate_invoice_number($order,true,'set');
				}
				$out.=$this->customizer->generate_template_html($html,$template_type,$order);
				$document_created = Wf_Woocommerce_Packing_List_Admin::created_document_count($order_id,$template_type);
				if($number_of_orders>1 && $order_inc<$number_of_orders)
				{
                	$out.='<p class="pagebreak"></p>';
	            }else
	            {
	                //$out.='<p class="no-page-break"></p>';
	            }
			}
			$out=$this->customizer->append_style_blocks($out,$style_blocks);
			$out=$this->customizer->append_header_and_footer_html($out,$template_type,$page_title);
    	}
    	$style_regex = '/<style id="template_font_style"[^>]*>[\s\S]*?<\/style>/';
    	$updated_style = '<style id="template_font_style">*{font-family:"DeJaVu Sans", monospace;}.template_footer{/*position:absolute;bottom:0px;*/}</style>';

    	$footer_to_the_bottom = apply_filters('wt_pklist_footer_to_the_bottom',true,$order,$template_type);

    	if($footer_to_the_bottom){
    		$updated_style = '<style id="template_font_style">*{font-family:"DeJaVu Sans", monospace;}.template_footer{position:absolute;bottom:0px;}</style>';
	    	
	    	$is_mpdf_used = Wf_Woocommerce_Packing_List_Admin::check_if_mpdf_used();
	    	if(is_rtl() && ($is_mpdf_used === true)){
	    		$updated_style = '<style id="template_font_style">*{font-family:"DeJaVu Sans", monospace;}.template_footer{position:absolute;bottom:0px;right:0px;}</style>';
	    	}
    	}

    	if($action == "print_invoice"){
    		$updated_style = '<style id="template_font_style">*{/*font-family:"DeJaVu Sans", monospace;*/}.template_footer{/*position:absolute;bottom:0px;*/}</style>';
    	}

     	if(preg_match($style_regex, $out)){
 			$out = preg_replace($style_regex, $updated_style, $out);
 		}
    	return $out;
    }
}
new Wf_Woocommerce_Packing_List_Invoice();