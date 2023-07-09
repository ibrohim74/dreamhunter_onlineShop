<?php
/**
 * Review request
 *  
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}
class Wf_Woocommerce_Packing_List_Review_Request
{
	/**
	* config options 
	*/
	private $plugin_title="WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels";
	private $review_url="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce/reviews/#new-post";
	private $plugin_prefix="wt_pklist"; /* must be unique name */
	private $activation_hook="wt_pklist_activate"; /* hook for activation, to store activated date */
	private $deactivation_hook="wt_pklist_deactivate"; /* hook for deactivation, to delete activated date */
	private $days_to_show_banner=10; /* when did the banner to show */
	private $remind_days=15; /* remind interval in days */
	private $webtoffee_logo_url=WF_PKLIST_PLUGIN_URL.'assets/images/webtoffee-logo_small.png';

	
	private $start_date=0; /* banner to show count start date. plugin installed date, remind me later added date */
	private $current_banner_state=2; /* 1: active, 2: waiting to show(first after installation), 3: closed by user/not interested to review, 4: user done the review, 5:remind me later */
	private $banner_state_option_name=''; /* WP option name to save banner state */
	private $start_date_option_name=''; /* WP option name to save start date */
	private $banner_css_class=''; /* CSS class name for Banner HTML element. */
	private $banner_message=''; /* Banner message. */
	private $later_btn_text=''; /* Remind me later button text */
	private $never_btn_text=''; /* Never review button text. */
	private $review_btn_text=''; /* Review now button text. */
	private $ajax_action_name=''; /* Name of ajax action to save banner state. */
	private $allowed_action_type_arr=array(
		'later', /* remind me later */
		'never', /* never */
		'review', /* review now */
		'closed', /* not interested */
	);
	private $doc_created_count = 0;

	public function __construct()
	{
		//Set config vars
		$this->set_vars();

		add_action($this->activation_hook, array($this, 'on_activate'));
		add_action($this->deactivation_hook, array($this, 'on_deactivate'));

		if($this->check_condition()) /* checks the banner is active now */
		{
			$this->banner_message=sprintf(__("Hey, we at %sWebToffee%s would like to thank you for using %s %s %s. %s Less than a minute of your time will motivate us to keep doing what we do. We would really appreciate if you could take a moment to drop a quick review that motivate us to keep going.", 'print-invoices-packing-slip-labels-for-woocommerce'), '<b>', '</b>','<b>', $this->plugin_title, '</b>', '<br />');

			/* button texts */
			$this->later_btn_text=__("Remind me later", 'print-invoices-packing-slip-labels-for-woocommerce');
			$this->never_btn_text=__("Not interested", 'print-invoices-packing-slip-labels-for-woocommerce');
			$this->review_btn_text=__("Review now", 'print-invoices-packing-slip-labels-for-woocommerce');

			if(1000 <= $this->doc_created_count){
				$this->webtoffee_logo_url = "";
				$round_count = floor($this->doc_created_count/100) * 100;
				$this->banner_message=sprintf('<b>%1$s</b> %2$s <b style="color:#FF6636;font-size: 14px;">%3$s +</b> %4$s <b style="color:#FF6636;font-size: 14px;">%5$s</b>. %6$s',
					__("Knock, Knock!",'print-invoices-packing-slip-labels-for-woocommerce'),
					__("You`ve just created","print-invoices-packing-slip-labels-for-woocommerce"),
					$round_count,
					__("commercial documents using","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Woocommerce PDF invoices, packing slips, delivery notes, and shipping labels","print-invoices-packing-slip-labels-for-woocommerce"),
					__("That’s awesome! We’d love it if you take a moment to share what you think and help spread the word.","print-invoices-packing-slip-labels-for-woocommerce")
					);
				$this->review_btn_text=__("Rate us now", 'print-invoices-packing-slip-labels-for-woocommerce');
			}

			add_action('admin_notices', array($this, 'show_banner')); /* show banner */
			add_action('admin_print_footer_scripts', array($this, 'add_banner_scripts')); /* add banner scripts */
			add_action('wp_ajax_'.$this->ajax_action_name, array($this, 'process_user_action')); /* process banner user action */
		}
	}

	/**
	*	Set config vars
	*/
	public function set_vars()
	{
		$this->ajax_action_name=$this->plugin_prefix.'_process_user_review_action';
		$this->banner_state_option_name=$this->plugin_prefix."_review_request"; 
		$this->start_date_option_name=$this->plugin_prefix."_start_date";
		$this->banner_css_class=$this->plugin_prefix."_review_request";

		$this->start_date=absint(get_option($this->start_date_option_name));
		$banner_state=absint(get_option($this->banner_state_option_name));
		$this->current_banner_state=($banner_state==0 ? $this->current_banner_state : $banner_state);	
		$this->doc_created_count = (int)get_option('wt_created_document_count',true);	
	}

	/**
	*	Actions on plugin activation
	*	Saves activation date
	*/
	public function on_activate()
	{
		if($this->start_date==0)
		{
			$this->reset_start_date();
		}
	}

	/**
	*	Actions on plugin deactivation
	*	Removes activation date
	*/
	public function on_deactivate()
	{
		delete_option($this->start_date_option_name);
	}

	/**
	*	Reset the start date. 
	*/
	private function reset_start_date()
	{
		update_option($this->start_date_option_name, time());
	}

	/**
	*	Update the banner state 
	*/
	private function update_banner_state($val)
	{
		update_option($this->banner_state_option_name, $val);
	}
	
	/**
	*	Prints the banner 
	*/
	public function show_banner()
	{
		$this->update_banner_state(1); /* update banner active state */
		if(1000 <= $this->doc_created_count){
			?>
			<div class="<?php echo sanitize_text_field($this->banner_css_class); ?> value_based_notice notice is-dismissible">
				<p style="width: 85%;">
		            <?php echo wp_kses_post($this->banner_message); ?>
		        </p>
		        <div style="margin-top: 1em;">
		        	<p>
		                <a class="button" data-type="review" style="background: #FFE500;border-color: #ccc;;color: #000;"><?php echo sanitize_text_field($this->review_btn_text); ?></a>
		                <a class="button button-secondary" style="color:#333; border-color:#ccc; background:#efefef;" data-type="later"><?php echo sanitize_text_field($this->later_btn_text); ?></a>
		            </p>
		        </div>
				<div style="position: absolute;top: 1.8em;<?php if(is_rtl()){echo "left: 0;";}else{echo "right: 0;"; } ?>">
		        	<img src="<?php echo WF_PKLIST_PLUGIN_URL.'admin/images/value_based_review_img.png'?>" style="height: 85px;<?php if(is_rtl()){echo "transform: scaleX(-1);";} ?>">
		        </div>
			</div>
			<?php
		}else{
			?>
			<div class="<?php echo esc_attr($this->banner_css_class); ?> notice-info notice is-dismissible">
	            <?php
	            if ("" !== $this->webtoffee_logo_url) {
	            ?>
	                <h3 style="margin: 10px 0;"><?php echo wp_kses_post($this->plugin_title); ?></h3>
	            <?php
	            }
	            ?>
	            <p>
	                <?php echo wp_kses_post($this->banner_message); ?>
	            </p>
	            <p>
	                <a class="button button-secondary" style="color:#333; border-color:#ccc; background:#efefef;" data-type="later"><?php echo wp_kses_post($this->later_btn_text); ?></a>
	                <a class="button button-primary" data-type="review"><?php echo wp_kses_post($this->review_btn_text); ?></a>
	            </p>
	            <div class="wt-cli-review-footer" style="position: relative;">
	                <span class="wt-cli-footer-icon" style="position: absolute;bottom: 10px;<?php echo is_rtl() ? 'left: 0;' :'right:0;'; ?>"><img src="<?php echo esc_url($this->webtoffee_logo_url); ?>" style="max-width:100px;"></span>
	            </div>
	        </div>
			<?php
		}
	}

	/**
	*	Ajax hook to process user action on the banner
	*/
	public function process_user_action()
	{
		check_ajax_referer($this->plugin_prefix);
		if(isset($_POST['wt_review_action_type']))
		{
			$action_type=sanitize_text_field($_POST['wt_review_action_type']);
			
			/* current action is in allowed action list */
			if(in_array($action_type, $this->allowed_action_type_arr))
			{
				if("never" === $action_type || "closed" === $action_type)
				{
					$new_banner_state=3;
					$this->reset_start_date();
					if(1000 <= $this->doc_created_count){
						$new_banner_state=33; 
					}
				}
				elseif("review" === $action_type)
				{
					$new_banner_state=4;
				}else
				{
					/* reset start date to current date */
					$this->reset_start_date();
					$new_banner_state=5; /* remind me later */
				}
				$this->update_banner_state($new_banner_state);
			}
		}
		exit();
	}

	/**
	*	Add banner JS to admin footer
	*/
	public function add_banner_scripts()
	{
		$ajax_url=admin_url('admin-ajax.php');
		$nonce=wp_create_nonce($this->plugin_prefix);
		?>
		<script type="text/javascript">
		    (function($){
		        "use strict";

		        /* prepare data object */
	            var data_obj={
	            	_wpnonce: '<?php echo $nonce;?>',
            		action: '<?php echo $this->ajax_action_name;?>',
            		wt_review_action_type: ''
	            };

		        $(document).on('click', '.<?php echo $this->banner_css_class;?> a.button', function(e)
		        {
		            e.preventDefault();
		            var elm=$(this);
		            var btn_type=elm.attr('data-type');
		            if(btn_type=='review')
		            {
		            	window.open('<?php echo $this->review_url;?>');
		            }
		            elm.parents('.<?php echo $this->banner_css_class;?>').hide();

		            data_obj['wt_review_action_type']=btn_type;
		            $.ajax({
		            	url:'<?php echo $ajax_url;?>',
		            	data:data_obj,
		            	type: 'POST'
		            });

		        }).on('click', '.<?php echo $this->banner_css_class;?> .notice-dismiss', function(e)
		        {
	                e.preventDefault();
		            data_obj['wt_review_action_type']='closed';
		            $.ajax({
		            	url:'<?php echo $ajax_url;?>',
		            	data:data_obj,
		            	type: 'POST',
		            });

		        });

		    })(jQuery)
		</script>
		<?php
	}

	/**
	*	Checks the condition to show the banner
	*/
	private function check_condition()
	{
		if(1 === $this->current_banner_state || "1" === $this->current_banner_state) /* currently showing then return true */
		{
			return true;
		}
		
		if(2 === $this->current_banner_state || "2" === $this->current_banner_state || 5 === $this->current_banner_state || "5" === $this->current_banner_state) /* only waiting/remind later state */
		{
			if(0 === $this->start_date || "0" === $this->start_date) /* unable to get activated date */
			{
				/* set current date as activation date*/
				$this->reset_start_date();
				return false;
			}

			$days=(2 === $this->current_banner_state || "2" === $this->current_banner_state ? $this->days_to_show_banner : $this->remind_days);
			$date_to_check=$this->start_date+(86400*$days);
			// $date_to_check = $this->start_date+(60); // for testing
			if($date_to_check<=time()) /* time reached to show the banner */
			{
				return true;
			}else
			{
				return false;
			}
		}

		if((3 === $this->current_banner_state || "3" === $this->current_banner_state) && 1000 <= $this->doc_created_count){
			$date_to_check=$this->start_date+(86400*15);
			// $date_to_check = $this->start_date+(60); // for testing
			if($date_to_check<=time()) /* time reached to show the banner */
			{
				return true;
			}
		}

		return false;
	}
}
new Wf_Woocommerce_Packing_List_Review_Request();