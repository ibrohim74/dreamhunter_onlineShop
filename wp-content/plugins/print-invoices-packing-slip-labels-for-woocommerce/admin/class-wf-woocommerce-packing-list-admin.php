<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin
 * @author     WebToffee <info@webtoffee.com>
 */
class Wf_Woocommerce_Packing_List_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/*
	 * module list, Module folder and main file must be same as that of module name
	 * Please check the `register_modules` method for more details
	 */
	public static $modules=array(
		'customizer',
		'uninstall-feedback',
		//'freevspro',
	);

	public static $existing_modules=array();

	public $bulk_actions=array();

	public static $tooltip_arr=array();

	/**
	*	To store the RTL needed or not status
	*	@since 2.6.6
	*/
	public static $is_enable_rtl=null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.5.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wf-woocommerce-packing-list-admin.css', array(), $this->version, 'all' );
		if(!empty(self::not_activated_pro_addons())){
			wp_enqueue_style( $this->plugin_name.'-addons-page', plugin_dir_url( __FILE__ ) . 'css/wf-woocommerce-packing-list-admin-addons-page.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_scripts() 
	{
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wf-woocommerce-packing-list-admin.js', array( 'jquery','jquery-ui-autocomplete','wp-color-picker','jquery-tiptip'), $this->version, false );
		//order list page bulk action filter
		$this->bulk_actions=apply_filters('wt_print_bulk_actions',$this->bulk_actions);

		$order_meta_autocomplete = self::order_meta_dropdown_list();
		$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images/uploader_sample_img.png';
		$is_rtl = is_rtl() ? 'rtl' : 'ltr';
		$params=array(
			'nonces' => array(
		            'wf_packlist' => wp_create_nonce(WF_PKLIST_PLUGIN_NAME),
		     ),
			'ajaxurl' => admin_url('admin-ajax.php'),
			'no_image'=>$wf_admin_img_path,
			'bulk_actions'=>array_keys($this->bulk_actions),
			'print_action_url'=>admin_url('?print_packinglist=true'),
			'order_meta_autocomplete' => json_encode($order_meta_autocomplete),
			'is_rtl' => $is_rtl,
			'msgs'=>array(
				'settings_success'=>__('Settings updated.','print-invoices-packing-slip-labels-for-woocommerce'),
				'all_fields_mandatory'=>__('All fields are mandatory','print-invoices-packing-slip-labels-for-woocommerce'),
				'settings_error'=>sprintf(__('Unable to update settings due to an internal error. %s To troubleshoot please click %s here. %s', 'print-invoices-packing-slip-labels-for-woocommerce'), '<br />', '<a href="https://www.webtoffee.com/how-to-fix-the-unable-to-save-settings-issue/" target="_blank">', '</a>'),
				'select_orders_first'=>__('You have to select order(s) first!','print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_not_gen_bulk'=>__('One or more order do not have invoice generated. Generate manually?','print-invoices-packing-slip-labels-for-woocommerce'),
				'error'=>__('Error','print-invoices-packing-slip-labels-for-woocommerce'),
				'please_wait'=>__('Please wait','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_required'=>__("is required",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_title_prompt' => __("Invoice",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt' => __("number has not been generated yet. Do you want to manually generate one ?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt_free_order' => __("‘Generate invoice for free orders’ is disabled in Invoice settings > Advanced. You are attempting to generate invoice for this free order. Proceed?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'creditnote_number_prompt' => __("Refund in this order seems not having credit number yet. Do you want to manually generate one ?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt_no_from_addr' => __("Please fill the `from address` in the plugin's general settings.",'print-invoices-packing-slip-labels-for-woocommerce'),
				'fitler_code_copied' => __("Code Copied","print-invoices-packing-slip-labels-for-woocommerce"),
				'close'=>__("Close",'print-invoices-packing-slip-labels-for-woocommerce'),
				'save'=>__("Save",'print-invoices-packing-slip-labels-for-woocommerce'),
				'enter_mandatory_fields'=>__('Please enter mandatory fields','print-invoices-packing-slip-labels-for-woocommerce'),
				'buy_pro_prompt_order_meta' => __('You can add more than 1 order meta in','print-invoices-packing-slip-labels-for-woocommerce'),
				'buy_pro_prompt_edit_order_meta' => __('Edit','print-invoices-packing-slip-labels-for-woocommerce'),
				'buy_pro_prompt_edit_order_meta_desc' => __('You can edit an existing item by using its key.','print-invoices-packing-slip-labels-for-woocommerce'),
			)
		);
		wp_localize_script($this->plugin_name, 'wf_pklist_params', $params);

	}


	/**
    * 	@since 2.5.8
    * 	Set tooltip for form fields 
    */
    public static function set_tooltip($key,$base_id="",$custom_css="")
    {
    	$tooltip_text=self::get_tooltips($key,$base_id);
    	if("" !== $tooltip_text)
    	{
    		$rtl_css = is_rtl() ? 'left:0;' : 'right:0;';
    		$tooltip_text='<span style="color:#4d535a; '.($custom_css!="" ? $custom_css : 'top:15px; margin-left:2px; position:absolute;').$rtl_css.'" class="dashicons dashicons-editor-help wt-tips" data-wt-tip="'.wp_kses_post($tooltip_text).'"></span>';
    	}
    	return $tooltip_text;
    }

    /**
    * 	@since 2.5.8
    * 	Get tooltip config data for non form field items
    * 	@return array 'class': class name to enable tooltip, 'text': tooltip text including data attribute if not empty
    */
    public static function get_tooltip_configs($key,$base_id="")
    {
    	$out=array('class'=>'','text'=>'');
    	$text=self::get_tooltips($key,$base_id);
    	if("" !== $text)
    	{
    		$out['text']=' data-wt-tip="'.wp_kses_post($text).'"';
    		$out['class']=' wt-tips';
    	}  	
    	return $out;
    }

    /**
    *	@since 2.5.8
	* 	This function will take tooltip data from modules and store ot 
	*
	*/
	public function register_tooltips()
	{
		include(plugin_dir_path( __FILE__ ).'data/data.tooltip.php');
		self::$tooltip_arr=array(
			'main'=>$arr
		);
		/* hook for modules to register tooltip */
		self::$tooltip_arr=apply_filters('wt_pklist_alter_tooltip_data',self::$tooltip_arr);
	}

	/**
	* 	Get tooltips
	*	@since 2.5.8
	*	@param string $key array key for tooltip item
	*	@param string $base module base id
	* 	@return tooltip content, empty string if not found
	*/
	public static function get_tooltips($key,$base_id='')
	{
		$arr = ("" !== $base_id && isset(self::$tooltip_arr[$base_id]) ? self::$tooltip_arr[$base_id] : self::$tooltip_arr['main']);
		return (isset($arr[$key]) ? $arr[$key] : '');
	}

	/**
	 * Function to add Items to Orders Bulk action dropdown
	 *
	 * @since    2.5.0
	 */
	public function alter_bulk_action($actions)
	{
        return array_merge($actions,$this->bulk_actions);
	}
	

	/**
	 * Function to add print button in order list page action column
	 *
	 * @since    2.5.0
	 */
	public function add_checkout_fields($fields) 
	{
		$checkout_fields_from_pro = apply_filters('wt_pklist_switch_pro_for_checkout_fields',false);
		if(!$checkout_fields_from_pro){
			$additional_options=Wf_Woocommerce_Packing_List::get_option('wf_invoice_additional_checkout_data_fields');
			$basic_checkout_fields = Wf_Woocommerce_Packing_List::$default_additional_checkout_data_fields;
	        if(is_array($additional_options) && count(array_filter($additional_options))>0 && is_array($basic_checkout_fields))
	        {
	            foreach ($additional_options as $value)
	            {
	            	if(in_array($value,$basic_checkout_fields)){
	            		$fields['billing']['billing_' . $value] = array(
		                    'text' => 'text',
		                    'label' => __(str_replace('_', ' ', $value), 'woocommerce'),
		                    'placeholder' => _x('Enter ' . str_replace('_', ' ', $value), 'placeholder', 'woocommerce'),
		                    'required' => false,
		                    'class' => array('form-row-wide', 'align-left'),
		                    'clear' => true
		                );
	            	}
	            }
	        }
		}
		return $fields;
	}

	/**
	 * Function to add print button in order list page action column
	 *
	 * @since    2.5.0
	 */
	public function add_print_action_button($actions,$order)
	{
        $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
        $wf_pklist_print_options=array(
            array(
                'name' => '',
                'action' => 'wf_pklist_print_document',
                'url' => sprintf('#%s',$order_id)
            ),
        );
        return array_merge($actions,$wf_pklist_print_options);
    } 

    /**
	 * Function to add email attachments to order email
	 *
	 * @since    2.5.0
	 */
	public function add_email_attachments($attachments, $status=null, $order=null)
	{
		if(is_object($order) && is_a($order,'WC_Order') && isset($status))
		{
            $order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$attachments=apply_filters('wt_email_attachments',$attachments,$order,$order_id,$status);
        }
		return $attachments;	
	}
   
    /**
	 * Function to add action buttons in order email
	 *
	 * 	@since    2.5.0
	 *	@since 	  2.6.5 	[Bug fix] Print button missing in email 
	 */
	public function add_email_print_actions($order)
	{
		if(is_object($order) && is_a($order,'WC_Order'))
		{
			$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$html='';
			$html=apply_filters('wt_email_print_actions',$html,$order,$order_id);	
		}
	}

    /**
	 * Function to add action buttons in user dashboard order list page
	 *
	 * @since    2.5.0
	 */
	public function add_fontend_print_actions($order)
	{
		$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
		$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
		$html='';
		$html=apply_filters('wt_frontend_print_actions',$html,$order,$order_id);	
	}

	public function add_order_list_page_print_actions($actions, $order)
	{
		$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
		$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();

		$wt_actions=array();
		$wt_actions=apply_filters('wt_pklist_intl_frontend_order_list_page_print_actions', $wt_actions, $order, $order_id);
		if(is_array($wt_actions) && count($wt_actions)>0)
		{
			foreach($wt_actions as $template_type => $action_arr)
			{
				if(is_array($action_arr))
				{
					foreach ($action_arr as $action => $title)
					{
						$show_button=true;
						$show_button=apply_filters('wt_pklist_is_frontend_order_list_page_print_action', $show_button, $template_type, $action);
						if($show_button)
						{
							/** button info to WC hook */
							$action_data=array(
								'url'  => Wf_Woocommerce_Packing_List::generate_print_url_for_user($order, $order_id, $template_type, $action),
								'name' => $title,
							);
							$actions['wt_pklist_'.$template_type.'_'.$action]=apply_filters('wt_pklist_frontend_order_list_page_print_action', $action_data, $template_type, $action, $order, $order_id);
						}
					}
				}
			}
		}

		return $actions;
	}

	public static function get_print_url($order_id, $action)
	{
		$url=wp_nonce_url(admin_url('?print_packinglist=true&post='.($order_id).'&type='.$action), WF_PKLIST_PLUGIN_NAME);
		$url=(isset($_GET['debug']) ? $url.'&debug' : $url);
		return $url;
	}

	public static function generate_print_button_data($order,$order_id,$action,$label,$icon_url,$is_show_prompt,$button_location="detail_page")
	{
		$url=self::get_print_url($order_id, $action);
		
		$href_attr='';
		$onclick='';
		$confirmation_clss='';
		if(false === Wf_Woocommerce_Packing_List::is_from_address_available()) 
    	{
    		$is_show_prompt = 3;
    	}
		if((1 === $is_show_prompt || "1" === $is_show_prompt) || (2 === $is_show_prompt || "2" === $is_show_prompt) || (3 === $is_show_prompt || "3" === $is_show_prompt))
		{
			$confirmation_clss='wf_pklist_confirm_'.$action;
			$onclick='onclick=" return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(\''.$url.'\','.$is_show_prompt.');"';
		}else
		{
			$href_attr=' href="'.esc_url($url).'"';
		}
		if("detail_page" === $button_location)
        {
        ?>
		<tr>
			<td>
				<a class="button tips wf-packing-list-link" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" data-tip="<?php echo strip_tags($label);?>" >
				<?php
				if("" !== $icon_url)
				{
				?>
					<img src="<?php echo esc_url($icon_url);?>" alt="<?php echo esc_attr($label);?>" width="14"> 
				<?php
				}
				?>
				<?php echo wp_kses_post($label);?>
				</a>
			</td>
		</tr>
		<?php
        }elseif("list_page" === $button_location)
        {
        ?>
			<li>
				<a class="<?php echo esc_attr($confirmation_clss);?>" data-id="<?php echo esc_attr($order_id);?>" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank"><?php echo wp_kses_post($label);?></a>
			</li>
		<?php
        }
	}

	/**
	 * Function to add action buttons in order list page
	 *
	 * @since    2.5.0
	 */
	public function add_print_actions($column)
	{
		global $post, $woocommerce, $the_order;
		if("order_actions" === $column || "wc_actions" === $column)
		{
			$order = ( WC()->version < '2.7.0' ) ? new WC_Order($post->ID) : new wf_order($post->ID);
            $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$html='';
			?>
			<div id="wf_pklist_print_document-<?php echo $order_id;?>" class="wf-pklist-print-tooltip-order-actions">				
				<div class="wf-pklist-print-tooltip-content">
                    <ul>
                    <?php
					$btn_arr=array();
					$btn_arr=apply_filters('wt_print_actions', $btn_arr, $order, $order_id, 'list_page');
					self::generate_print_button_html($btn_arr, $order, $order_id, 'list_page'); //generate buttons
					?>
					</ul>
                </div>
                <div class="wf_arrow"></div>	
			</div>
			<?php
		}
		return $column;
	}

	/**
	 * Registers meta box and printing options
	 *
	 * @since    2.5.0
	 */
	public function add_meta_boxes()
	{
		add_meta_box('woocommerce-packinglist-box', __('Invoice/Packing','print-invoices-packing-slip-labels-for-woocommerce'), array($this,'create_metabox_content'),'shop_order', 'side', 'default');
	}

	/**
	 * Add plugin action links
	 *
	 * @param array $links links array
	 */
	public function plugin_action_links($links) 
	{
	   	$links[] = '<a href="'.admin_url('admin.php?page='.WF_PKLIST_POST_TYPE).'">'.__('Settings', 'print-invoices-packing-slip-labels-for-woocommerce').'</a>';
	   	$links[] = '<a href="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce" target="_blank">'.__('Support','print-invoices-packing-slip-labels-for-woocommerce').'</a>';
	   	$links[] = '<a href="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce/reviews/?rate=5#new-post" target="_blank">' . __('Review','print-invoices-packing-slip-labels-for-woocommerce') . '</a>';
	   	$links[] = '<a href="https://www.webtoffee.com/woocommerce-pdf-invoices-packing-slips-delivery-notes-shipping-labels-userguide-free-version/" target="_blank">' . __('Documentation','print-invoices-packing-slip-labels-for-woocommerce') . '</a>';
	   	$not_activated_pro_addons = Wf_Woocommerce_Packing_List_Admin::not_activated_pro_addons('wt_qr_addon');
	   	if(!empty($not_activated_pro_addons)){
	   		$pro_addon_arr = array(
		   		'wt_ipc_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=PDF_invoice&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('PDF Invoices','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   		'wt_sdd_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-shipping-labels-delivery-notes/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=Shipping_Label&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('Shipping labels','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   		'wt_pl_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-picklist/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=Picklist&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('Pick lists','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   		'wt_pi_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-proforma-invoice/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=Proforma_Invoice&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('Proforma invoices','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   		'wt_al_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-address-label/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=Address_Label&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('Address labels','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   	);
	   		$addon_link = '<br><span style="color:#3db634;">'.__("Premium Extensions","print-invoices-packing-slip-labels-for-woocommerce").': </span>';
		   	for($i = 0; $i < count($not_activated_pro_addons); $i++){
		   		if(isset($pro_addon_arr[$not_activated_pro_addons[$i]])){
		   			$pro_add = $pro_addon_arr[$not_activated_pro_addons[$i]];
		   			$addon_link .= '<a href="'.esc_url($pro_add['utm_link']).'" target="_blank">'.esc_html($pro_add['link_label']).'</a>';
		   			if($i < count($not_activated_pro_addons)-1){
		   				$addon_link .=' | ';
		   			}
		   		}
		   	}
	   		$links[] = $addon_link;
	   	}
	   	return $links;
	}

	/**
	 *	@since  4.0.0  
	 * 	- create content for metabox
	 *	- added separate section for document details and print actions
	 * 
	 */
	public function create_metabox_content()
	{
		global $post;
        $order = ( WC()->version < '2.7.0' ) ? new WC_Order($post->ID) : new wf_order($post->ID);
        $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
		?>
		<table class="wf_invoice_metabox" style="width:100%;">			
			<?php
			$data_arr=array();
			$data_arr=apply_filters('wt_print_docdata_metabox',$data_arr, $order, $order_id);
			if(count($data_arr)>0)
			{
			?>
			<tr>
				<td style="font-weight:bold;">
					<h4 style="margin:0px; padding-top:5px; padding-bottom:3px; border-bottom:dashed 1px #ccc;"><?php _e('Document details','print-invoices-packing-slip-labels-for-woocommerce'); ?></h4>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:10px;">
					<?php
					
					foreach($data_arr as $datav)
					{
						echo '<span style="font-weight:500;">';
						echo ("" !== $datav['label'] ? $datav['label'].': ' : '');
						echo '</span>';
						echo $datav['value'].'<br />';
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td>
					<h4 style="margin:0px; padding-top:5px; padding-bottom:3px; border-bottom:dashed 1px #ccc;"><?php _e('Print/Download','print-invoices-packing-slip-labels-for-woocommerce'); ?></h4>
				</td>
			</tr>
			<tr>
				<td style="height:3px; font-size:0px; line-height:0px;"></td>
			</tr>
			<?php
			$btn_arr=array();
			$btn_arr=apply_filters('wt_print_actions', $btn_arr, $order, $order_id, 'detail_page');
			self::generate_print_button_html($btn_arr, $order, $order_id, 'detail_page'); //generate buttons
			?>
		</table>
		<?php
	}

	public static function generate_print_button_html($btn_arr, $order, $order_id, $button_location)
	{
		/* filter for customers to alter buttons */
		$btn_arr=apply_filters('wt_pklist_alter_print_actions',$btn_arr, $order, $order_id, $button_location);

		foreach($btn_arr as $btn_key=>$args)
		{
			$action=$args['action'];
			$css_class=(isset($args['css_class']) && is_string($args['css_class']) ? $args['css_class'] : ''); /* button custom css */
			$custom_attr=(isset($args['custom_attr']) && is_string($args['custom_attr']) ? $args['custom_attr'] : ''); /* button custom attribute */

			$label=$args['label'];
			$is_show_prompt=$args['is_show_prompt'];
			$tooltip=(isset($args['tooltip']) ? $args['tooltip'] : $label);
			$button_location=(isset($args['button_location']) ? $args['button_location'] : 'detail_page');

			$url=self::get_print_url($order_id, $action);

			$href_attr='';
			$onclick='';
			$confirmation_clss='';
			if(0 !== $is_show_prompt && "0" !== $is_show_prompt) //$is_show_prompt variable is a string then it will set as warning msg title
			{
				$confirmation_clss='wf_pklist_confirm_'.$action;
				$onclick='onclick=" return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(\''.$url.'\',\''.$is_show_prompt.'\');"';
			}else
			{
				$href_attr=' href="'.$url.'"';
			}
			if("detail_page" === $button_location)
	        {
	        	$button_type=(isset($args['button_type']) ? $args['button_type'] : 'normal');
	        	$button_key=(isset($args['button_key']) ? $args['button_key'] : 'button_key_'.$btn_key);
	        ?>
				<tr>
					<td class="wt_pklist_dash_btn_row">
						<?php
						if("aggregate" === $button_type || "dropdown" === $button_type)
						{
							if("aggregate" === $button_type) /* reverse the order of buttons */
							{
								$args['items']=array_reverse($args['items']);
							}
							?>
							<div class="wt_pklist_<?php echo $button_type;?> <?php echo $css_class;?>" <?php echo $custom_attr;?> >
								<div class="wt_pklist_btn_text"><?php echo wp_kses_post($label);?></div>
								<div class="wt_pklist_<?php echo $button_type;?>_content">
									<?php
									foreach($args['items'] as $btnkk => $btnvv)
									{
										$action=$btnvv['action'];
										$label=$btnvv['label'];
										
										$icon=(isset($btnvv['icon']) && "" !== $btnvv['icon'] ? $btnvv['icon'] : ''); //dashicon
										$icon_url=(isset($btnvv['icon_url']) && "" !== $btnvv['icon_url'] ? $btnvv['icon_url'] : ''); //image icon

										if("aggregate" === $button_type) /* only icon, No label */
										{
											if("" === $icon && "" === $icon_url)
											{											
												global $wp_version;
												if(version_compare($wp_version, '5.5.3')>=0)
												{
													$fallback_icon='tag';
													if(false !== strpos($action, 'download_'))
													{
														$fallback_icon='download';

													}elseif(false !== strpos($action, 'print_'))
													{
														$fallback_icon='printer';
													}
													$btn_label='<span class="dashicons dashicons-'.$fallback_icon.'"></span>';

												}else
												{
													$fallback_icon_url='tag-icon.png';
													if(false !== strpos($action, 'download_'))
													{
														$fallback_icon_url='download-icon.png';

													}elseif(false !== strpos($action, 'print_'))
													{
														$fallback_icon_url='print-icon.png';
													}
													$btn_label='<span class="dashicons" style="line-height:17px;"><img src="'.WF_PKLIST_PLUGIN_URL.'admin/images/'.$fallback_icon_url.'" style="width:16px; height:16px; display:inline;"></span>';
												}
											}else
											{
												if("" !== $icon)
												{
													$btn_label='<span class="dashicons dashicons-'.$icon.'"></span>';
												}else
												{
													$btn_label='<span class="dashicons" style="line-height:17px;"><img src="'.esc_url($icon_url).'" style="width:16px; height:16px; display:inline;"></span>';
												}
											}
										}else
										{
											$btn_label=$label;
										}

										$tooltip=(isset($btnvv['tooltip']) ? $btnvv['tooltip'] : $label);
										$is_show_prompt=$btnvv['is_show_prompt'];
										$item_css_class=(isset($btnvv['css_class']) && is_string($btnvv['css_class']) ? $btnvv['css_class'] : ''); /* dropdown item custom css */
										$item_custom_attr=(isset($btnvv['custom_attr']) && is_string($btnvv['custom_attr']) ? $btnvv['custom_attr'] : ''); /* dropdown item custom attribute */
										
										$url=self::get_print_url($order_id, $action);
										
										$href_attr='';
										$onclick='';
										$confirmation_clss='';
										$print_node_attr = '';
										if(0 !== $is_show_prompt) //$is_show_prompt variable is a string then it will set as warning msg title
										{
											if(strpos($item_css_class, 'wt_pklist_printnode_manual_print') === false){
												$confirmation_clss='wf_pklist_confirm_'.$action;
												$onclick='onclick=" return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(\''.$url.'\',\''.$is_show_prompt.'\');"';
											}
											$print_node_attr = $is_show_prompt;
										}else
										{
											$href_attr=' href="'.esc_url($url).'"';
											$print_node_attr = 0;
										}
										?>
										<a <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" data-id="<?php echo esc_attr($order_id);?>" class="<?php echo esc_attr($item_css_class);?>" <?php echo $item_custom_attr;?> title="<?php echo esc_attr($tooltip);?>" data-prompt="<?php echo esc_attr($print_node_attr); ?>"> <?php echo wp_kses_post($btn_label);?></a>
										<?php
									}
									?>
								</div>
							</div>
							<?php
						}else
						{
						?>
							<a class="button tips wf-packing-list-link <?php echo $css_class;?>" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" data-tip="<?php echo esc_attr($tooltip);?>" data-id="<?php echo $order_id;?>" <?php echo $custom_attr;?> >
								<?php echo $label;?>
							</a>
						<?php
						}
						?>
					</td>
				</tr>
			<?php
	        }elseif("list_page" === $button_location)
	        {
	        ?>
				<li>
					<a class="<?php echo esc_attr($confirmation_clss);?> <?php echo esc_attr($css_class);?>" data-id="<?php echo esc_attr($order_id);?>" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" title="<?php echo esc_attr($tooltip);?>" <?php echo $custom_attr;?> ><?php echo wp_kses_post($label);?></a>
				</li>
			<?php
	        }
	    }
	}

	/**
	 * @since 4.0.0 Removed other solution page, instead created seperate menu for all documents
	 * 
	 */
	public function admin_menu()
	{
		$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images';
		$menus=array(
			array(
				'menu',
				__('General Settings','print-invoices-packing-slip-labels-for-woocommerce'),
				__('Invoice/Packing','print-invoices-packing-slip-labels-for-woocommerce'),
				'manage_woocommerce',
				WF_PKLIST_POST_TYPE,
				array($this,'admin_settings_page'),
				'dashicons-media-text',
				56,
				'id' => 'main_menu',
			)
		);

		$menus=apply_filters('wt_admin_menu',$menus);
		$menus[]=array(
			'submenu',
			WF_PKLIST_POST_TYPE,
			 __('Extensions','print-invoices-packing-slip-labels-for-woocommerce'),
			__('Extensions','print-invoices-packing-slip-labels-for-woocommerce'),
			'manage_woocommerce',
			WF_PKLIST_POST_TYPE.'_premium_extension',
			array($this,'admin_premium_extension_page'),
			'id' => 'premium_extension',
		);
		
		$menus = apply_filters('wt_pklist_add_menu',$menus);
		if(count($menus)>0)
		{
			add_submenu_page(WF_PKLIST_POST_TYPE,__('General Settings','print-invoices-packing-slip-labels-for-woocommerce'),__('General Settings','print-invoices-packing-slip-labels-for-woocommerce'), "manage_woocommerce",WF_PKLIST_POST_TYPE,array($this,'admin_settings_page'));
			foreach($menus as $menu)
			{
				if("submenu" === $menu[0])
				{
					add_submenu_page($menu[1],$menu[2],$menu[3],$menu[4],$menu[5],$menu[6]);
				}else
				{
					add_menu_page($menu[1],$menu[2],$menu[3],$menu[4],$menu[5],$menu[6],$menu[7]);	
				}
			}
		}

		if(function_exists('remove_submenu_page')){
			//remove_submenu_page(WF_PKLIST_POST_TYPE,WF_PKLIST_POST_TYPE);
		}
	}

	public static function add_menu_after_id($menus,$current_menu,$after_id){
		$pos = 1;
		foreach($menus as $key => $menu){
			if ( isset( $menu['id'] ) && $menu['id'] == $after_id ) {
				break;
			}else{
				$pos++;
			}
		}
		$menus = array_merge( array_slice( $menus, 0, $pos, true ), $current_menu, array_slice( $menus, $pos, NULL, true ) );
		return $menus;
	}

	public static function add_tab_after_id($tab_items,$new_tab_item,$after_id,$template_type,$module_id){
		$pos = 1;
		foreach($tab_items as $key => $tab_item){
			if ( $key == $after_id ) {
				break;
			}else{
				$pos++;
			}
		}
		$tab_items = array_merge( array_slice( $tab_items, 0, $pos, true ), $new_tab_item, array_slice( $tab_items, $pos, NULL, true ) );
		return $tab_items;
	}

	/**
	* @since 2.5.9
	* Is allowed to print
	*/
	public static function check_role_access()
	{
		$admin_print_role_access=array('manage_options', 'manage_woocommerce');
    	$admin_print_role_access=apply_filters('wf_pklist_alter_admin_print_role_access', $admin_print_role_access);  
    	$admin_print_role_access=(!is_array($admin_print_role_access) ? array() : $admin_print_role_access);
    	$is_allowed=false;
    	foreach($admin_print_role_access as $role) //checking access
    	{
    		if(current_user_can($role)) //any of the role is okay then allow to print
    		{
    			$is_allowed=true;
    			break;
    		}
    	}
    	return $is_allowed;
	}

	/**
	 * function to render printing window
	 *
	 */
    public function print_window() 
    {       
        $attachments = array();
        if(isset($_GET['print_packinglist'])) 
        {
        	//checkes user is logged in
        	if(!is_user_logged_in())
        	{
        		auth_redirect();
        	}
        	$not_allowed_msg=__('You are not allowed to view this page.','print-invoices-packing-slip-labels-for-woocommerce');
        	$not_allowed_title=__('Access denied !!!.','print-invoices-packing-slip-labels-for-woocommerce');

            $client = false;
            //	to check current user has rights to get invoice and packing list
            if(!isset($_GET['attaching_pdf']))
            {
	            $nonce=isset($_GET['_wpnonce']) ? sanitize_text_field($_GET['_wpnonce']) : ''; 
	            if(!(wp_verify_nonce($nonce,WF_PKLIST_PLUGIN_NAME)))
	            {
	                wp_die($not_allowed_msg,$not_allowed_title);
	            }else
	            {
	            	if(!$this->check_role_access()) //Check access
	                {
	                	wp_die($not_allowed_msg,$not_allowed_title);
	                }
	            	$orders = explode(',', sanitize_text_field($_GET['post']));
	            }
        	}else 
        	{
        		// to get the orders number
	            if(isset($_GET['email']) && isset($_GET['post']) && isset($_GET['user_print']))
	            {
	                $email_data_get =Wf_Woocommerce_Packing_List::wf_decode(sanitize_text_field($_GET['email']));
	                $order_data_get =Wf_Woocommerce_Packing_List::wf_decode(sanitize_text_field($_GET['post']));
	                $order_data = wc_get_order($order_data_get);
	                if(!$order_data)
	                {
	                	wp_die($not_allowed_msg,$not_allowed_title);
	                }
	                $logged_in_userid=get_current_user_id();
	                $order_user_id=((WC()->version < '2.7.0') ? $order_data->user_id : $order_data->get_user_id());
	                if($logged_in_userid!=$order_user_id) //the current order not belongs to the current logged in user
	                { 
	  	             	if(!$this->check_role_access()) //Check access
	                	{
	                		wp_die($not_allowed_msg,$not_allowed_title);
	                	}
	                }

	                //checks the email parameters belongs to the given order
	                if($email_data_get === ((WC()->version < '2.7.0') ? $order_data->billing_email : $order_data->get_billing_email())) 
	                {
	                    $orders=explode(",",$order_data_get); //must be an array
	                }else
	                {
	                    wp_die($not_allowed_msg,$not_allowed_title);
	                }
	            }else
	            {
	            	wp_die($not_allowed_msg,$not_allowed_title);
	            }
        	} 
            $orders=array_values(array_filter($orders));
            $orders=$this->verify_order_ids($orders);
            if(count($orders)>0)
            {
	            remove_action('wp_footer', 'wp_admin_bar_render', 1000);
	            $action = (isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '');
	            //action for modules to hook print function
	            do_action('wt_print_doc', $orders, $action);
	        }
            exit();
        }
    }

    /**
	* Check for valid order ids
	* @since 2.5.4
	* @since 2.5.7 Added compatiblity for `Sequential Order Numbers for WooCommerce`
	*/
    public static function verify_order_ids($order_ids)
    {
    	$out=array();
    	foreach ($order_ids as $order_id)
    	{
    		if(false === wc_get_order($order_id))
    		{
    			/* compatibility for sequential order number */
    			$order_data=wc_get_orders(
    				array(
    					'limit' => 1,
    					'return' => 'ids',
    					'meta_query'=>array(
    						'key'=>'_order_number',
    						'value'=>$order_id,
    					)
    			));
    			if(false !== $order_data && is_array($order_data) && 1 === count($order_data))
    			{
    				$order_id=(int) $order_data[0];
    				if($order_id>0 && false !== wc_get_order($order_id))
    				{
    					$out[]=$order_id;
    				}
    			}
    		}else
    		{
    			$out[]=$order_id;
    		}
    	}
    	return $out;
    }

    public function load_address_from_woo()
    {
    	if(!self::check_write_access()) 
		{
			exit();
		}
    	$out=array(
    		'status'=>1,
    		'address_line1'=>get_option('woocommerce_store_address'),
    		'address_line2'=>get_option('woocommerce_store_address_2'),
    		'city'=>get_option('woocommerce_store_city'),
    		'country'=>get_option('woocommerce_default_country'),
    		'postalcode'=>get_option('woocommerce_store_postcode'),
    	);
    	echo json_encode($out);
    	exit();
    }

	private function dismiss_notice()
	{
		$allowd_items=array();
		if(isset($_GET['wf_pklist_notice_dismiss']) && "" !== trim($_GET['wf_pklist_notice_dismiss']))
		{
			if(in_array(sanitize_text_field($_GET['wf_pklist_notice_dismiss']),$allowd_items))
			{
				update_option(sanitize_text_field($_GET['wf_pklist_notice_dismiss']),1);
			}
		}
	}

	/**
	 * Webtoffee extension page
	 * @since 4.0.8
	 */
	public function admin_premium_extension_page()
	{
		wp_enqueue_style( 'woocommerce_admin_styles' );
		include_once WF_PKLIST_PLUGIN_PATH.'admin/views/premium_extension_page.php';
	}

	/**
	 * Admin settings page
	 *
	 * @since    2.5.0
	 * 
	 */
	public function admin_settings_page()
	{
		//dismiss the notice if exists
		$this->dismiss_notice();

		$the_options=Wf_Woocommerce_Packing_List::get_settings();
		$no_image=Wf_Woocommerce_Packing_List::$no_image;
		$order_statuses = wc_get_order_statuses();
		$wf_generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus');
		
		/**
		*	@since 2.6.6
		*	Get available PDF libraries
		*/
		$pdf_libs=Wf_Woocommerce_Packing_List::get_pdf_libraries();

		wp_enqueue_media();
		wp_enqueue_script('wc-enhanced-select');
		wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css');

		/* enable/disable modules */
		if(isset($_POST['wf_update_module_status']))
		{
			// Check nonce:
	        if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
    		{
    			exit();
    		}

		    $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
		    if(false === $wt_pklist_common_modules)
		    {
		        $wt_pklist_common_modules=array();
		    }
		    if(isset($_POST['wt_pklist_common_modules']))
		    {
		        $wt_pklist_post=self::sanitize_text_arr($_POST['wt_pklist_common_modules']);
		        foreach($wt_pklist_common_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && (1 === $wt_pklist_post[$k] || "1" === $wt_pklist_post[$k]))
		            {
		                $wt_pklist_common_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_common_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_common_modules as $k=>$v)
		        {
					$wt_pklist_common_modules[$k]=0;
		        }
		    }
		    update_option('wt_pklist_common_modules',$wt_pklist_common_modules);
		    wp_redirect($_SERVER['REQUEST_URI']); exit();
		}

		include WF_PKLIST_PLUGIN_PATH.'admin/partials/admin-settings.php';
	}

	/**
	* @since 2.6.2
	* Is user allowed 
	*/
	public static function check_write_access($nonce_id='')
	{
		$er=true;
		//checkes user is logged in
    	if(!is_user_logged_in())
    	{
    		$er=false;
    	}

    	if(true === $er) //no error then proceed
    	{
    		$nonce=sanitize_text_field($_REQUEST['_wpnonce']);
    		$nonce=(is_array($nonce) ? $nonce[0] : $nonce);
    		$nonce_id=("" === $nonce_id ? WF_PKLIST_PLUGIN_NAME : $nonce_id);
    		if(!(wp_verify_nonce($nonce, $nonce_id)))
	        {
	            $er=false;
	        }else
	        {
	        	if(!Wf_Woocommerce_Packing_List_Admin::check_role_access()) //Check access
	            {
	            	$er=false;
	            }
	        }
    	}
    	return $er;
	}

	/**
	* Form action for debug settings tab
	* @since 2.6.7
	*/
	public function debug_save()
	{	
		if(isset($_POST['wt_pklist_admin_modules_btn']))
		{
		    if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
	    	{
	    		return;
	    	}
	        
		    $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
		    if(false === $wt_pklist_common_modules)
		    {
		        $wt_pklist_common_modules=array();
		    }
		    if(isset($_POST['wt_pklist_common_modules']))
		    {
		        $wt_pklist_post=self::sanitize_text_arr($_POST['wt_pklist_common_modules']);
		        foreach($wt_pklist_common_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && (1 === $wt_pklist_post[$k] || "1" === $wt_pklist_post[$k]))
		            {
		                $wt_pklist_common_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_common_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_common_modules as $k=>$v)
		        {
					$wt_pklist_common_modules[$k]=0;
		        }
		    }

		    $wt_pklist_admin_modules=get_option('wt_pklist_admin_modules');
		    if(false === $wt_pklist_admin_modules)
		    {
		        $wt_pklist_admin_modules=array();
		    }
		    if(isset($_POST['wt_pklist_admin_modules']))
		    {
		        $wt_pklist_post=self::sanitize_text_arr($_POST['wt_pklist_admin_modules']);
		        foreach($wt_pklist_admin_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && (1 === $wt_pklist_post[$k] || "1" === $wt_pklist_post[$k]))
		            {
		                $wt_pklist_admin_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_admin_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_admin_modules as $k=>$v)
		        {
					$wt_pklist_admin_modules[$k]=0;
		        }
		    }
		    update_option('wt_pklist_admin_modules',$wt_pklist_admin_modules);
		    update_option('wt_pklist_common_modules',$wt_pklist_common_modules);
		    wp_redirect($_SERVER['REQUEST_URI']); exit();
		}

		if(Wf_Woocommerce_Packing_List_Admin::check_role_access()) //Check access
	    {
			//module debug settings saving hook
	    	do_action('wt_pklist_module_save_debug_settings');
	    }
	}

	/**
	*	@since 2.6.2 
	* 	Validate array data
	*/
	public static function sanitize_text_arr($arr, $type='text')
	{
		if(is_array($arr))
		{
			$out=array();
			foreach($arr as $k=>$arrv)
			{
				if(is_array($arrv))
				{
					$out[$k]=self::sanitize_text_arr($arrv, $type);
				}else
				{
					if("int" === $type)
					{
						$out[$k]=intval($arrv);
					}else
					{
						$out[$k]=sanitize_text_field($arrv);
					}
				}
			}
			return $out;
		}else
		{
			if("int" === $type)
			{
				return intval($arr);
			}else
			{
				return sanitize_text_field($arr);
			}
		}
	}

	/**
	*	@since 2.6.2 
	* 	Settings validation function for modules and plugin settings
	*/
	public function validate_settings_data($val, $key, $validation_rule=array())
	{		
		if(isset($validation_rule[$key]) && is_array($validation_rule[$key])) /* rule declared/exists */
		{
			if(isset($validation_rule[$key]['type']))
			{
				if("text" === $validation_rule[$key]['type'])
				{
					$val=sanitize_text_field($val);
				}elseif("text_arr" === $validation_rule[$key]['type'])
				{
					$val=self::sanitize_text_arr($val);
				}elseif("int" === $validation_rule[$key]['type'])
				{
					$val=intval($val);
				}elseif("float" === $validation_rule[$key]['type'])
				{
					$val=floatval($val);
				}elseif("int_arr" === $validation_rule[$key]['type'])
				{
					$val=self::sanitize_text_arr($val, 'int');
				}
				elseif("textarea" === $validation_rule[$key]['type'])
				{
					$val=sanitize_textarea_field($val);
				}else
				{
					$val=sanitize_text_field($val);
				}
			}
		}else
		{
			$val=sanitize_text_field($val);
		}
		return $val;
	}

	public function validate_box_packing_field($value)
	{           
        $new_boxes = array();
        foreach ($value as $key => $value)
        {
            if ($value['length'] != '') {
                $value['enabled'] = isset($value['enabled']) ? true : false;
                $new_boxes[] = $value;
            }
        }
        return $new_boxes;
    }

	/**
	 * Envelope settings tab content with tab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_tabcontent($target_id,$view_file="",$html="",$variables=array(),$need_submit_btn=0)
	{
		extract($variables);
	?>
		<div class="wf-tab-content" data-id="<?php echo $target_id;?>">
			<?php
			if("" !== $view_file && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
			<?php 
			if($need_submit_btn==1)
			{
				include plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME)."admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
	<?php
	}

	/**
	 * Envelope settings subtab content with subtab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_subtabcontent($target_id,$view_file="",$html="",$variables=array(),$need_submit_btn=0)
	{
		extract($variables);
	?>
		<div class="wf_sub_tab_content" data-id="<?php echo $target_id;?>">
			<?php
			if("" !== $view_file && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
			<?php 
			if(1 === $need_submit_btn || "1" === $need_submit_btn)
			{
				include plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME)."admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
	<?php
	}

	/**
	 * Registers modules: public+admin	 
	 */
	public function admin_modules()
	{ 	
		$admin_module_save = 0;
		$wt_pklist_admin_modules=get_option('wt_pklist_admin_modules');
		if(false === $wt_pklist_admin_modules)
		{
			$wt_pklist_admin_modules=array();
			$admin_module_save = 1;
		}elseif(empty($wt_pklist_admin_modules)){
			$admin_module_save = 1;
		}

		foreach (self::$modules as $module) //loop through module list and include its file
		{
			$is_active=1;
			if(isset($wt_pklist_admin_modules[$module]))
			{
				$is_active=$wt_pklist_admin_modules[$module]; //checking module status
			}else
			{
				$wt_pklist_admin_modules[$module]=1; //default status is active
			}
			$module_file=plugin_dir_path( __FILE__ )."modules/$module/$module.php";	
			if("customizer" === $module){
				$cus_file_name = "basic_customizer.php";
				$module_file=plugin_dir_path( __FILE__ )."modules/$module/".$cus_file_name;	
			}
			
			if(file_exists($module_file) && (1 === $is_active || "1" === $is_active))
			{
				self::$existing_modules[]=$module; //this is for module_exits checking
				require_once $module_file;
			}else
			{
				$wt_pklist_admin_modules[$module]=0;	
			}
		}
		$out=array();
		foreach($wt_pklist_admin_modules as $k=>$m)
		{
			if(in_array($k,self::$modules))
			{
				$out[$k]=$m;
			}
		}

		if(1 === $admin_module_save || "1" === $admin_module_save){
			update_option('wt_pklist_admin_modules',$out);
		}
	}

	public static function module_exists($module)
	{
		return in_array($module,self::$existing_modules);
	}

	/**
	*	@since 2.6.2
	* 	Save admin settings and module settings ajax hook
	*/
	public function save_settings()
	{
		$out=array(
			'status'=>false,
			'msg'=>__('Error', 'print-invoices-packing-slip-labels-for-woocommerce'),
		);

		$base=(isset($_POST['wf_settings_base']) ? sanitize_text_field($_POST['wf_settings_base']) : 'main');
		$base_id=("main" === $base ? '' : Wf_Woocommerce_Packing_List::get_module_id($base));
		$tab_name = (isset($_POST['wt_tab_name']) ? sanitize_text_field($_POST['wt_tab_name']) : "");
		if(Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
    	{
    		$the_options=Wf_Woocommerce_Packing_List::get_settings($base_id);
    		$single_checkbox_fields = Wf_Woocommerce_Packing_List::get_single_checkbox_fields($base_id,$tab_name);
    		$multi_checkbox_fields = Wf_Woocommerce_Packing_List::get_multi_checkbox_fields($base_id,$tab_name);

    		//multi select form fields array. (It will not return a $_POST val if it's value is empty so we need to set default value)
	        $default_val_needed_fields=array();

	        /* this is an internal filter */
	        $default_val_needed_fields=apply_filters('wt_pklist_intl_alter_multi_select_fields', $default_val_needed_fields, $base_id);

	        $validation_rule=array(				
				'woocommerce_wf_packinglist_boxes'=>array('type'=>'text_arr'),
				'woocommerce_wf_packinglist_footer'=>array('type'=>'textarea'),
				'woocommerce_wf_generate_for_taxstatus'=>array('type'=>'text_arr'),
				'wf_woocommerce_invoice_show_print_button'=>array('type'=>'text_arr'),
		    ); //this is for plugin settings default. Modules can alter
	        $validation_rule=apply_filters('wt_pklist_intl_alter_validation_rule', $validation_rule, $base_id);

	       	$run_empty_count = false;
	        //invoice number empty count trigger when changing the order status in invoice settings page
	        if(isset($_POST['woocommerce_wf_generate_for_orderstatus'])){
	        	if(is_array($the_options['woocommerce_wf_generate_for_orderstatus']) && is_array($_POST['woocommerce_wf_generate_for_orderstatus'])){
	        		$find_diff = array_merge (array_diff($the_options['woocommerce_wf_generate_for_orderstatus'], $_POST['woocommerce_wf_generate_for_orderstatus']), array_diff($_POST['woocommerce_wf_generate_for_orderstatus'], $the_options['woocommerce_wf_generate_for_orderstatus']));
		        	if(!empty($find_diff)){
		        		$run_empty_count = true;
		        	}
	        	}
	        }

	        // invoice number empty count trigger when enable or disable the old orders
	        if(isset($the_options['wf_woocommerce_invoice_prev_install_orders'])){
	        	$prev_val = isset($_POST['wf_woocommerce_invoice_prev_install_orders']) ? sanitize_text_field($_POST['wf_woocommerce_invoice_prev_install_orders']) : "";
	        	if(("" !== $prev_val) && ($prev_val !== $the_options['wf_woocommerce_invoice_prev_install_orders'])){
	        		$run_empty_count = true;
		        }elseif(("" === $prev_val) && ("No" !== $the_options['wf_woocommerce_invoice_prev_install_orders'])){
	        		$run_empty_count = true;
		        }
	        }

	        foreach($the_options as $key => $value) 
	        {
	            if(isset($_POST[$key]))
	            {
	            	$the_options[$key]=$this->validate_settings_data($_POST[$key], $key, $validation_rule);
	            	if("woocommerce_wf_packinglist_boxes" === $key)
	            	{
	            		$the_options[$key]=$this->validate_box_packing_field($_POST[$key]);
	            	}

	            	if(isset($multi_checkbox_fields[$key])){
	            		$the_options[$key] = apply_filters('wf_module_save_multi_checkbox_fields',$the_options[$key],$key,$multi_checkbox_fields,$base_id);
	            	}
	            }elseif(array_key_exists($key,$single_checkbox_fields)){
	            	if(!isset($_POST['update_sequential_number'])){ //since the settings of the invoice are divided into 2
	            		$the_options[$key] = $single_checkbox_fields[$key]; //if unchecked,PHP will not send the values, so get the unchecked value from the respective modules
	            	}
	            }elseif(array_key_exists($key, $multi_checkbox_fields)){
		            $the_options[$key] = $multi_checkbox_fields[$key];
	            }else
	            {
	            	if(array_key_exists($key,$default_val_needed_fields))
	            	{
	            		/* Set a hidden field for every multi-select field in the form. This will be used to populate the multi-select field with an empty array when it does not have any value. */
	            		if(isset($_POST[$key.'_hidden']))
	            		{
	            			$the_options[$key]=$default_val_needed_fields[$key];
	            		}
	            	}
	            }
	        }
	        Wf_Woocommerce_Packing_List::update_settings($the_options, $base_id);

	        do_action('wf_pklist_intl_after_setting_update', $the_options, $base_id);

	        if(true === $run_empty_count){
	        	$this->wt_get_empty_invoice_number_count();
	        }

	        $out['status']=true;
	        $out['msg']=__('Settings Updated', 'print-invoices-packing-slip-labels-for-woocommerce');
	       
    	}
		echo json_encode($out);
		exit();
	}

	public static function strip_unwanted_tags($html)
	{
		$html=html_entity_decode(stripcslashes($html));
		$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
		$html = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $html);
		$html = preg_replace('#<audio(.*?)>(.*?)</audio>#is', '', $html);
		$html = preg_replace('#<video(.*?)>(.*?)</video>#is', '', $html);
		return $html;
	}

	/**
	*	@since 2.6.6
	* 	List of all languages with locale name and native name
	*  	@since 4.0.8 - Add all the languages to option table to avoid the memory exhausted error
	* 	@return array An associative array of languages.
	*/
	public static function get_language_list()
	{
		if(false === get_option('wt_pklist_languages_list')){
			update_option('wt_pklist_languages_list',self::all_wt_pklist_languages());
		}

		/**
		*	Alter language list.
		*	@param array An associative array of languages.
		*/
		$wt_pklist_language_list=apply_filters('wt_pklist_alter_language_list', get_option('wt_pklist_languages_list',array()));

		return $wt_pklist_language_list;
	}

	/**
	*	@since 2.6.6 Get list of RTL languages
	*	@return array an associative array of RTL languages with locale name, native name, locale code, WP locale code
	*/
	public static function get_rtl_languages()
	{
		$rtl_lang_keys=array('ar', 'dv', 'he_IL', 'ps', 'fa_IR', 'ur');

		/**
		*	Alter RTL language list.
		*	@param array RTL language locale codes (WP specific locale codes)
		*/
		$rtl_lang_keys=apply_filters('wt_pklist_alter_rtl_language_list', $rtl_lang_keys);

		$lang_list=self::get_language_list(); //taking full language list		
		
		$rtl_lang_keys=array_flip($rtl_lang_keys);
		return array_intersect_key($lang_list, $rtl_lang_keys);
	}

	/**
	*	@since 2.6.6 Checks user enabled RTL and current language needs RTL support.
	*	@return boolean
	*/
	public static function is_enable_rtl_support()
	{
		if(!is_null(self::$is_enable_rtl)) /* already checked then return the stored result */
		{
			return self::$is_enable_rtl;
		}
		$rtl_languages=self::get_rtl_languages();
		$current_lang=get_locale();
		
		self::$is_enable_rtl=isset($rtl_languages[$current_lang]); 
		return self::$is_enable_rtl;
	}

	/**
    * @since 2.7.8
    * Compatible with multi currency and currency switcher plugin
    * 2.7.9 - bug fix - compatible with WC version below 4.1.0
    */
    public static function wf_display_price($user_currency,$order,$price,$from=""){

    	$order_id=WC()->version<'2.7.0' ? $order->id : $order->get_id();
    	$price = (float)$price;

    	if(WC()->version<'4.1.0'){
    		$symbols = self::wf_get_woocommerce_currency_symbols();
    	}else{
    		$symbols = get_woocommerce_currency_symbols();
    	}

    	if(get_option('woocommerce_currency_pos')){
    		$currency_pos = get_option('woocommerce_currency_pos');
    	}else{
    		$currency_pos = "left";
    	}
    	
    	$wc_currency_symbol = isset( $symbols[ $user_currency ] ) ? $symbols[ $user_currency ] : '';

    	if(get_option('woocommerce_price_num_decimals')){
    		$decimal = wc_get_price_decimals();
    	}else{
    		$decimal = 0;
    	}
    	
    	if(get_option('woocommerce_price_decimal_sep')){
    		$decimal_sep = wc_get_price_decimal_separator();
    	}else{
    		$decimal_sep = ".";
    	} 

    	if(get_option('woocommerce_price_thousand_sep')){
    		$thousand_sep = wc_get_price_thousand_separator();
    	}else{
    		$thousand_sep = ",";
    	}

    	if(is_plugin_active('woocommerce-currency-switcher/index.php'))
		{
			if(class_exists('WOOCS')){
				global $WOOCS;
				$multi_currencies = $WOOCS->get_currencies();
				$user_selected_currency = $multi_currencies[$user_currency];
				$currency_symbol = "";
				if(!empty($user_selected_currency)){
					if(array_key_exists('position', $user_selected_currency))
					{
						$currency_pos = $user_selected_currency["position"];
					}
					if(array_key_exists('decimals', $user_selected_currency))
					{
						$decimal = $user_selected_currency["decimals"];
					}
					if(array_key_exists('symbol',$user_selected_currency))
					{
						$wc_currency_symbol = $user_selected_currency["symbol"];
					}
				}
			}
		}elseif(is_plugin_active('woo-multi-currency/woo-multi-currency.php'))
		{
			if ( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) 
			{
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
			   	if(array_key_exists($user_currency, $wmc_order_info))
			   	{
			   		if(array_key_exists('pos', $wmc_order_info[$user_currency]))
			   		{
				   		$currency_pos = $wmc_order_info[$user_currency]['pos'];
				   	}
			   		if(array_key_exists('decimals', $wmc_order_info[$user_currency]))
			   		{
				   		$decimal = $wmc_order_info[$user_currency]['decimals'];
				   	}
			   	}
			}
		}

		if("" === trim($decimal)){
			$decimal = 0;
		}
		if("" === trim($decimal_sep)){
			$decimal_sep = ".";
		}
		if("" === trim($thousand_sep)){
			$thousand_sep = ",";
		}

		$wc_currency_symbol = apply_filters('wt_pklist_alter_currency_symbol',$wc_currency_symbol,$symbols,$user_currency,$order,$price);

		$currency_pos = apply_filters('wt_pklist_alter_currency_symbol_position',$currency_pos,$symbols,$wc_currency_symbol,$user_currency,$order,$price);
		$decimal = apply_filters('wt_pklist_alter_currency_decimal',$decimal,$wc_currency_symbol,$user_currency,$order,$price);
		$decimal_sep = apply_filters('wt_pklist_alter_currency_decimal_seperator',$decimal_sep,$symbols,$wc_currency_symbol,$user_currency,$order,$price);
    	$thousand_sep = apply_filters('wt_pklist_alter_currency_thousand_seperator',$thousand_sep,$symbols,$wc_currency_symbol,$user_currency,$order,$price);
    	$wf_formatted_price = number_format($price,$decimal,$decimal_sep,$thousand_sep);
    	if("qrcode" === $from){
			return $wf_formatted_price.' '.$user_currency;
		}
		if("" !== trim($wc_currency_symbol)){
			switch ($currency_pos) {
				case 'left':
					$result = $wc_currency_symbol.$wf_formatted_price;
					break;
				case 'right':
					$result = $wf_formatted_price.$wc_currency_symbol;
					break;
				case 'left_space':
					$result = $wc_currency_symbol.' '.$wf_formatted_price;
					break;
				case 'right_space':
					$result = $wf_formatted_price.' '.$wc_currency_symbol;
					break;
				default:
					$result = $wc_currency_symbol.$wf_formatted_price;
					break;
			}
		}else{
			$result = $wf_formatted_price.' '.$user_currency;
		}

		$result = apply_filters('wt_pklist_change_currency_format',$result,$symbols,$wc_currency_symbol,$currency_pos,$decimal,$decimal_sep,$thousand_sep,$user_currency,$price,$order);

		return "<span>".$result."</span>";	
    }

    public static function wf_get_decimal_price($user_currency,$order){
    	$order_id=WC()->version<'2.7.0' ? $order->id : $order->get_id();
    	if(true === get_option('woocommerce_price_num_decimals')){
    		$decimal = wc_get_price_decimals();
    	}else{
    		$decimal = 0;
    	}

    	if(is_plugin_active('woocommerce-currency-switcher/index.php'))
		{
			if(class_exists('WOOCS')){
				global $WOOCS;
				$multi_currencies = $WOOCS->get_currencies();
				$user_selected_currency = $multi_currencies[$user_currency];
				if(!empty($user_selected_currency)){
					if(array_key_exists('decimals', $user_selected_currency))
					{
						$decimal = $user_selected_currency["decimals"];
					}
				}
			}
		}elseif(is_plugin_active('woo-multi-currency/woo-multi-currency.php'))
		{
			if ( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) 
			{
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
			   	if(array_key_exists($user_currency, $wmc_order_info))
			   	{
			   		if(array_key_exists('decimals', $wmc_order_info[$user_currency]))
			   		{
				   		$decimal = $wmc_order_info[$user_currency]['decimals'];
				   	}
			   	}
			}
		}

		if("" === trim($decimal)){
			$decimal = 0;
		}

		return $decimal;
    }
    /**
    * @since 2.7.8
    * Convert the price with multi currency and currency switcher plugin
    */
    public static function wf_convert_to_user_currency($item_price,$user_currency,$order){

    	$rate = 1;
    	$order_id=WC()->version<'2.7.0' ? $order->id : $order->get_id();
    	$item_price = (float)$item_price;

    	/* currency switcher - packinglist product table */
    	if(is_plugin_active('woocommerce-currency-switcher/index.php')) 
		{	
			if ( metadata_exists( 'post', $order_id, '_woocs_order_rate' ) ) {
			    $rate = get_post_meta( $order_id, '_woocs_order_rate', true );
			}elseif( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) {
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
				$rate = $wmc_order_info[$user_currency]['rate'];
			}
		}
		elseif(is_plugin_active('woo-multi-currency/woo-multi-currency.php')) /* Multi currency - packinglist product table */
		{
			if ( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) {
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
				$rate = $wmc_order_info[$user_currency]['rate'];
			}elseif ( metadata_exists( 'post', $order_id, '_woocs_order_rate' ) ) {
			    $rate = get_post_meta( $order_id, '_woocs_order_rate', true );
			}
		}
		else
		{
			/* currency switcher / multicurrency even plugins are not available - packinglist product table */
			if ( metadata_exists( 'post', $order_id, '_woocs_order_rate' ) ) {
			    $rate = get_post_meta( $order_id, '_woocs_order_rate', true );
			}elseif( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) {
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
				$rate = $wmc_order_info[$user_currency]['rate'];
			}
		}
		return $item_price * (float)$rate;
    }

    /**
    * @since 2.7.9
    * Get the currecy symbols array for the WC < 4.1.0
    */
    public static function wf_get_woocommerce_currency_symbols(){
    	$symbols = array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => 'Afl.',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BYN' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x20be;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => '&#8376;',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'MDL',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRU' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => 'N&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#1088;&#1089;&#1076;',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STN' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VES' => 'Bs.S',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'CFA',
			'XCD' => '&#36;',
			'XOF' => 'CFA',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);
		return $symbols;
    }

    /**
    * @since 2.8.0
    * Shipping address with order currency symbol
    */
	public static function wf_shipping_formated_price($order){
		$order_id=(WC()->version<'2.7.0' ? $order->id : $order->get_id());
		$user_currency = get_post_meta($order_id,'_order_currency',true);
		$tax_display = get_option( 'woocommerce_tax_display_cart' );

		$tax_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_taxstatus');
		$incl_tax=in_array('in_tax', $tax_type);

		if ( 0 < abs( (float) $order->get_shipping_total() ) ) {
			if(!$incl_tax){
				// Show shipping excluding tax.
				$shipping = apply_filters('wt_pklist_change_price_format',$user_currency,$order,$order->get_shipping_total());
				if ( (float) $order->get_shipping_tax() > 0 && $order->get_prices_include_tax() ) {
					$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_tax_label', '&nbsp;<small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>', $order, $tax_display );
				}
			} else {
				// Show shipping including tax.
				$tot_shipping_amount = $order->get_shipping_total() + $order->get_shipping_tax();
				$shipping = apply_filters('wt_pklist_change_price_format',$user_currency,$order,$tot_shipping_amount);
				if ( (float) $order->get_shipping_tax() > 0 && ! $order->get_prices_include_tax() ) {
				$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_tax_label', '&nbsp;<small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>', $order, $tax_display );
				}
			}
			/* translators: %s: method */
			$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_shipped_via', '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $order->get_shipping_method() ) . '</small>', $order );

		} elseif ( $order->get_shipping_method() ) {
			$shipping = $order->get_shipping_method();
		} else {
			$shipping = __( 'Free!', 'woocommerce' );
		}
		return $shipping;
	}

    /**
    * @since 2.8.0
    * Generate PDF file name for invoice template
    */

    public static function get_invoice_pdf_name($template_type,$order_ids,$module_id){

		$order = wc_get_order( $order_ids[0] );

		Wf_Woocommerce_Packing_List_Invoice::generate_invoice_number($order,true,'set');
		
		$wf_invoice_pdf_name_format = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_custom_pdf_name', $module_id);
		$wf_invoice_pdf_name_prefix = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_custom_pdf_name_prefix', $module_id);

		if("[prefix][order_no]" === $wf_invoice_pdf_name_format){
			$invoice_pdf_name_number_pos = $order->get_order_number();
		}else{
			$invoice_pdf_name_number_pos = get_post_meta($order_ids[0],'wf_invoice_number',true);
		}

		if("" === trim($wf_invoice_pdf_name_prefix)){
			$invoice_pdf_name_prefix_pos = "Invoice_";
		}else{
			$invoice_pdf_name_prefix_pos = $wf_invoice_pdf_name_prefix;
		}

		if("[prefix][invoice_no]" === $wf_invoice_pdf_name_format){
			$invoice_pdf_name_format = $wf_invoice_pdf_name_format;
		}else{
			$invoice_pdf_name_format = "[prefix][order_no]";
		}

		return str_replace(array('[prefix]','[order_no]','[invoice_no]'),array($invoice_pdf_name_prefix_pos,$invoice_pdf_name_number_pos,$invoice_pdf_name_number_pos),$invoice_pdf_name_format); 
	}

	public static function wf_search_order_by_invoice_number($search_fields){
		array_push($search_fields, 'wf_invoice_number');
		return $search_fields;
	}

	public function save_plugin_version_in_db(){
		if(false === get_option('wfpklist_basic_version')){
            update_option('wfpklist_basic_version_prev','none');
        }else{
            $prev_version = get_option('wfpklist_basic_version','none');
            update_option('wfpklist_basic_version_prev',$prev_version);
        }
        update_option('wfpklist_basic_version',WF_PKLIST_VERSION);
	}

	public static function check_if_mpdf_used(){
		$active_pdf_library = Wf_Woocommerce_Packing_List::get_option('active_pdf_library');
		if("mpdf" === $active_pdf_library){
			return true;
		}
		return false;
	}

	public static function qrcode_barcode_visibility($html,$template_type){
		$show_qrcode_placeholder = apply_filters('wt_pklist_show_qrcode_placeholder_in_template',false,$template_type);
		if(!$show_qrcode_placeholder){
			$html = str_replace('wfte_img_barcode wfte_hidden','wfte_img_barcode',$html);
			if (false !== strpos($html, 'wfte_img_qrcode')){
				$html = str_replace('wfte_img_qrcode','wfte_img_qrcode wfte_hidden',$html);
			}
		}
		return $html;
	}

	public function advanced_settings()
	{
		$out=array('key'=>'', 'val'=>'', 'success'=>false, 'msg'=>__('Error', 'print-invoices-packing-slip-labels-for-woocommerce'));
		$warn_msg=__('Please enter mandatory fields','print-invoices-packing-slip-labels-for-woocommerce');
		
		if(Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
    	{
			if(isset($_POST['wt_pklist_custom_field_btn']))  
			{
			    //additional fields for checkout
				if(isset($_POST['wt_pklist_new_custom_field_title']) && isset($_POST['wt_pklist_new_custom_field_key']) && isset($_POST['wt_pklist_custom_field_type'])) 
		        {
		        	if("" !== trim($_POST['wt_pklist_new_custom_field_title']) && "" !== trim($_POST['wt_pklist_new_custom_field_key']))
		        	{
		        		$custom_field_type=sanitize_text_field($_POST['wt_pklist_custom_field_type']);
		        		if("order_meta" === $custom_field_type)
		        		{
		        			$module_base = (isset($_POST['wt_pklist_settings_base']) ? sanitize_text_field($_POST['wt_pklist_settings_base']) : 'main');
							$module_id = ("main" === $module_base ? '' : Wf_Woocommerce_Packing_List::get_module_id($module_base));
							$add_only = (isset($_POST['add_only']) ? true : false);
		        			$field_config=array(
		        				'order_meta'=>array(
		        					'list'=>'wf_additional_data_fields',
		        					'selected'=>'wf_'.$module_base.'_contactno_email',
		        				),
		        			);

		        			/* form input */
		        			$new_meta_key=sanitize_text_field($_POST['wt_pklist_new_custom_field_key']);		            
        					$new_meta_vl=sanitize_text_field($_POST['wt_pklist_new_custom_field_title']);

        					/* option key names for full list, selected list */
        					$list_field=$field_config[$custom_field_type]['list'];
        					$val_field=$field_config[$custom_field_type]['selected'];
        					
        					/* list of user created items */
        					$user_created=Wf_Woocommerce_Packing_List::get_option($list_field); //this is plugin main setting so no need to specify module base

        					/* updating new item to user created list */
        					$old_meta_key = "";
        					$old_meta_key_label = "";
        					if(!empty($user_created) && is_array($user_created)){
        						$old_meta_key = function_exists('array_key_first') ? array_key_first($user_created): key( array_slice( $user_created, 0, 1, true ) );
								if (null === $old_meta_key) {
								    $old_meta_key = ""; // An error should be handled here
								} else {
								    $old_meta_key_label = $user_created[$old_meta_key];
								}
        					}

        					$user_created = array();
        					$action=(isset($user_created[$new_meta_key]) ? 'edit' : 'add');
				            
				            $can_add_item=true;
        					if("edit" === $action && $add_only)
        					{
        						$can_add_item=false;
        					}

        					if($can_add_item)
        					{	

				            	$user_created[$new_meta_key] = $new_meta_vl;
				            	Wf_Woocommerce_Packing_List::update_option($list_field, $user_created);
				            }

				            if(!$add_only)
				            {
					            $vl=Wf_Woocommerce_Packing_List::get_option($val_field, $module_id);
					            $user_selected_arr =("" !== $vl && is_array($vl) ? $vl : array());			            

					            if(!in_array($new_meta_key, $user_selected_arr)) 
					            {
					                $user_selected_arr[] = $new_meta_key;
					                Wf_Woocommerce_Packing_List::update_option($val_field, $user_selected_arr, $module_id);			                
					            }
					        }

					        if($can_add_item)
					        {
					            $new_meta_key_display=Wf_Woocommerce_Packing_List::get_display_key($new_meta_key);

					            $dc_slug=self::sanitize_css_class_name($new_meta_key_display); /* This is for Dynamic customizer */

					            $out=array('key'=>$new_meta_key, 'val'=>$new_meta_vl.$new_meta_key_display, 'dc_slug'=>$dc_slug, 'success'=>true, 'action'=>$action, 'old_meta_key' => $old_meta_key, 'old_meta_key_label' => $old_meta_key_label, 'new_meta_label' => $new_meta_vl);
					        }else
					        {
					        	$out['msg']=__('Item with same meta key already exists', 'print-invoices-packing-slip-labels-for-woocommerce');
					        }
		        		}

		        	}else
		        	{
		        		$out['msg']=$warn_msg;
		        	}
		        }
		    }
		}
	    echo json_encode($out);
		exit();
	}

	public static function sanitize_css_class_name($str)
	{
		return preg_replace('/[^\-_a-zA-Z0-9]+/', '', $str);
	}

    public static function order_meta_dropdown_list(){
    	$order_meta_query = array();
    	if(isset($_GET['page'])){
    		if("wf_woocommerce_packing_list_invoice" === $_GET['page']){
    			global $wpdb;
		    	$order_meta_selected_list = Wf_Woocommerce_Packing_List::get_option('wf_additional_data_fields');
		    	$first_meta_key = function_exists('array_key_first') ? array_key_first($order_meta_selected_list): key( array_slice( $order_meta_selected_list, 0, 1, true ) );
		    	$user_added_arr = array();
		    	if (null !== $first_meta_key) {
				    $user_added_arr[] = array('label' => $first_meta_key);
				}
		        $order_meta_query = $user_added_arr;
    		}
    	}
        return $order_meta_query;
    }

    /**
     * @since 3.0.2
     * Added target=_blank to the print invoice button on order listing of my account page
     */
    public function action_after_account_orders_js() {
	    ?>
	    <script>
	    (function($){
            $('a.wt_pklist_invoice_print').attr('target','_blank');
	    })(jQuery);
	    </script>
	    <?php
	}

	/**
	 * @since 3.0.3
	 * Tool for deleting all the invoice numbers
	 */
	public function wt_pklist_delete_all_invoice_numbers_tool($tools){
		$article_url = "https://www.webtoffee.com/reset-delete-existing-invoice-numbers";

		$tool_description = sprintf('%1$s<br><strong class="red">%2$s</strong>',__( 'This will remove all invoice numbers created by WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels by WebToffee.', 'print-invoices-packing-slip-labels-for-woocommerce' ),__( 'Note:', 'print-invoices-packing-slip-labels-for-woocommerce' ))." ".sprintf(__( 'Before using this tool, please make sure you followed the steps described in this article %1$s how to reset/delete existing invoice numbers%2$s.', 'print-invoices-packing-slip-labels-for-woocommerce' ),'<a href="' . esc_url( $article_url ) . '">','</a>');
		
		$tools['wf_pklist_delete_all_invoice_number'] = array(
	        'name' => __('Delete all generated invoice numbers', 'print-invoices-packing-slip-labels-for-woocommerce'),
	        'button' => __('Delete',  'print-invoices-packing-slip-labels-for-woocommerce'), 
	        'desc'   => $tool_description,
	        'callback' => array( $this, 'wf_pklist_delete_all_invoice_numbers_func' ),
	    );
	    return $tools;
	}

	public function wf_pklist_delete_all_invoice_numbers_func(){
		delete_post_meta_by_key( 'wf_invoice_number' );
		$invoice_module_id=Wf_Woocommerce_Packing_List::get_module_id('invoice');
		$enable_invoice = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$invoice_module_id);
		if((Wf_Woocommerce_Packing_List_Public::module_exists('invoice')) && ("Yes" === $enable_invoice)){
			$this->wt_get_empty_invoice_number_count();
		}
	}

	public function wt_pklist_action_scheduler_for_invoice_number_count(){
		$invoice_module_id=Wf_Woocommerce_Packing_List::get_module_id('invoice');
		$enable_invoice = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$invoice_module_id);
		$group = "wt_pklist_get_invoice_number_count_auto_generation";
		if((Wf_Woocommerce_Packing_List_Public::module_exists('invoice')) && ("Yes" === $enable_invoice)){
			if ( false === as_next_scheduled_action( 'update_empty_invoice_number_count' ) ) {
			        as_schedule_recurring_action( time(), 86400, 'update_empty_invoice_number_count', array(), $group );
			}
		}else{
			if (as_next_scheduled_action('update_empty_invoice_number_count', array(), $group) === true) {
	            as_unschedule_all_actions('update_empty_invoice_number_count', array(), $group);
	        }
		}
	}

	public function wt_get_empty_invoice_number_count(){
		$invoice_module_id=Wf_Woocommerce_Packing_List::get_module_id('invoice');
		$enable_invoice = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$invoice_module_id);
		$empty_count = count(self::get_order_ids_for_invoice_number_generation($invoice_module_id));
		update_option('invoice_empty_count',$empty_count);
	}

	public function wt_pklist_action_scheduler_for_invoice_number(){
		$data = self::check_before_auto_generating_invoice_no();
		$group = "wt_pklist_invoice_number_auto_generation";
		if(true === $data["invoice_enabled"] && $data["order_empty_invoice_count"] > 0){
			if((true === $data["auto_generate"]) && (20 < $data["order_empty_invoice_count"]) && (as_next_scheduled_action('wt_pklist_schedule_auto_generate_invoice_number', array(), $group) === false)){
				as_enqueue_async_action('wt_pklist_schedule_auto_generate_invoice_number', array(), $group);
			}elseif(true === $data["auto_generate"] && (20 >= $data["order_empty_invoice_count"] && 0 < $data["order_empty_invoice_count"])){
				do_action('wt_pklist_auto_generate_invoice_number_module');
			}
		}else{
			if (as_next_scheduled_action('wt_pklist_schedule_auto_generate_invoice_number', array(), $group) === true && false === $data["invoice_enabled"]) {
	            as_unschedule_all_actions('wt_pklist_schedule_auto_generate_invoice_number', array(), $group);
	        }
		}
	}

	public static function check_before_auto_generating_invoice_no(){
		global $pagenow, $typenow, $post;
		$auto_generate = false;
		$invoice_enabled = false;
		$result = array('auto_generate' => false, 'order_empty_invoice_count' => 0, 'invoice_enabled' => false);
		if('edit.php' === $pagenow && (isset($_GET['post_type']) && "shop_order" === $_GET['post_type'])){
			$result["auto_generate"] = true;
		}elseif('post.php' === $pagenow){
			$req_type = "";
			if ('post' === $typenow && isset($_GET['post']) && !empty($_GET['post'])) {
		        $req_type = $post->post_type;
		    } elseif (empty($typenow) && !empty($_GET['post'])) {
		        $post = get_post($_GET['post']);
		        $req_type = $post->post_type;
		    }

		    if("shop_order" === $req_type){
		    	$result["auto_generate"] = true;
		    }
		}

		$invoice_module_id=Wf_Woocommerce_Packing_List::get_module_id('invoice');
		$enable_invoice = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$invoice_module_id);
		if((Wf_Woocommerce_Packing_List_Public::module_exists('invoice')) && ("Yes" === $enable_invoice)){
			$result["invoice_enabled"] = true;
		}
		if(true === $result["auto_generate"] && true === $result["invoice_enabled"]){
			$result["order_empty_invoice_count"] = (int)get_option('invoice_empty_count',true);
		}
		return $result;
	}

	public function action_for_auto_generate_invoice_number()
	{
		do_action('wt_pklist_auto_generate_invoice_number_module');
	}

	public static function get_order_ids_for_invoice_number_generation($module_id){
		$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$module_id);
		$order_meta_query_arr = array();
		if(!empty($generate_invoice_for)){
			$invoice_for_prev_install_order = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_prev_install_orders',$module_id);
	   		$args = array(
						'orderby'	=> 'ID',
						'order' => 'ASC',
					    'posts_per_page' => -1,
					    'post_type' => 'shop_order',
					    'post_status' => $generate_invoice_for,
					    'fields' => 'ids',
					    'meta_query' => array(
						   'relation' => 'OR',
						    array(
						     'key' => 'wf_invoice_number',
						     'compare' => 'NOT EXISTS'
						    ),
						    array(
						     'key' => 'wf_invoice_number',
						     'value' => ''
						    ),
						    array(
						     'key' => 'wf_invoice_number',
						     'value' => NULL
						    )
						)
					);

	   		if("No" === $invoice_for_prev_install_order){
	   			$utc_timestamp = get_option('wt_pklist_installation_date');
				$utc_timestamp_converted = date( 'Y-m-d h:i:s', $utc_timestamp );
				$local_timestamp = get_date_from_gmt( $utc_timestamp_converted, 'Y-m-d h:i:s' );
				$args['date_query'] = array('after' => $local_timestamp);
	   		}
	   		$empty_invoice_order_qry = new WP_Query($args);
	   		$order_meta_query_arr = $empty_invoice_order_qry->posts;
		}
		update_option('invoice_empty_count',count($order_meta_query_arr));
		return $order_meta_query_arr;
	}

	/**
	 * @since 3.0.5
	 * Function to add the count when user generating the documents
	 */
	public static function created_document_count($order_id,$template_type){
		$check_old_order = self::check_the_order_is_old($order_id);
		$update_count = false;
		if(!$check_old_order){
			$order_docs = get_post_meta($order_id,'_created_document',true);
			if($order_docs){
				if(is_array($order_docs) && !in_array($template_type, $order_docs)){
					array_push($order_docs,$template_type);
					update_post_meta($order_id,'_created_document',$order_docs);
					$update_count = true;
				}
			}else{
				$order_docs = array($template_type);
				update_post_meta($order_id,'_created_document',$order_docs);
				$update_count = true;
			}
		}
		if($update_count){
			if ( false !== get_option( 'wt_created_document_count' )) {
				$count = (int)get_option( 'wt_created_document_count' );
				update_option('wt_created_document_count',$count+1);
			}else{
				update_option('wt_created_document_count',1);
			}
		}
	}

	/**
	 * @since 3.0.5
	 * Function to check whether the order is old or not from the installation date
	 */
	public static function check_the_order_is_old($order_id){
   		$order_date_format='Y-m-d h:i:s';
   		$order_date=(get_the_date($order_date_format,$order_id));
   		if(false === get_option('wt_pklist_installation_date')){
			if(get_option('wt_pklist_start_date')){
				$install_date = get_option('wt_pklist_start_date',time());
			}else{
				$install_date = time();
			}
			update_option('wt_pklist_installation_date',$install_date);
		}
        $utc_timestamp = get_option('wt_pklist_installation_date');
		$utc_timestamp_converted = date( 'Y-m-d h:i:s', $utc_timestamp );
		$local_timestamp = get_date_from_gmt( $utc_timestamp_converted, 'Y-m-d h:i:s' );
   		if($order_date < $local_timestamp){
   			return true;
   		}
	   	return false;
	}

	/**
	 *  @since 4.0.0
	 *	Enable/disable the document modules using ajax
	 */

	public static function document_module_enable_disable(){
		// Check nonce:
        if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
		{
			echo json_encode(array('status' => true, 'doc_set' => 0, 'message' => __('You are not allowed to do this action','print-invoices-packing-slip-labels-for-woocommerce')));
			exit();
		}

		$output = array('status' => true);
	    $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
	    if(false === $wt_pklist_common_modules)
	    {
	        $wt_pklist_common_modules=array();
	    }
	    if(isset($_POST['doc_module_name']))
	    {	
	    	$wt_pklist_post = explode('wt_pklist_',$_POST['doc_module_name']);
	    	$wt_pklist_common_modules[$wt_pklist_post[1]]=$_POST['doc_module_set'];
	    	$output['doc_set'] = 1;
	    	$output['message'] = __('Updated','print-invoices-packing-slip-labels-for-woocommerce');	
	    }else{
	    	foreach($wt_pklist_common_modules as $k=>$v)
	        {
				$wt_pklist_common_modules[$k]=0;
	        }
	        $output['doc_set'] = 2;
	        $output['message'] = __('No modules','print-invoices-packing-slip-labels-for-woocommerce');
	    }
	    update_option('wt_pklist_common_modules',$wt_pklist_common_modules);
	    echo json_encode($output);
		exit();
	}

	/**
	 * @since 4.0.0
	 * Check the general settings fields are filled or not
	 * if all are empty then open the form wizard
	 * else some are empty then open classic form
	 */
	public static function check_general_settings(){
		if(	"" === Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_companyname') &&
			"" === Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_address_line1') && 
            "" === Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_address_line2') && 
            "" === Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_city') && 
            "" === Wf_Woocommerce_Packing_List::get_option('wf_country') && 
            "" === Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_postalcode')) 
            {
            	return false;
            }
        return true;
	}

	public static function check_full_refunded_property($order){
		$all_refund_orders = $order->get_refunds();
		$number_of_refunds = count($all_refund_orders);
		$order_status = ( WC()->version < '2.7.0' ) ? $order->status : $order->get_status();
		if(1 === $number_of_refunds && $order_status == "refunded"){
			$order->full_refunded = 1;
		}else{
			$order->full_refunded = 0;
		}
		return $order;
	}

	public static function not_activated_pro_addons($excl_addon = ""){
		$pro_addons_list = array(
            'wt_ipc_addon'  => 'wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php',
            'wt_sdd_addon'  => 'wt-woocommerce-shippinglabel-addon/wt-woocommerce-shippinglabel-addon.php',
            'wt_pl_addon'   => 'wt-woocommerce-picklist-addon/wt-woocommerce-picklist-addon.php',
            'wt_pi_addon'   => 'wt-woocommerce-proforma-addon/wt-woocommerce-proforma-addon.php',
            'wt_al_addon'   => 'wt-woocommerce-addresslabel-addon/wt-woocommerce-addresslabel-addon.php',
            'wt_qr_addon'   => 'qrcode-addon-for-woocommerce-pdf-invoices/qrcode-addon-for-woocommerce-pdf-invoices.php',
        );
        if("" !== $excl_addon && isset($pro_addons_list[$excl_addon])){
        	unset($pro_addons_list[$excl_addon]);
        }
        $not_activated_pro_addons = array();
        foreach($pro_addons_list as $pro_addon_key => $pro_addon){
            if(!is_plugin_active($pro_addon)){
                array_push($not_activated_pro_addons, $pro_addon_key);
            }
        }
        return $not_activated_pro_addons;
	}

	/**
	 * Added to hide the shipping address if it is an empty
	 * Added to use the billing address as shipping address
	 * @since 4.0.4
	 */
	public static function hide_empty_shipping_address($html,$template_type,$order){
		$use_billing_address = apply_filters('wt_pklist_use_billing_address_for_shipping_address',true,$template_type,$order);
		if(!empty($order) && !$use_billing_address){
			$shipping_address = $order->get_formatted_shipping_address();
			if(empty($shipping_address))
			{
				$html .='<style>
				.wfte_shipping_address{
					display:none !important;
				}
				</style>';
			}
		}
		return $html;
	}
	
	/**
	 * Function to get all the language list for pdf
	 * @since 4.0.8
	 * @return array
	 */
	public static function all_wt_pklist_languages(){
		$wt_pklist_language_list =
			array (
				'af' => array (
					'name' => 'Afrikaans',
					'native_name' => 'Afrikaans',
					'locale_code' => 'af',
					'wp_locale_code' => 'af',
					'is_rtl' => false,
				),
				'ak' => array (
					'name' => 'Akan',
					'native_name' => 'Akan',
					'locale_code' => 'ak',
					'wp_locale_code' => 'ak',
					'is_rtl' => false,
				),
				'sq' => array (
					'name' => 'Albanian',
					'native_name' => 'Shqip',
					'locale_code' => 'sq',
					'wp_locale_code' => 'sq',
					'is_rtl' => false,
				),
				'arq' => array (
					'name' => 'Algerian Arabic',
					'native_name' => 'الدارجة الجزايرية',
					'locale_code' => 'arq',
					'wp_locale_code' => 'arq',
					'is_rtl' => false,
				),
				'am' => array (
					'name' => 'Amharic',
					'native_name' => 'አማርኛ',
					'locale_code' => 'am',
					'wp_locale_code' => 'am',
					'is_rtl' => false,
				),
				'ar' => array (
					'name' => 'Arabic',
					'native_name' => 'العربية',
					'locale_code' => 'ar',
					'wp_locale_code' => 'ar',
					'is_rtl' => true,
				),
				'hy' => array (
					'name' => 'Armenian',
					'native_name' => 'Հայերեն',
					'locale_code' => 'hy',
					'wp_locale_code' => 'hy',
					'is_rtl' => false,
				),
				'rup_MK' => array (
					'name' => 'Aromanian',
					'native_name' => 'Armãneashce',
					'locale_code' => 'rup',
					'wp_locale_code' => 'rup_MK',
					'is_rtl' => false,
				),
				'frp' => array (
					'name' => 'Arpitan',
					'native_name' => 'Arpitan',
					'locale_code' => 'frp',
					'wp_locale_code' => 'frp',
					'is_rtl' => false,
				),
				'as' => array (
					'name' => 'Assamese',
					'native_name' => 'অসমীয়া',
					'locale_code' => 'as',
					'wp_locale_code' => 'as',
					'is_rtl' => false,
				),
				'az' => 
				array (
					'name' => 'Azerbaijani',
					'native_name' => 'Azərbaycan dili',
					'locale_code' => 'az',
					'wp_locale_code' => 'az',
					'is_rtl' => false,
				),
				'az_TR' => 
				array (
					'name' => 'Azerbaijani (Turkey)',
					'native_name' => 'Azərbaycan Türkcəsi',
					'locale_code' => 'az-tr',
					'wp_locale_code' => 'az_TR',
					'is_rtl' => false,
				),
				'bcc' => 
				array (
					'name' => 'Balochi Southern',
					'native_name' => 'بلوچی مکرانی',
					'locale_code' => 'bcc',
					'wp_locale_code' => 'bcc',
					'is_rtl' => false,
				),
				'ba' => 
				array (
					'name' => 'Bashkir',
					'native_name' => 'башҡорт теле',
					'locale_code' => 'ba',
					'wp_locale_code' => 'ba',
					'is_rtl' => false,
				),
				'eu' => 
				array (
					'name' => 'Basque',
					'native_name' => 'Euskara',
					'locale_code' => 'eu',
					'wp_locale_code' => 'eu',
					'is_rtl' => false,
				),
				'bel' => 
				array (
					'name' => 'Belarusian',
					'native_name' => 'Беларуская мова',
					'locale_code' => 'bel',
					'wp_locale_code' => 'bel',
					'is_rtl' => false,
				),
				'bn_BD' => 
				array (
					'name' => 'Bengali',
					'native_name' => 'বাংলা',
					'locale_code' => 'bn',
					'wp_locale_code' => 'bn_BD',
					'is_rtl' => false,
				),
				'bs_BA' => 
				array (
					'name' => 'Bosnian',
					'native_name' => 'Bosanski',
					'locale_code' => 'bs',
					'wp_locale_code' => 'bs_BA',
					'is_rtl' => false,
				),
				'bre' => 
				array (
					'name' => 'Breton',
					'native_name' => 'Brezhoneg',
					'locale_code' => 'br',
					'wp_locale_code' => 'bre',
					'is_rtl' => false,
				),
				'bg_BG' => 
				array (
					'name' => 'Bulgarian',
					'native_name' => 'Български',
					'locale_code' => 'bg',
					'wp_locale_code' => 'bg_BG',
					'is_rtl' => false,
				),
				'ca' => 
				array (
					'name' => 'Catalan',
					'native_name' => 'Català',
					'locale_code' => 'ca',
					'wp_locale_code' => 'ca',
					'is_rtl' => false,
				),
				'bal' => 
				array (
					'name' => 'Catalan (Balear)',
					'native_name' => 'Català (Balear)',
					'locale_code' => 'bal',
					'wp_locale_code' => 'bal',
					'is_rtl' => false,
				),
				'ceb' => 
				array (
					'name' => 'Cebuano',
					'native_name' => 'Cebuano',
					'locale_code' => 'ceb',
					'wp_locale_code' => 'ceb',
					'is_rtl' => false,
				),
				'zh_CN' => 
				array (
					'name' => 'Chinese (China)',
					'native_name' => '简体中文',
					'locale_code' => 'zh-cn',
					'wp_locale_code' => 'zh_CN',
					'is_rtl' => false,
				),
				'zh_HK' => 
				array (
					'name' => 'Chinese (Hong Kong)',
					'native_name' => '香港中文版',
					'locale_code' => 'zh-hk',
					'wp_locale_code' => 'zh_HK',
					'is_rtl' => false,
				),
				'zh_TW' => 
				array (
					'name' => 'Chinese (Taiwan)',
					'native_name' => '繁體中文',
					'locale_code' => 'zh-tw',
					'wp_locale_code' => 'zh_TW',
					'is_rtl' => false,
				),
				'co' => 
				array (
					'name' => 'Corsican',
					'native_name' => 'Corsu',
					'locale_code' => 'co',
					'wp_locale_code' => 'co',
					'is_rtl' => false,
				),
				'hr' => 
				array (
					'name' => 'Croatian',
					'native_name' => 'Hrvatski',
					'locale_code' => 'hr',
					'wp_locale_code' => 'hr',
					'is_rtl' => false,
				),
				'cs_CZ' => 
				array (
					'name' => 'Czech',
					'native_name' => 'Čeština‎',
					'locale_code' => 'cs',
					'wp_locale_code' => 'cs_CZ',
					'is_rtl' => false,
				),
				'da_DK' => 
				array (
					'name' => 'Danish',
					'native_name' => 'Dansk',
					'locale_code' => 'da',
					'wp_locale_code' => 'da_DK',
					'is_rtl' => false,
				),
				'dv' => 
				array (
					'name' => 'Dhivehi',
					'native_name' => 'ދިވެހި',
					'locale_code' => 'dv',
					'wp_locale_code' => 'dv',
					'is_rtl' => true,
				),
				'nl_NL' => 
				array (
					'name' => 'Dutch',
					'native_name' => 'Nederlands',
					'locale_code' => 'nl',
					'wp_locale_code' => 'nl_NL',
					'is_rtl' => false,
				),
				'nl_BE' => 
				array (
					'name' => 'Dutch (Belgium)',
					'native_name' => 'Nederlands (België)',
					'locale_code' => 'nl-be',
					'wp_locale_code' => 'nl_BE',
					'is_rtl' => false,
				),
				'dzo' => 
				array (
					'name' => 'Dzongkha',
					'native_name' => 'རྫོང་ཁ',
					'locale_code' => 'dzo',
					'wp_locale_code' => 'dzo',
					'is_rtl' => false,
				),
				'art_xemoji' => 
				array (
					'name' => 'Emoji',
					'native_name' => '🌏🌍🌎 (Emoji)',
					'locale_code' => 'art-xemoji',
					'wp_locale_code' => 'art_xemoji',
					'is_rtl' => false,
				),
				'en_US' => 
				array (
					'name' => 'English',
					'native_name' => 'English',
					'locale_code' => 'en',
					'wp_locale_code' => 'en_US',
					'is_rtl' => false,
				),
				'en_AU' => 
				array (
					'name' => 'English (Australia)',
					'native_name' => 'English (Australia)',
					'locale_code' => 'en-au',
					'wp_locale_code' => 'en_AU',
					'is_rtl' => false,
				),
				'en_CA' => 
				array (
					'name' => 'English (Canada)',
					'native_name' => 'English (Canada)',
					'locale_code' => 'en-ca',
					'wp_locale_code' => 'en_CA',
					'is_rtl' => false,
				),
				'en_NZ' => 
				array (
					'name' => 'English (New Zealand)',
					'native_name' => 'English (New Zealand)',
					'locale_code' => 'en-nz',
					'wp_locale_code' => 'en_NZ',
					'is_rtl' => false,
				),
				'en_ZA' => 
				array (
					'name' => 'English (South Africa)',
					'native_name' => 'English (South Africa)',
					'locale_code' => 'en-za',
					'wp_locale_code' => 'en_ZA',
					'is_rtl' => false,
				),
				'en_GB' => 
				array (
					'name' => 'English (UK)',
					'native_name' => 'English (UK)',
					'locale_code' => 'en-gb',
					'wp_locale_code' => 'en_GB',
					'is_rtl' => false,
				),
				'eo' => 
				array (
					'name' => 'Esperanto',
					'native_name' => 'Esperanto',
					'locale_code' => 'eo',
					'wp_locale_code' => 'eo',
					'is_rtl' => false,
				),
				'et' => 
				array (
					'name' => 'Estonian',
					'native_name' => 'Eesti',
					'locale_code' => 'et',
					'wp_locale_code' => 'et',
					'is_rtl' => false,
				),
				'fo' => 
				array (
					'name' => 'Faroese',
					'native_name' => 'Føroyskt',
					'locale_code' => 'fo',
					'wp_locale_code' => 'fo',
					'is_rtl' => false,
				),
				'fi' => 
				array (
					'name' => 'Finnish',
					'native_name' => 'Suomi',
					'locale_code' => 'fi',
					'wp_locale_code' => 'fi',
					'is_rtl' => false,
				),
				'fr_BE' => 
				array (
					'name' => 'French (Belgium)',
					'native_name' => 'Français de Belgique',
					'locale_code' => 'fr-be',
					'wp_locale_code' => 'fr_BE',
					'is_rtl' => false,
				),
				'fr_CA' => 
				array (
					'name' => 'French (Canada)',
					'native_name' => 'Français du Canada',
					'locale_code' => 'fr-ca',
					'wp_locale_code' => 'fr_CA',
					'is_rtl' => false,
				),
				'fr_FR' => 
				array (
					'name' => 'French (France)',
					'native_name' => 'Français',
					'locale_code' => 'fr',
					'wp_locale_code' => 'fr_FR',
					'is_rtl' => false,
				),
				'fy' => 
				array (
					'name' => 'Frisian',
					'native_name' => 'Frysk',
					'locale_code' => 'fy',
					'wp_locale_code' => 'fy',
					'is_rtl' => false,
				),
				'fur' => 
				array (
					'name' => 'Friulian',
					'native_name' => 'Friulian',
					'locale_code' => 'fur',
					'wp_locale_code' => 'fur',
					'is_rtl' => false,
				),
				'fuc' => 
				array (
					'name' => 'Fulah',
					'native_name' => 'Pulaar',
					'locale_code' => 'fuc',
					'wp_locale_code' => 'fuc',
					'is_rtl' => false,
				),
				'gl_ES' => 
				array (
					'name' => 'Galician',
					'native_name' => 'Galego',
					'locale_code' => 'gl',
					'wp_locale_code' => 'gl_ES',
					'is_rtl' => false,
				),
				'ka_GE' => 
				array (
					'name' => 'Georgian',
					'native_name' => 'ქართული',
					'locale_code' => 'ka',
					'wp_locale_code' => 'ka_GE',
					'is_rtl' => false,
				),
				'de_DE' => 
				array (
					'name' => 'German',
					'native_name' => 'Deutsch',
					'locale_code' => 'de',
					'wp_locale_code' => 'de_DE',
					'is_rtl' => false,
				),
				'de_CH' => 
				array (
					'name' => 'German (Switzerland)',
					'native_name' => 'Deutsch (Schweiz)',
					'locale_code' => 'de-ch',
					'wp_locale_code' => 'de_CH',
					'is_rtl' => false,
				),
				'el' => 
				array (
					'name' => 'Greek',
					'native_name' => 'Ελληνικά',
					'locale_code' => 'el',
					'wp_locale_code' => 'el',
					'is_rtl' => false,
				),
				'kal' => 
				array (
					'name' => 'Greenlandic',
					'native_name' => 'Kalaallisut',
					'locale_code' => 'kal',
					'wp_locale_code' => 'kal',
					'is_rtl' => false,
				),
				'gn' => 
				array (
					'name' => 'Guaraní',
					'native_name' => 'Avañe\'ẽ',
					'locale_code' => 'gn',
					'wp_locale_code' => 'gn',
					'is_rtl' => false,
				),
				'gu' => 
				array (
					'name' => 'Gujarati',
					'native_name' => 'ગુજરાતી',
					'locale_code' => 'gu',
					'wp_locale_code' => 'gu',
					'is_rtl' => false,
				),
				'haw_US' => 
				array (
					'name' => 'Hawaiian',
					'native_name' => 'Ōlelo Hawaiʻi',
					'locale_code' => 'haw',
					'wp_locale_code' => 'haw_US',
					'is_rtl' => false,
				),
				'haz' => 
				array (
					'name' => 'Hazaragi',
					'native_name' => 'هزاره گی',
					'locale_code' => 'haz',
					'wp_locale_code' => 'haz',
					'is_rtl' => false,
				),
				'he_IL' => 
				array (
					'name' => 'Hebrew',
					'native_name' => 'עִבְרִית',
					'locale_code' => 'he',
					'wp_locale_code' => 'he_IL',
					'is_rtl' => true,
				),
				'hi_IN' => 
				array (
					'name' => 'Hindi',
					'native_name' => 'हिन्दी',
					'locale_code' => 'hi',
					'wp_locale_code' => 'hi_IN',
					'is_rtl' => false,
				),
				'hu_HU' => 
				array (
					'name' => 'Hungarian',
					'native_name' => 'Magyar',
					'locale_code' => 'hu',
					'wp_locale_code' => 'hu_HU',
					'is_rtl' => false,
				),
				'is_IS' => 
				array (
					'name' => 'Icelandic',
					'native_name' => 'Íslenska',
					'locale_code' => 'is',
					'wp_locale_code' => 'is_IS',
					'is_rtl' => false,
				),
				'ido' => 
				array (
					'name' => 'Ido',
					'native_name' => 'Ido',
					'locale_code' => 'ido',
					'wp_locale_code' => 'ido',
					'is_rtl' => false,
				),
				'id_ID' => 
				array (
					'name' => 'Indonesian',
					'native_name' => 'Bahasa Indonesia',
					'locale_code' => 'id',
					'wp_locale_code' => 'id_ID',
					'is_rtl' => false,
				),
				'ga' => 
				array (
					'name' => 'Irish',
					'native_name' => 'Gaelige',
					'locale_code' => 'ga',
					'wp_locale_code' => 'ga',
					'is_rtl' => false,
				),
				'it_IT' => 
				array (
					'name' => 'Italian',
					'native_name' => 'Italiano',
					'locale_code' => 'it',
					'wp_locale_code' => 'it_IT',
					'is_rtl' => false,
				),
				'ja' => 
				array (
					'name' => 'Japanese',
					'native_name' => '日本語',
					'locale_code' => 'ja',
					'wp_locale_code' => 'ja',
					'is_rtl' => false,
				),
				'jv_ID' => 
				array (
					'name' => 'Javanese',
					'native_name' => 'Basa Jawa',
					'locale_code' => 'jv',
					'wp_locale_code' => 'jv_ID',
					'is_rtl' => false,
				),
				'kab' => 
				array (
					'name' => 'Kabyle',
					'native_name' => 'Taqbaylit',
					'locale_code' => 'kab',
					'wp_locale_code' => 'kab',
					'is_rtl' => false,
				),
				'kn' => 
				array (
					'name' => 'Kannada',
					'native_name' => 'ಕನ್ನಡ',
					'locale_code' => 'kn',
					'wp_locale_code' => 'kn',
					'is_rtl' => false,
				),
				'kk' => 
				array (
					'name' => 'Kazakh',
					'native_name' => 'Қазақ тілі',
					'locale_code' => 'kk',
					'wp_locale_code' => 'kk',
					'is_rtl' => false,
				),
				'km' => 
				array (
					'name' => 'Khmer',
					'native_name' => 'ភាសាខ្មែរ',
					'locale_code' => 'km',
					'wp_locale_code' => 'km',
					'is_rtl' => false,
				),
				'kin' => 
				array (
					'name' => 'Kinyarwanda',
					'native_name' => 'Ikinyarwanda',
					'locale_code' => 'kin',
					'wp_locale_code' => 'kin',
					'is_rtl' => false,
				),
				'ky_KY' => 
				array (
					'name' => 'Kirghiz',
					'native_name' => 'кыргыз тили',
					'locale_code' => 'ky',
					'wp_locale_code' => 'ky_KY',
					'is_rtl' => false,
				),
				'ko_KR' => 
				array (
					'name' => 'Korean',
					'native_name' => '한국어',
					'locale_code' => 'ko',
					'wp_locale_code' => 'ko_KR',
					'is_rtl' => false,
				),
				'ckb' => 
				array (
					'name' => 'Kurdish (Sorani)',
					'native_name' => 'كوردی‎',
					'locale_code' => 'ckb',
					'wp_locale_code' => 'ckb',
					'is_rtl' => false,
				),
				'lo' => 
				array (
					'name' => 'Lao',
					'native_name' => 'ພາສາລາວ',
					'locale_code' => 'lo',
					'wp_locale_code' => 'lo',
					'is_rtl' => false,
				),
				'lv' => 
				array (
					'name' => 'Latvian',
					'native_name' => 'Latviešu valoda',
					'locale_code' => 'lv',
					'wp_locale_code' => 'lv',
					'is_rtl' => false,
				),
				'li' => 
				array (
					'name' => 'Limburgish',
					'native_name' => 'Limburgs',
					'locale_code' => 'li',
					'wp_locale_code' => 'li',
					'is_rtl' => false,
				),
				'lin' => 
				array (
					'name' => 'Lingala',
					'native_name' => 'Ngala',
					'locale_code' => 'lin',
					'wp_locale_code' => 'lin',
					'is_rtl' => false,
				),
				'lt_LT' => 
				array (
					'name' => 'Lithuanian',
					'native_name' => 'Lietuvių kalba',
					'locale_code' => 'lt',
					'wp_locale_code' => 'lt_LT',
					'is_rtl' => false,
				),
				'lb_LU' => 
				array (
					'name' => 'Luxembourgish',
					'native_name' => 'Lëtzebuergesch',
					'locale_code' => 'lb',
					'wp_locale_code' => 'lb_LU',
					'is_rtl' => false,
				),
				'mk_MK' => 
				array (
					'name' => 'Macedonian',
					'native_name' => 'Македонски јазик',
					'locale_code' => 'mk',
					'wp_locale_code' => 'mk_MK',
					'is_rtl' => false,
				),
				'mg_MG' => 
				array (
					'name' => 'Malagasy',
					'native_name' => 'Malagasy',
					'locale_code' => 'mg',
					'wp_locale_code' => 'mg_MG',
					'is_rtl' => false,
				),
				'ms_MY' => 
				array (
					'name' => 'Malay',
					'native_name' => 'Bahasa Melayu',
					'locale_code' => 'ms',
					'wp_locale_code' => 'ms_MY',
					'is_rtl' => false,
				),
				'ml_IN' => 
				array (
					'name' => 'Malayalam',
					'native_name' => 'മലയാളം',
					'locale_code' => 'ml',
					'wp_locale_code' => 'ml_IN',
					'is_rtl' => false,
				),
				'mri' => 
				array (
					'name' => 'Maori',
					'native_name' => 'Te Reo Māori',
					'locale_code' => 'mri',
					'wp_locale_code' => 'mri',
					'is_rtl' => false,
				),
				'mr' => 
				array (
					'name' => 'Marathi',
					'native_name' => 'मराठी',
					'locale_code' => 'mr',
					'wp_locale_code' => 'mr',
					'is_rtl' => false,
				),
				'xmf' => 
				array (
					'name' => 'Mingrelian',
					'native_name' => 'მარგალური ნინა',
					'locale_code' => 'xmf',
					'wp_locale_code' => 'xmf',
					'is_rtl' => false,
				),
				'mn' => 
				array (
					'name' => 'Mongolian',
					'native_name' => 'Монгол',
					'locale_code' => 'mn',
					'wp_locale_code' => 'mn',
					'is_rtl' => false,
				),
				'me_ME' => 
				array (
					'name' => 'Montenegrin',
					'native_name' => 'Crnogorski jezik',
					'locale_code' => 'me',
					'wp_locale_code' => 'me_ME',
					'is_rtl' => false,
				),
				'ary' => 
				array (
					'name' => 'Moroccan Arabic',
					'native_name' => 'العربية المغربية',
					'locale_code' => 'ary',
					'wp_locale_code' => 'ary',
					'is_rtl' => false,
				),
				'my_MM' => 
				array (
					'name' => 'Myanmar (Burmese)',
					'native_name' => 'ဗမာစာ',
					'locale_code' => 'mya',
					'wp_locale_code' => 'my_MM',
					'is_rtl' => false,
				),
				'ne_NP' => 
				array (
					'name' => 'Nepali',
					'native_name' => 'नेपाली',
					'locale_code' => 'ne',
					'wp_locale_code' => 'ne_NP',
					'is_rtl' => false,
				),
				'nb_NO' => 
				array (
					'name' => 'Norwegian (Bokmål)',
					'native_name' => 'Norsk bokmål',
					'locale_code' => 'nb',
					'wp_locale_code' => 'nb_NO',
					'is_rtl' => false,
				),
				'nn_NO' => 
				array (
					'name' => 'Norwegian (Nynorsk)',
					'native_name' => 'Norsk nynorsk',
					'locale_code' => 'nn',
					'wp_locale_code' => 'nn_NO',
					'is_rtl' => false,
				),
				'oci' => 
				array (
					'name' => 'Occitan',
					'native_name' => 'Occitan',
					'locale_code' => 'oci',
					'wp_locale_code' => 'oci',
					'is_rtl' => false,
				),
				'ory' => 
				array (
					'name' => 'Oriya',
					'native_name' => 'ଓଡ଼ିଆ',
					'locale_code' => 'ory',
					'wp_locale_code' => 'ory',
					'is_rtl' => false,
				),
				'os' => 
				array (
					'name' => 'Ossetic',
					'native_name' => 'Ирон',
					'locale_code' => 'os',
					'wp_locale_code' => 'os',
					'is_rtl' => false,
				),
				'ps' => 
				array (
					'name' => 'Pashto',
					'native_name' => 'پښتو',
					'locale_code' => 'ps',
					'wp_locale_code' => 'ps',
					'is_rtl' => true,
				),
				'fa_IR' => 
				array (
					'name' => 'Persian',
					'native_name' => 'فارسی',
					'locale_code' => 'fa',
					'wp_locale_code' => 'fa_IR',
					'is_rtl' => true,
				),
				'fa_AF' => 
				array (
					'name' => 'Persian (Afghanistan)',
					'native_name' => '(فارسی (افغانستان',
					'locale_code' => 'fa-af',
					'wp_locale_code' => 'fa_AF',
					'is_rtl' => false,
				),
				'pl_PL' => 
				array (
					'name' => 'Polish',
					'native_name' => 'Polski',
					'locale_code' => 'pl',
					'wp_locale_code' => 'pl_PL',
					'is_rtl' => false,
				),
				'pt_BR' => 
				array (
					'name' => 'Portuguese (Brazil)',
					'native_name' => 'Português do Brasil',
					'locale_code' => 'pt-br',
					'wp_locale_code' => 'pt_BR',
					'is_rtl' => false,
				),
				'pt_PT' => 
				array (
					'name' => 'Portuguese (Portugal)',
					'native_name' => 'Português',
					'locale_code' => 'pt',
					'wp_locale_code' => 'pt_PT',
					'is_rtl' => false,
				),
				'pa_IN' => 
				array (
					'name' => 'Punjabi',
					'native_name' => 'ਪੰਜਾਬੀ',
					'locale_code' => 'pa',
					'wp_locale_code' => 'pa_IN',
					'is_rtl' => false,
				),
				'rhg' => 
				array (
					'name' => 'Rohingya',
					'native_name' => 'Ruáinga',
					'locale_code' => 'rhg',
					'wp_locale_code' => 'rhg',
					'is_rtl' => false,
				),
				'ro_RO' => 
				array (
					'name' => 'Romanian',
					'native_name' => 'Română',
					'locale_code' => 'ro',
					'wp_locale_code' => 'ro_RO',
					'is_rtl' => false,
				),
				'roh' => 
				array (
					'name' => 'Romansh Vallader',
					'native_name' => 'Rumantsch Vallader',
					'locale_code' => 'roh',
					'wp_locale_code' => 'roh',
					'is_rtl' => false,
				),
				'ru_RU' => 
				array (
					'name' => 'Russian',
					'native_name' => 'Русский',
					'locale_code' => 'ru',
					'wp_locale_code' => 'ru_RU',
					'is_rtl' => false,
				),
				'rue' => 
				array (
					'name' => 'Rusyn',
					'native_name' => 'Русиньскый',
					'locale_code' => 'rue',
					'wp_locale_code' => 'rue',
					'is_rtl' => false,
				),
				'sah' => 
				array (
					'name' => 'Sakha',
					'native_name' => 'Сахалыы',
					'locale_code' => 'sah',
					'wp_locale_code' => 'sah',
					'is_rtl' => false,
				),
				'sa_IN' => 
				array (
					'name' => 'Sanskrit',
					'native_name' => 'भारतम्',
					'locale_code' => 'sa-in',
					'wp_locale_code' => 'sa_IN',
					'is_rtl' => false,
				),
				'srd' => 
				array (
					'name' => 'Sardinian',
					'native_name' => 'Sardu',
					'locale_code' => 'srd',
					'wp_locale_code' => 'srd',
					'is_rtl' => false,
				),
				'gd' => 
				array (
					'name' => 'Scottish Gaelic',
					'native_name' => 'Gàidhlig',
					'locale_code' => 'gd',
					'wp_locale_code' => 'gd',
					'is_rtl' => false,
				),
				'sr_RS' => 
				array (
					'name' => 'Serbian',
					'native_name' => 'Српски језик',
					'locale_code' => 'sr',
					'wp_locale_code' => 'sr_RS',
					'is_rtl' => false,
				),
				'szl' => 
				array (
					'name' => 'Silesian',
					'native_name' => 'Ślōnskŏ gŏdka',
					'locale_code' => 'szl',
					'wp_locale_code' => 'szl',
					'is_rtl' => false,
				),
				'snd' => 
				array (
					'name' => 'Sindhi',
					'native_name' => 'سنڌي',
					'locale_code' => 'snd',
					'wp_locale_code' => 'snd',
					'is_rtl' => false,
				),
				'si_LK' => 
				array (
					'name' => 'Sinhala',
					'native_name' => 'සිංහල',
					'locale_code' => 'si',
					'wp_locale_code' => 'si_LK',
					'is_rtl' => false,
				),
				'sk_SK' => 
				array (
					'name' => 'Slovak',
					'native_name' => 'Slovenčina',
					'locale_code' => 'sk',
					'wp_locale_code' => 'sk_SK',
					'is_rtl' => false,
				),
				'sl_SI' => 
				array (
					'name' => 'Slovenian',
					'native_name' => 'Slovenščina',
					'locale_code' => 'sl',
					'wp_locale_code' => 'sl_SI',
					'is_rtl' => false,
				),
				'so_SO' => 
				array (
					'name' => 'Somali',
					'native_name' => 'Afsoomaali',
					'locale_code' => 'so',
					'wp_locale_code' => 'so_SO',
					'is_rtl' => false,
				),
				'azb' => 
				array (
					'name' => 'South Azerbaijani',
					'native_name' => 'گؤنئی آذربایجان',
					'locale_code' => 'azb',
					'wp_locale_code' => 'azb',
					'is_rtl' => false,
				),
				'es_AR' => 
				array (
					'name' => 'Spanish (Argentina)',
					'native_name' => 'Español de Argentina',
					'locale_code' => 'es-ar',
					'wp_locale_code' => 'es_AR',
					'is_rtl' => false,
				),
				'es_CL' => 
				array (
					'name' => 'Spanish (Chile)',
					'native_name' => 'Español de Chile',
					'locale_code' => 'es-cl',
					'wp_locale_code' => 'es_CL',
					'is_rtl' => false,
				),
				'es_CO' => 
				array (
					'name' => 'Spanish (Colombia)',
					'native_name' => 'Español de Colombia',
					'locale_code' => 'es-co',
					'wp_locale_code' => 'es_CO',
					'is_rtl' => false,
				),
				'es_GT' => 
				array (
					'name' => 'Spanish (Guatemala)',
					'native_name' => 'Español de Guatemala',
					'locale_code' => 'es-gt',
					'wp_locale_code' => 'es_GT',
					'is_rtl' => false,
				),
				'es_MX' => 
				array (
					'name' => 'Spanish (Mexico)',
					'native_name' => 'Español de México',
					'locale_code' => 'es-mx',
					'wp_locale_code' => 'es_MX',
					'is_rtl' => false,
				),
				'es_PE' => 
				array (
					'name' => 'Spanish (Peru)',
					'native_name' => 'Español de Perú',
					'locale_code' => 'es-pe',
					'wp_locale_code' => 'es_PE',
					'is_rtl' => false,
				),
				'es_PR' => 
				array (
					'name' => 'Spanish (Puerto Rico)',
					'native_name' => 'Español de Puerto Rico',
					'locale_code' => 'es-pr',
					'wp_locale_code' => 'es_PR',
					'is_rtl' => false,
				),
				'es_ES' => 
				array (
					'name' => 'Spanish (Spain)',
					'native_name' => 'Español',
					'locale_code' => 'es',
					'wp_locale_code' => 'es_ES',
					'is_rtl' => false,
				),
				'es_VE' => 
				array (
					'name' => 'Spanish (Venezuela)',
					'native_name' => 'Español de Venezuela',
					'locale_code' => 'es-ve',
					'wp_locale_code' => 'es_VE',
					'is_rtl' => false,
				),
				'su_ID' => 
				array (
					'name' => 'Sundanese',
					'native_name' => 'Basa Sunda',
					'locale_code' => 'su',
					'wp_locale_code' => 'su_ID',
					'is_rtl' => false,
				),
				'sw' => 
				array (
					'name' => 'Swahili',
					'native_name' => 'Kiswahili',
					'locale_code' => 'sw',
					'wp_locale_code' => 'sw',
					'is_rtl' => false,
				),
				'sv_SE' => 
				array (
					'name' => 'Swedish',
					'native_name' => 'Svenska',
					'locale_code' => 'sv',
					'wp_locale_code' => 'sv_SE',
					'is_rtl' => false,
				),
				'gsw' => 
				array (
					'name' => 'Swiss German',
					'native_name' => 'Schwyzerdütsch',
					'locale_code' => 'gsw',
					'wp_locale_code' => 'gsw',
					'is_rtl' => false,
				),
				'tl' => 
				array (
					'name' => 'Tagalog',
					'native_name' => 'Tagalog',
					'locale_code' => 'tl',
					'wp_locale_code' => 'tl',
					'is_rtl' => false,
				),
				'tah' => 
				array (
					'name' => 'Tahitian',
					'native_name' => 'Reo Tahiti',
					'locale_code' => 'tah',
					'wp_locale_code' => 'tah',
					'is_rtl' => false,
				),
				'tg' => 
				array (
					'name' => 'Tajik',
					'native_name' => 'Тоҷикӣ',
					'locale_code' => 'tg',
					'wp_locale_code' => 'tg',
					'is_rtl' => false,
				),
				'tzm' => 
				array (
					'name' => 'Tamazight (Central Atlas)',
					'native_name' => 'ⵜⴰⵎⴰⵣⵉⵖⵜ',
					'locale_code' => 'tzm',
					'wp_locale_code' => 'tzm',
					'is_rtl' => false,
				),
				'ta_IN' => 
				array (
					'name' => 'Tamil',
					'native_name' => 'தமிழ்',
					'locale_code' => 'ta',
					'wp_locale_code' => 'ta_IN',
					'is_rtl' => false,
				),
				'ta_LK' => 
				array (
					'name' => 'Tamil (Sri Lanka)',
					'native_name' => 'தமிழ்',
					'locale_code' => 'ta-lk',
					'wp_locale_code' => 'ta_LK',
					'is_rtl' => false,
				),
				'tt_RU' => 
				array (
					'name' => 'Tatar',
					'native_name' => 'Татар теле',
					'locale_code' => 'tt',
					'wp_locale_code' => 'tt_RU',
					'is_rtl' => false,
				),
				'te' => 
				array (
					'name' => 'Telugu',
					'native_name' => 'తెలుగు',
					'locale_code' => 'te',
					'wp_locale_code' => 'te',
					'is_rtl' => false,
				),
				'th' => 
				array (
					'name' => 'Thai',
					'native_name' => 'ไทย',
					'locale_code' => 'th',
					'wp_locale_code' => 'th',
					'is_rtl' => false,
				),
				'bo' => 
				array (
					'name' => 'Tibetan',
					'native_name' => 'བོད་སྐད',
					'locale_code' => 'bo',
					'wp_locale_code' => 'bo',
					'is_rtl' => false,
				),
				'tir' => 
				array (
					'name' => 'Tigrinya',
					'native_name' => 'ትግርኛ',
					'locale_code' => 'tir',
					'wp_locale_code' => 'tir',
					'is_rtl' => false,
				),
				'tr_TR' => 
				array (
					'name' => 'Turkish',
					'native_name' => 'Türkçe',
					'locale_code' => 'tr',
					'wp_locale_code' => 'tr_TR',
					'is_rtl' => false,
				),
				'tuk' => 
				array (
					'name' => 'Turkmen',
					'native_name' => 'Türkmençe',
					'locale_code' => 'tuk',
					'wp_locale_code' => 'tuk',
					'is_rtl' => false,
				),
				'twd' => 
				array (
					'name' => 'Tweants',
					'native_name' => 'Twents',
					'locale_code' => 'twd',
					'wp_locale_code' => 'twd',
					'is_rtl' => false,
				),
				'ug_CN' => 
				array (
					'name' => 'Uighur',
					'native_name' => 'Uyƣurqə',
					'locale_code' => 'ug',
					'wp_locale_code' => 'ug_CN',
					'is_rtl' => false,
				),
				'uk' => 
				array (
					'name' => 'Ukrainian',
					'native_name' => 'Українська',
					'locale_code' => 'uk',
					'wp_locale_code' => 'uk',
					'is_rtl' => false,
				),
				'ur' => 
				array (
					'name' => 'Urdu',
					'native_name' => 'اردو',
					'locale_code' => 'ur',
					'wp_locale_code' => 'ur',
					'is_rtl' => true,
				),
				'uz_UZ' => 
				array (
					'name' => 'Uzbek',
					'native_name' => 'O‘zbekcha',
					'locale_code' => 'uz',
					'wp_locale_code' => 'uz_UZ',
					'is_rtl' => false,
				),
				'vi' => 
				array (
					'name' => 'Vietnamese',
					'native_name' => 'Tiếng Việt',
					'locale_code' => 'vi',
					'wp_locale_code' => 'vi',
					'is_rtl' => false,
				),
				'wa' => 
				array (
					'name' => 'Walloon',
					'native_name' => 'Walon',
					'locale_code' => 'wa',
					'wp_locale_code' => 'wa',
					'is_rtl' => false,
				),
				'cy' => 
				array (
					'name' => 'Welsh',
					'native_name' => 'Cymraeg',
					'locale_code' => 'cy',
					'wp_locale_code' => 'cy',
					'is_rtl' => false,
				),
				'yor' => 
				array (
					'name' => 'Yoruba',
					'native_name' => 'Yorùbá',
					'locale_code' => 'yor',
					'wp_locale_code' => 'yor',
					'is_rtl' => false,
				),
		);
		return $wt_pklist_language_list;
	}
}