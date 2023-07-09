<?php
class QrctWp_from_Goaskle_Com
{
    public $pluginName = 'QR Code Tag for WC order emails, POS receipt emails, PDF invoices, PDF packing slips, Blog posts, Custom post types and Pages (from goaskle.com)';
    public $pluginVersion = '1.9.15';
  
    private $pluginDomain = 'qr-code-tag-for-wc-from-goaskle-com';          // translation domain
    private $pluginOptions = 'qrct_from_goaskle_com_options';   // tag for WordPress options database
    private $shortcodeTag = 'qrcodetag_from_goaskle_com';       // WordPress shortcode representation
    private $qrcode;                           // QR Code object that handles the code generation
    private $qrcodeExt = '';                   // image file extension
    private $pluginUrl = '';                   // plugin URL
    private $pluginBase = '';                  // plugin base path
     
     
     
    private $defaultOptions = array(
        'widget' => array(
            'size' => '150',
            'enc' => 'UTF-8',
            'ecc' => 'L',
            'version' => '0',
            'margin' => '4',
            'imageparam' => ' qrctwidget ',
            'link' => 'https://Goaskle.com',
            'atagparam' => '',
            'tooltip' => '',
            'text' => '',
            'title' => 'QR Code',
            'content' => '',
            'before' => '',
            'after' => '',
            'vat_number' => 'Enter you VAT'),
            'wc_data_template' => '',
            'disable_auto_wc' => '0',
            'disable_pages_auto_wc' => '0',
            'hash_order_wc' => '0',
            'main_color' => '',
            'disable_all_wc_emails_except_standard' => '0',
        'shortcode' => array(
            'size' => '150',
            'enc' => 'UTF-8',
            'ecc' => 'L',
            'version' => '0',
            'margin' => '4',
            'imageparam' => ' qrctimage ',
            'link' => 'false',
            'atagparam' => '',
            'tooltip' => '',
            'vat_number' => 'Enter you VAT'),
            'wc_data_template' => '',
            'disable_auto_wc' => '0',
            'disable_pages_auto_wc' => '0',
            'hash_order_wc' => '0',
            'main_color' => '',            
            'disable_all_wc_emails_except_standard' => '0',
        'tooltip' => array(
            'size' => '150',
            'enc' => 'UTF-8',
            'ecc' => 'L',
            'version' => '0',
            'margin' => '4'),
            'main_color' => '',            
        'global' => array(
            'generator' => 'lib',
            'vat_number' => 'Enter you VAT',
            'wc_data_template' => '',
            'disable_auto_wc' => '0',
            'disable_pages_auto_wc' => '0',
            'hash_order_wc' => '0',
            'main_color' => '',            
            'disable_all_wc_emails_except_standard' => '0',
            'ext' => 'png'));

    /**
    * WordPress integration with Hooks, Translation and Widgets
    *   
    * @param  string  $baseFile  The full path and filename to the Creator script
    */
    
    

    public function __construct($baseFile)
    {
        // set initial paths - because of resolved symlinks within $baseFile 
        // we'll construct the URL and base paths using the script name and 
        // the 'guessed' plugin directory name - thus do not name the 
        // plugin directory other than the main script file! 
        // plugin_basename(__FILE__) does not work with symlinks. 
        // blame php. not wp.
       
        define("GOASKLEPLUGINBASE", basename($baseFile,'.'.pathinfo($baseFile, PATHINFO_EXTENSION))."/".basename($baseFile));
        
        //$this->pluginBase = basename($baseFile,'.'.pathinfo($baseFile, PATHINFO_EXTENSION)).
                           // "/".basename($baseFile);
        
        define("GOASKLEPLUGINURL", WP_PLUGIN_URL.'/'.dirname(GOASKLEPLUGINBASE).'/');

        //$this->pluginUrl = WP_PLUGIN_URL.'/'.dirname($this->pluginBase).'/';

        // load text translation

//print_r(dirname(GOASKLEPLUGINBASE));
//add_action( 'plugins_loaded', 'load_my_textdomain' );

//add_action( 'plugins_loaded', 'myplugin_init' );
//function myplugin_init() {
	 //load_plugin_textdomain( 'qr-code-tag-for-wc-from-goaskle-com', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
//}
add_action( 'init', 'goaskle_load_textdomain_qrctwp' );
function goaskle_load_textdomain_qrctwp() {
    load_plugin_textdomain( 'qr-code-tag-for-wc-from-goaskle-com', false, './'.dirname(GOASKLEPLUGINBASE).'/lang' ); 
}

        // activation and deactivation function
        register_activation_hook($baseFile, array($this, 'activate'));
        register_deactivation_hook($baseFile, array($this, 'deactivate'));

        // widget integration
        add_action('widgets_init', array($this, 'initWidget'));
    
        // shortcode integration
        add_shortcode($this->shortcodeTag, array($this, 'shortcode'));

        // setup an options page
        if (is_admin()) { 
            add_action('admin_menu', array($this, 'setAdminMenu'),777); 

			add_action( 'admin_enqueue_scripts', 'add_admin_goaskle_scripts' );
				function add_admin_goaskle_scripts( $hook ){

				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );

				}            
        }

//goaskle.com 01 11 2021 add to wc

/*add_filter( 'woocommerce_email_footer', 'goaskle_com_filter_woocommerce_email_footer' );
function goaskle_com_filter_woocommerce_email_footer( $email ){
echo "<p style='text-align: center;'>".do_shortcode('[qrcodetag_from_goaskle_com/]')."</p>";
	return $email;
}*/



//180223 order hash added to every order wc

//trace url
/*$args = array(
        'slug' => 'hash-gaqr',
        'post_title' => 'Fake Page Title',
        'post_content' => 'This is the fake page content'
);
$buba = new GoAskle_dynamic_page($args);*/

add_filter( 'query_vars', function( $vars ){
	$vars[] = 'hash-gaqr';
	return $vars;
} );
function goaskle_qr_code_wc_template_redirect_action(){
//validation of hash from url

$is_admin = current_user_can('administrator');
$hash_is_valid = 0;

//are we on thank you page?
/*if ( class_exists( 'woocommerce' ) ) {
	

    if ( is_wc_endpoint_url( 'order-received' ) && isset( $_GET['key'] ) ) {
    	
    }
}*/
//?

global $wp;
	//echo '<pre>';print_r($_GET['hash-gaqr'].'-00'); echo '</pre>';
	
if ( isset($wp->query_vars['hash-gaqr']) || isset($_GET['hash-gaqr']) ){
                $hash_for_validate = '';
//	echo '<pre>';print_r($hash_for_validate.'-0'); echo '</pre>';
        if (isset($_GET['hash-gaqr']) && $_GET['hash-gaqr'] <> '') {
        $hash_for_validate = '';
                $hash_for_validate = (string)sanitize_text_field($_GET['hash-gaqr']);
//echo '<pre>';print_r($hash_for_validate.'-1'); echo '</pre>';
        } else {
  //      echo '<pre>';print_r($hash_for_validate.'-2'); echo '</pre>';
        
        		if (isset($wp->query_vars['hash-gaqr']) && $wp->query_vars['hash-gaqr'] <>''){
        		$hash_for_validate = '';
			        $hash_for_validate = (string)$wp->query_vars['hash-gaqr'];        
//echo '<pre>';print_r($hash_for_validate.'-3'); echo '</pre>';
        		} else {
$hash_for_validate = '';
//echo '<pre>';print_r($hash_for_validate.'-4'); echo '</pre>';
        		return;
/*$args = array(
        'slug' => 'hash-gaqr',
        'post_title' => 'Fake Page Title',
        'post_content' => 'This is the fake page content222'
);

new GoAskle_dynamic_page($args);
return wp_redirect('/hash-gaqr');
			        $hash_for_validate = '';    */       			
        		}

	    	}

	    	
		//validate
	$args = array('post_type' => 'shop_order','meta_key' => '_goaskle_com_qr_code_wc_order_hash','meta_value' => $hash_for_validate,'return' => 'ids');

if ( class_exists( 'woocommerce' ) ) {
	$all_orders_wc_with_hash = wc_get_orders( $args );
	} else {
		$all_orders_wc_with_hash = array();
	}
	
//	echo "<pre>"; print_r($all_orders_wc_with_hash); echo "</pre>";


	$count_valid = (int)count($all_orders_wc_with_hash);
	$hash_is_valid = ($count_valid == 1) ? 1 : 0;

		if ($hash_is_valid) {
	$oid = $all_orders_wc_with_hash[0];
if (metadata_exists('post', $oid, '_goaskle_com_qr_code_hash_data')){

	$hod = maybe_unserialize(get_post_meta( $oid, '_goaskle_com_qr_code_hash_data', true ));
	$hash_is_active = $hod['data']['hash_order_status'];
} else {
	$hash_is_active = 0;
}
	
	
	$order = wc_get_order( $oid );
   $urlr = $order->get_checkout_order_received_url();


//redirect to thank you page
if (!is_wc_endpoint_url( 'order-received' )){
wp_safe_redirect(  $urlr ."&valid-gaqr=1"."&hash-gaqr=".$hash_for_validate  );
exit;
}
	if (((int)$hash_is_active == 1 && !$is_admin) || $is_admin ){
		
	add_action( 'woocommerce_before_thankyou', 'goaskle_com_qr_code_custom_thank_you_valid' );
	add_filter( 'woocommerce_endpoint_order-received_title', 'goaskle_com_qr_code_thank_you_title_valid' );
	}
		}

	if ((int)$hash_is_valid == 0 || ((int)$hash_is_active == 0 && !$is_admin) ){
	add_action( 'woocommerce_before_thankyou', 'goaskle_com_qr_code_custom_thank_you_notvalid' );//
	add_filter( 'woocommerce_endpoint_order-received_title', 'goaskle_com_qr_code_thank_you_title_notvalid' );	
	}
    
//validation
}
//thank you page valid
}
//=== adding hash to admin order layout

function goaskle_qr_code_woocommerce_admin_order_data_after_order_details( $order ){

$is_admin = current_user_can('administrator');
if (!$is_admin) {return;}

$oid = $order->get_id();
$hod = maybe_unserialize(get_post_meta( $oid, '_goaskle_com_qr_code_hash_data', true ));
if (!empty($hod)){
$sta = $hod['data']['hash_order_status'];	
} else {
$sta = 0;	
}

$act = ((int)$sta == 1) ? __('Active','qr-code-tag-for-wc-from-goaskle-com') : __( 'Deactivated', 'qr-code-tag-for-wc-from-goaskle-com' );
$used = 0;
$tab = '';

if (isset($hod['data']['hash_using']) && is_array($hod['data']['hash_using'])) {
	$activations_count = count($hod['data']['hash_using']);
		if ($activations_count >= 1){
		$tab = '<table><thead><th>'.__('#','qr-code-tag-for-wc-from-goaskle-com').'</th><th>'.__('When','qr-code-tag-for-wc-from-goaskle-com').'</th></thead><tbody>';
		$used = $activations_count;
		
			foreach ($hod['data']['hash_using'] as $num => $use) {
				$when = $use['when_activated'];
				$tab .= '<tr><td>'.$num.'</td><td>'.date('l jS \of F Y h:i:s A',$when).'</td></tr>';
			}
			$tab .= '</tbody></table>';

} else {
		$used = 0;
}

} else {
	$used = 0;
}

?>
    <br class="clear" />
    <?php 
        $hash_code = get_post_meta( $oid, '_goaskle_com_qr_code_wc_order_hash', true );
    ?>
    <div class="edit_custom_field"> <!-- use same css class in h4 tag -->
    <?php
        woocommerce_wp_textarea_input( array(
            'id' => '_goaskle_com_qr_code_wc_order_hash',
            'label' => __( 'HASH:', 'qr-code-tag-for-wc-from-goaskle-com' ),
            'value' => $hash_code,
            'wrapper_class' => 'form-field-wide'
        ) );
    ?>
    </div>
<div><?php echo __('Status: ','qr-code-tag-for-wc-from-goaskle-com').' '.$act; ?><br><span><a target="_blank" href="<?php echo site_url().'?hash-gaqr='.$hash_code; ?>"><?php _e( 'Change', 'qr-code-tag-for-wc-from-goaskle-com' ); ?></a></span></div>
<div><?php echo __('Was activated (times): ','qr-code-tag-for-wc-from-goaskle-com'). $used; ?></div>
<div><?php echo $tab; ?></div>    
<?php
}

// * Save the hash fields values in admin
function goaskle_qr_woocommerce_process_shop_order_meta( $order_id ){
//    //check unique of hash and generate new if exist same goaskle 24 02 2023 veles

$current_order_id = $order_id;
$is_new_hash_unique = 0;

$hash_code = get_post_meta( $order_id, '_goaskle_com_qr_code_wc_order_hash', true );
$new_hash_code = wc_sanitize_textarea( $_POST[ '_goaskle_com_qr_code_wc_order_hash' ] );

//write new hash to db
$new_gen_hash_for_order = update_post_meta( $current_order_id, '_goaskle_com_qr_code_wc_order_hash', $new_hash_code );


if (metadata_exists('post', $order_id, '_goaskle_com_qr_code_hash_data')){
$hod = maybe_unserialize(get_post_meta( $order_id, '_goaskle_com_qr_code_hash_data', true ));

} else {

$hod = array();

if ($hash_code){

	$hod['data']['code']['first'] = $hash_code;	
} else {
	$hod['data']['code']['first'] = $new_hash_code;
}
$hod['data']['code']['current'] = $new_hash_code;
$hod['data']['when_added_to_order'] = time();
$hod['data']['hash_order_status'] = 0;
$hod['data']['hash_times_for_use'] = -1;
$hod['data']['hash_last_date'] = -1;

}

	//checking unique or not
while ($is_new_hash_unique == 0) {

	$args = array('exclude' => array( $current_order_id ),'post_type' => 'shop_order','meta_key' => '_goaskle_com_qr_code_wc_order_hash','meta_value' => $new_hash_code,'return' => 'ids');
	$all_orders_wc_with_hash = wc_get_orders( $args );

	//total same hash
	$counto = (int)count($all_orders_wc_with_hash);
              
		if ($counto > 0){

		$is_new_hash_unique = 0;
		
			$new_hash_code = sha1(md5(uniqid()));
			$new_gen_hash_for_order = update_post_meta( $current_order_id, '_goaskle_com_qr_code_wc_order_hash', $new_hash_code );
			$hod['data']['code']['current'] = $new_hash_code;
		} else {
			$is_new_hash_unique = 1;
		}
} 
//
update_post_meta( $order_id, '_goaskle_com_qr_code_hash_data', maybe_serialize($hod));
//check    
}
//===

if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'])){
$hash_order_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'];	
} else {
	$hash_order_wc = 0;
}

$hash_is_valid = 0;


//check if hash function activated
if ($hash_order_wc == 1){
add_action( 'woocommerce_checkout_order_created', 'goaskle_qr_code_woocommerce_checkout_order_created_action' );
add_action( 'template_redirect', 'goaskle_qr_code_wc_template_redirect_action' );
add_action( 'woocommerce_admin_order_data_after_order_details', 'goaskle_qr_code_woocommerce_admin_order_data_after_order_details' );
add_action( 'woocommerce_process_shop_order_meta', 'goaskle_qr_woocommerce_process_shop_order_meta' );
/////

}

function goaskle_com_qr_code_thank_you_title_notvalid( $old_title ){
$old_title = '<div class="validated_thanks_qr_code_goaskle">'.__( 'SORRY!', 'qr-code-tag-for-wc-from-goaskle-com' ).'</div>';
 	return $old_title;
}

//IF HASH IS NOT VALID SHOW THIS THANK YOU PAGE
function goaskle_com_qr_code_custom_thank_you_notvalid( $order_id ) {

if (metadata_exists('post', $order_id, '_goaskle_com_qr_code_wc_order_hash')){
$hash_code = __( 'Hash:', 'qr-code-tag-for-wc-from-goaskle-com' )." ".get_post_meta( $order_id, '_goaskle_com_qr_code_wc_order_hash', true );
} else {
	$hash_code = '';
}
/*$hash_is_valid = 0;

    $order = wc_get_order( $order_id );

    if ( ! $order  || $order->has_status( 'failed' ) ){
    	
    }*/

    ?>
<style>
.validated_thanks_qr_code_goaskle_desc:before{
    content: "\2716";
    border: 1px solid;
    float: left;
    margin-right: 30px;
    margin-bottom: 50px;
    border-radius: 100%;
    padding-left: 25px;
    padding-right: 25px;
    font-size: 44px;
    font-weight: bold;
    color: darkred;
    background-color: palevioletred;
    outline: 6px solid red;
    }
.validated_thanks_qr_code_goaskle{
    margin: 0 auto;
    text-align: center;
    font-size: 30px;
    font-weight: 600;
    color: red;
}
</style>    
        <div class="goaskle_com_qr_code_valid_wrapper">
        <div class="validated_thanks_qr_code_goaskle_desc"><?php _e( "YOUR QR CODE HASH IS <b style='color: red;'>NOT VALID!</b>", "qr-code-tag-for-wc-from-goaskle-com" ); ?></div>
        <div class="validated_thanks_qr_code_goaskle_desc_second"><?php _e( "HAVE A NICE DAY!", "qr-code-tag-for-wc-from-goaskle-com" ); ?></div>
                <div class="validated_thanks_qr_code_goaskle_desc_thank"><?php _e( "THANK YOU", "qr-code-tag-for-wc-from-goaskle-com" ); ?></div>
                <div><?php echo $hash_code; ?></div>
                <hr><br>
			</div>
    <?php
}

// ajax change hash data 21 02 23 goaskle activation of qr hash in order of woocommerce
function gaqr_make_used_qr_code_ajax_callback() {
	//wp_die('tut1');
$is_admin = current_user_can('administrator');
$msg = array();

if (!$is_admin){
$msg['msg'] = __( 'You have no right for this action!', 'qr-code-tag-for-wc-from-goaskle-com' );
$msg['res'] = 'not_admin';
wp_die(json_encode($msg));
}

check_ajax_referer( 'gaqr_make_used_qr_code', 'security' );
$order_id = $_POST['ord_id'];
$com = $_POST['com'];

if (metadata_exists('post', $order_id, '_goaskle_com_qr_code_hash_data')){
$hod = maybe_unserialize(get_post_meta( $order_id, '_goaskle_com_qr_code_hash_data', true ));
} else {
$hod = array();
	$hod['data']['hash_order_status'] = 0;
}

//wp_die(json_encode($hod));
if (isset($com)){

	switch ($com){
    case 'a':
//activation of hash    

//if active now
if ($hod['data']['hash_order_status'] == 1) {

//deactivate
$hod['data']['hash_order_status'] = 0;


if (isset($hod['data']['hash_using']) ) {

	$activations_count = count($hod['data']['hash_using']);
		if ($activations_count >= 1){
			$activations_count++;
			$hod['data']['hash_using'][$activations_count]['when_activated'] = time();
		} else {
				$hod['data']['hash_using'][1]['when_activated'] = time();
		}
} else {
//later rebuild (future) 
	$hod['data']['hash_using'][1]['when_activated'] = time();

}
$msg['msg'] = __( 'Hash Activated!', 'qr-code-tag-for-wc-from-goaskle-com' );
$msg['res'] = 'activated';
} else {
$msg['msg'] = __( 'Hash was Activated before, job cancelled', 'qr-code-tag-for-wc-from-goaskle-com' );
$msg['res'] = 'already_active';
	wp_die(json_encode($msg));
}
//
    break;
    case 'r':
//restore hash

if ($hod['data']['hash_order_status'] == 0) {

//activate
$hod['data']['hash_order_status'] = 1;

$msg['msg'] = __( 'Hash Restored and can be activated again', 'qr-code-tag-for-wc-from-goaskle-com' );
$msg['res'] = 'deactivated';
} else {
$msg['msg'] = __( 'Hash was Activated before, job cancelled', 'qr-code-tag-for-wc-from-goaskle-com' );
$msg['res'] = 'already_not_active';

	wp_die(json_encode($msg));
}

//
    break;
	 case 0:
//error
$msg['msg'] = __( 'Something went wrong, job not completed', 'qr-code-tag-for-wc-from-goaskle-com' );
$msg['res'] = 'error';
wp_die(json_encode($msg));
    break;    
}

//end switch
}



//$hod['data']['hash_times_for_use'] = -1;
//wp_die('tut7');
update_post_meta( $order_id, '_goaskle_com_qr_code_hash_data', maybe_serialize($hod));
//$mybid = 'all good '.$hod['data']['hash_order_status'];


/*
        if( is_wp_error( $result ) ) {
            // Process Error
        }*/
wp_die(json_encode($msg));
    }
    
    add_action( 'wp_ajax_gaqr_make_used_qr_code', 'gaqr_make_used_qr_code_ajax_callback' );
    add_action( 'wp_ajax_nopriv_gaqr_make_used_qr_code', 'gaqr_make_used_qr_code_ajax_callback' );
//

//IF HASH VALID THEN SHOW THIS THANKS YOU PAGE
function goaskle_com_qr_code_custom_thank_you_valid( $order_id ) {

$is_admin = current_user_can('administrator');
$order = wc_get_order( $order_id );

if (metadata_exists('post', $order_id, '_goaskle_com_qr_code_hash_data')){
$hash_data = maybe_unserialize(get_post_meta( $order_id, '_goaskle_com_qr_code_hash_data', true ));
$active_hash = $hash_data['data']['hash_order_status'];
$times_for_use_hash = (isset($hash_data['data']['hash_times_for_use'])) ? $hash_data['data']['hash_times_for_use'] : '';
} else {
	$hash_data = array();
	$active_hash = 0;
}

if (metadata_exists('post', $order_id, '_goaskle_com_qr_code_wc_order_hash')){
$hash_code = __( 'Hash:', 'qr-code-tag-for-wc-from-goaskle-com' )." ".get_post_meta( $order_id, '_goaskle_com_qr_code_wc_order_hash', true );
} else {
	$hash_code = '';
}
/*    if ( ! $order  || $order->has_status( 'failed' ) ){
    	
    }*/
//	echo "<pre>"; print_r($active_hash); echo "-111</pre>";
	
if ($is_admin && $active_hash > 0) {
 $butt_for_activate = '<button type="submit" id="make_used_qr_code" name="make_used_qr_code_use" href="#" class="butt-gaqr-adm butt-act-gaqr-adm button button-default">'.__( "Activate hash", "qr-code-tag-for-wc-from-goaskle-com" ).'</button>';	
}
if ($is_admin && $active_hash <= 0) {
 $butt_for_activate = '<button type="submit" id="make_used_qr_code" name="make_used_qr_code_restore" href="#" class="butt-gaqr-adm butt-deact-gaqr-adm button button-default">'.__( "Cancel activation", "qr-code-tag-for-wc-from-goaskle-com" ).'</button>';	
}




    ?>

<!-- //THE AJAX -->

<script>
jQuery( document ).ready( function() {
jQuery(function($){
    $( ".butt-gaqr-adm" ).on('click',click_on_hash_act);
    function click_on_hash_act (e) {
    document.getElementsByClassName('butt-gaqr-adm')[0].disabled = true;
var com = '0';

if (e.currentTarget.name == 'make_used_qr_code_restore') {
com = 'r';
} else if (e.currentTarget.name == 'make_used_qr_code_use') {
com = 'a';
} else {
com = '0';
}    
            var ajaxurl= '<?php echo admin_url('admin-ajax.php'); ?>';
		      var security    = '<?php echo wp_create_nonce( "gaqr_make_used_qr_code" ); ?>';
            e.preventDefault();
            var data = {
             action: 'gaqr_make_used_qr_code',
             security: security,
             com: com,
             ord_id: '<?php echo $order_id; ?>'
           };
             jQuery.post(ajaxurl, data, function(response) {
	var res = JSON.parse(response);
   var butt_for_activate = '<button type="submit" id="make_used_qr_code" name="make_used_qr_code_use" href="#" class="butt-gaqr-adm butt-act-gaqr-adm button button-default">'+'<?php echo __( "Activate hash", "qr-code-tag-for-wc-from-goaskle-com" ); ?>'+'</button>';
   var butt_for_deactivate = '<button type="submit" id="make_used_qr_code" name="make_used_qr_code_restore" href="#" class="butt-gaqr-adm butt-deact-gaqr-adm button button-default">'+'<?php echo __( "Cancel activation", "qr-code-tag-for-wc-from-goaskle-com" ); ?>'+'</button>';	
	switch(res.res){
		case 'activated':
		document.getElementsByClassName('butt-act-gaqr-adm')[0].outerHTML = butt_for_deactivate;
		document.getElementsByClassName('res_msg_gaqr_ajax')[0].innerHTML = res.msg;
		
      $( ".butt-gaqr-adm" ).on('click',click_on_hash_act);
      document.getElementsByClassName('butt-gaqr-adm')[0].disabled = false;
		break;
		
		case 'deactivated':
		document.getElementsByClassName('butt-deact-gaqr-adm')[0].outerHTML = butt_for_activate;
		document.getElementsByClassName('res_msg_gaqr_ajax')[0].innerHTML = res.msg;		
      $( ".butt-gaqr-adm" ).on('click',click_on_hash_act);		
      document.getElementsByClassName('butt-gaqr-adm')[0].disabled = false;      
		break;

		case 'already_not_active':
		document.getElementsByClassName('res_msg_gaqr_ajax')[0].innerHTML = res.msg;		
      document.getElementsByClassName('butt-gaqr-adm')[0].disabled = false;				
		break;		
		
		case 'already_active':
		document.getElementsByClassName('res_msg_gaqr_ajax')[0].innerHTML = res.msg;		
      document.getElementsByClassName('butt-gaqr-adm')[0].disabled = false;						
		break;		
		
		default:
		case 'error':

		document.getElementsByClassName('res_msg_gaqr_ajax')[0].innerHTML = res.msg;		
      document.getElementsByClassName('butt-gaqr-adm')[0].disabled = false;
		break;
	}

              //console.log(response);
              e.preventDefault();
            });

        }

}); 

} );
</script>

<style>
.validated_thanks_qr_code_goaskle_desc:before{
    content: "\2714";
    border: 1px solid;
    float: left;
    margin-right: 30px;
    margin-bottom: 50px;
    border-radius: 100%;
    color: darkgreen;
    background-color: lightgreen;
    outline: 6px solid green;
    padding-left: 25px;
    padding-right: 25px;
    font-size: 44px;
    font-weight: bold;
    }
.validated_thanks_qr_code_goaskle{
    margin: 0 auto;
    text-align: center;
    font-size: 30px;
    font-weight: 600;
    color: green;
}
</style>    

        <div class="goaskle_com_qr_code_valid_wrapper">
        <div class="validated_thanks_qr_code_goaskle_desc"><?php _e( "YOUR QR CODE HASH IS <b style='color: green;'>VALID!</b>", "qr-code-tag-for-wc-from-goaskle-com" ); ?></div>
        <div class="validated_thanks_qr_code_goaskle_desc_second"><?php _e( "HAVE A NICE DAY!", "qr-code-tag-for-wc-from-goaskle-com" ); ?></div>
                <div class="validated_thanks_qr_code_goaskle_desc_thank"><?php _e( "THANK YOU", "qr-code-tag-for-wc-from-goaskle-com" ); ?></div>
                <div><?php echo $hash_code; ?></div>
                <hr><br>
			</div>
			<?php if ($is_admin) { ?>
        <div class="goaskle_com_qr_code_valid_button_wrapper">
                <div class="res_msg_gaqr_ajax"></div>
        <?php echo $butt_for_activate; 
        if ($active_hash == 1) { ?>
        
        <!-- <div class="validated_thanks_qr_code_goaskle_never_activated"><?php _e( "NOT ACTIVATED", "qr-code-tag-for-wc-from-goaskle-com" ); ?></div>-->
        <?php } else { ?>
<!--                <div class="validated_thanks_qr_code_goaskle_was_activated"><?php _e( "WAS ACTIVATED: ", "qr-code-tag-for-wc-from-goaskle-com" ); ?></div>-->
                <?php } ?>
                <hr><br>
			</div>			
    <?php
    }
}

function goaskle_com_qr_code_thank_you_title_valid( $old_title ){
$old_title = '<div class="validated_thanks_qr_code_goaskle">'.__( 'CONGRATULATIONS!', 'qr-code-tag-for-wc-from-goaskle-com' ).'</div>';
 	return $old_title;
}

function goaskle_qr_code_woocommerce_checkout_order_created_action( $order ){

$current_order_id = $order->get_id();
$is_new_hash_unique = 0;
$new_hash_code = sha1(md5(uniqid()));

//write new hash to db
$new_gen_hash_for_order = update_post_meta( $current_order_id, '_goaskle_com_qr_code_wc_order_hash', $new_hash_code );

$hod = array();
$hod['data']['code']['first'] = $new_hash_code;
$hod['data']['code']['current'] = $new_hash_code;
$hod['data']['when_added_to_order'] = time();
$hod['data']['hash_order_status'] = 1;
$hod['data']['hash_times_for_use'] = -1;
$hod['data']['hash_last_date'] = -1;

	//checking unique or not
while ($is_new_hash_unique == 0) {

	$args = array('exclude' => array( $current_order_id ),'post_type' => 'shop_order','meta_key' => '_goaskle_com_qr_code_wc_order_hash','meta_value' => $new_hash_code,'return' => 'ids');
	$all_orders_wc_with_hash = wc_get_orders( $args );

	//total same hash
	$counto = (int)count($all_orders_wc_with_hash);
              
		if ($counto > 0){

		$is_new_hash_unique = 0;
		
			$new_hash_code = sha1(md5(uniqid()));
			$new_gen_hash_for_order = update_post_meta( $current_order_id, '_goaskle_com_qr_code_wc_order_hash', $new_hash_code );
			$hod['data']['code']['current'] = $new_hash_code;
		} else {
			$is_new_hash_unique = 1;
		}
} 
////write new hash data to db
update_post_meta( $current_order_id, '_goaskle_com_qr_code_hash_data', maybe_serialize($hod));
              

}

//260822 goaskle.com fixed no email footer

if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'])){
$disable_auto_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'];	
} else {
	$disable_auto_wc = 0;
}

if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['disable_pages_auto_wc'])){
$disable_pages_auto_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['disable_pages_auto_wc'];	
} else {
	$disable_pages_auto_wc = 0;
}

if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['disable_all_wc_emails_except_standard'])){
$disable_all_wc_emails_except_standard = get_option('qrct_from_goaskle_com_options')['shortcode']['disable_all_wc_emails_except_standard'];	
} else {
	$disable_all_wc_emails_except_standard = 0;
}

if ($disable_auto_wc == 0 && $disable_all_wc_emails_except_standard == 0) {
add_filter( 'woocommerce_email_footer_text', 'goaskle_com_filter_woocommerce_email_footer_text' );
} else {
    
    //020123 added for some emails wc
 if ($disable_auto_wc == 0 && $disable_all_wc_emails_except_standard == 1) {
    add_action( 'woocommerce_email_order_details', 'goaskle_com_add_custom_text_to_new_order_email', 10, 4 );
    
        function goaskle_com_add_custom_text_to_new_order_email( $order, $sent_to_admin, $plain_text, $email ) {
        // Only for "New Order"
        
        $enable_new_order_wc_email = get_option('qrct_from_goaskle_com_options')['shortcode']['qrct_from_goaskle_com_sc_new_order_wc_emails_enable'];
        $enable_fail_order_wc_email = get_option('qrct_from_goaskle_com_options')['shortcode']['qrct_from_goaskle_com_sc_failed_order_wc_emails_enable'];
        $enable_hold_order_wc_email = get_option('qrct_from_goaskle_com_options')['shortcode']['qrct_from_goaskle_com_sc_customer_on_hold_order_wc_emails_enable'];
        $enable_process_order_wc_email = get_option('qrct_from_goaskle_com_options')['shortcode']['qrct_from_goaskle_com_sc_customer_processing_order_wc_emails_enable'];
        $enable_completed_order_wc_email = get_option('qrct_from_goaskle_com_options')['shortcode']['qrct_from_goaskle_com_sc_customer_completed_order_wc_emails_enable'];
        $enable_refund_order_wc_email = get_option('qrct_from_goaskle_com_options')['shortcode']['qrct_from_goaskle_com_sc_customer_refunded_order_wc_emails_enable'];
        $enable_part_refund_order_wc_email = get_option('qrct_from_goaskle_com_options')['shortcode']['qrct_from_goaskle_com_sc_customer_partially_refunded_order_wc_emails_enable'];
        $enable_canc_order_wc_email = get_option('qrct_from_goaskle_com_options')['shortcode']['qrct_from_goaskle_com_sc_cancelled_order_wc_emails_enable'];
        $enable_inv_order_wc_email = get_option('qrct_from_goaskle_com_options')['shortcode']['qrct_from_goaskle_com_sc_customer_invoice_wc_emails_enable'];

            if(  'new_order' <> $email->id && 'failed_order' <> $email->id && 'customer_on_hold_order' <> $email->id && 'customer_processing_order' <> $email->id && 'customer_completed_order' <> $email->id  && 'customer_refunded_order' <> $email->id && 'customer_partially_refunded_order' <> $email->id  && 'cancelled_order' <> $email->id && 'customer_invoice' <> $email->id ) {
                return;
            }
           
            if('new_order' == $email->id && $enable_new_order_wc_email == 0 ||  'new_order' == $email->id && $sent_to_admin){
                return;
            }

            if('failed_order' == $email->id && $enable_fail_order_wc_email == 0){
                return;
            }
            
            if('customer_on_hold_order' == $email->id && $enable_hold_order_wc_email == 0 || 'customer_on_hold_order' == $email->id && $sent_to_admin){
                return;
            }
            
            if('customer_processing_order' == $email->id && $enable_process_order_wc_email == 0 || 'customer_processing_order' == $email->id && $sent_to_admin){
                return;
            }
            
            if('customer_completed_order' == $email->id  && $enable_completed_order_wc_email == 0 || 'customer_completed_order' == $email->id && $sent_to_admin){
                return;
            }
            
            if('customer_refunded_order' == $email->id && $enable_refund_order_wc_email == 0 || 'customer_refunded_order' == $email->id && $sent_to_admin){
                return;
            }
            
            if('customer_partially_refunded_order' == $email->id  && $enable_part_refund_order_wc_email == 0 || 'customer_partially_refunded_order' == $email->id&& $sent_to_admin){
                return;
            }
            
            if('cancelled_order' == $email->id && $enable_canc_order_wc_email == 0 ){
                return;
            }
            
            if('customer_invoice' == $email->id && $enable_inv_order_wc_email == 0 || 'customer_invoice' == $email->id && $sent_to_admin) {
                return;
            }
            add_filter( 'woocommerce_email_footer_text', 'goaskle_com_filter_woocommerce_email_footer_text' );
        }
 }
}

function goaskle_com_filter_woocommerce_email_footer_text( $option ){
    echo wp_kses_post("<div style='text-align: center;'>".do_shortcode('[qrcodetag_from_goaskle_com/]')."</div>");
	return $option;
}
//wcdn_after_items
//wcdn_after_info

if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'])){
$disable_auto_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'];	
} else {
	$disable_auto_wc = 0;
}

if ( ! class_exists( 'WCDN_Component' ) && $disable_auto_wc == 0 && $disable_pages_auto_wc == 0) {
add_action( 'wcdn_after_items', 'goaskle_wcdn_qrcode_after_items', 10, 1 );
}
function goaskle_wcdn_qrcode_after_items(){
	    echo wp_kses_post("<div style='text-align: center;margin-top: 10px;'>".do_shortcode('[qrcodetag_from_goaskle_com/]')."</div>");
}

if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'])){
$disable_auto_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'];	
} else {
	$disable_auto_wc = 0;
}

if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['disable_all_wc_emails_except_standard'])){
$disable_all_wc_emails_except_standard = get_option('qrct_from_goaskle_com_options')['shortcode']['disable_all_wc_emails_except_standard'];	
} else {
	$disable_all_wc_emails_except_standard = 0;
}

if ($disable_auto_wc == 0 && $disable_pages_auto_wc == 0) {
add_action( 'wpo_wcpdf_after_order_details', 'goaskle_com_wpo_wcpdf_after_footer',777,2 );
}

function goaskle_com_wpo_wcpdf_after_footer($type,$order) {
   echo wp_kses_post("<div style='text-align: right;'>".do_shortcode('[qrcodetag_from_goaskle_com/]')."</div>");
}

if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'])){
$disable_auto_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'];	
} else {
	$disable_auto_wc = 0;
}

if ($disable_auto_wc == 0 && $disable_pages_auto_wc == 0) {
add_action( 'woocommerce_thankyou', 'goaskle_com_add_content_thankyou',777 );
}

function goaskle_com_add_content_thankyou() {
   echo wp_kses_post("<div style='text-align: center;'>".do_shortcode('[qrcodetag_from_goaskle_com/]')."</div>");
}

add_action( 'wp_enqueue_scripts', 'theme_add_scripts' );

function theme_add_scripts() {
        // include javascript and css styles
        
        //wp_enqueue_script('jquery');
        //wp_enqueue_script('jquery-tooltip', GOASKLEPLUGINURL.'js/jquery.tooltip.min.js','jquery');
        //wp_enqueue_script('qrcodetagging', GOASKLEPLUGINURL.'js/qrct.js','jquery-tooltip');
        wp_enqueue_style('qrcodetagging_from_goaskle_com', GOASKLEPLUGINURL.'css/qrct.css');
        //print_r(GOASKLEPLUGINURL);
        
} 
        // add "Settings" link in the plugins list
        add_filter('plugin_action_links_'.GOASKLEPLUGINBASE, array($this, 'addConfigureLink'));
    }
    
    public function QrctWp_from_Goaskle_Com($baseFile){
    self::__construct($baseFile);
}    
  
    /**
    * WordPress Filter for adding a "Settings" links in the plugins List 
    * 
    * @param  $links
    * @return unknown_type
    */
    public function addConfigureLink($links) 
    { 
        $settings_link = '<a href="options-general.php?page='.GOASKLEPLUGINBASE.'">'.
                        __('Settings','qr-code-tag-for-wc-from-goaskle-com').'</a>';
        array_unshift($links, $settings_link ); 
        return $links; 
    }
    
    /**
    * WordPress Admin SubMenu Page setting
    */
    public function setAdminMenu() 
    {
        add_submenu_page('options-general.php', __('QR Code Tag (from Goaskle.com)'), __('QR Code Tag (from Goaskle.com)'), 'edit_pages', GOASKLEPLUGINBASE, array($this, 'adminSettingsPage'));
    }
  
    /**
    * Action on Plugin Activation
    * 
    * @return boolean TRUE
    */
    public function activate()
    {
        if (!get_option($this->pluginOptions)) { // if options not set before, set it now
            add_option($this->pluginOptions, $this->defaultOptions);
        } else { // else reset to default options
            update_option($this->pluginOptions, $this->defaultOptions);
        }
        return TRUE;
    } 
  
    /**
    * Action on Plugin Deactivation
    * 
    * @return boolean TRUE
    */
    public function deactivate()
    {
        remove_action('widgets_init',array($this, 'initWidget')); // remove widget
        remove_shortcode('qrcodetag_from_goaskle_com'); //remove shortcode
        delete_option($this->pluginOptions); // delete plugin options
        return TRUE;
    }
  
    /**
    * WordPress Widget initialization
    */
    public function initWidget()
    {
        // register widget and control page
        wp_register_sidebar_widget('widget_qrct_from_goaskle_com', $this->pluginName, array($this, 'widget'));
        wp_register_widget_control('widget_qrct_from_goaskle_com','Your QR Code', array($this, 'widgetControl'));  
    }

    /**
    * Get current script URL
    * 
    * @return  string  Current URL
    */
    public function currentUrl() 
    {
        $pageURL = 'http';
        if ((isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] == 'on')) { 
            $pageURL .= "s"; 
        }
        $pageURL .= '://';
     
        if ($_SERVER['SERVER_PORT'] != '80') { // add port if not standard
            $pageURL .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
        } else {
            $pageURL .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        }
        return $pageURL;
    }
  
    /**
    * Get the URL for a QR Code based on given parameters 
    * 
    * @param  string    $content  content to be encoded  
    * @param  integer   $size     image size
    * @param  string    $enc      encoding format
    * @param  string    $ecc      error correction type
    * @param  integer   $margin   image margin
    * @param  integer   $version  QR Code version
    * @return string
    */
    public function getQrcodeUrl($content, $size, $enc, $ecc, $margin, $version) 
    {
        // if qr code object not created yet, do now
        if (!$this->qrcode) {
      
            // load global options
            $options = get_option($this->pluginOptions);
            $options = $options['global'];

            // set QR Code file extension for later re-use
            $this->qrcodeExt = $options['ext'];
      
            // based on generator setting use appropriate class
            $generator = $options['generator']; 
            if ($generator == 'google') {
                require_once(dirname(__FILE__).'/QrcodeGoogle.php');
                $this->qrcode = new QrcodeGoogle_from_Goaskle_Com();
            } else {
                require_once(dirname(__FILE__).'/QrcodeLib.php');
                $this->qrcode = new QrcodeLib_from_Goaskle_Com();
            }
        }
    
        // get QR Code and return URL

        $file = $this->qrcode->get($content, $this->qrcodeExt, $size, $enc, $ecc, $margin, $version);
        return GOASKLEPLUGINURL.'data/'.$file;
    }
  
    /**
    * WordPress widget call
    * 
    * @param $args
    */
    public function widget($args)
    {
        // import variables into the current symbol table
        extract($args);
    
        // load widget options
        $pluginOptions = get_option($this->pluginOptions);
        $options = $pluginOptions['widget'];
    
        // load content
        $content = $options['text'];
        // Display the widget  
        echo $before_widget;
        echo $before_title;
        echo esc_html($options['title']);
        echo $after_title;
        echo '<div class="qrcode">'.esc_html($options['before']);
        echo wp_kses_post($this->createTag($content, $options, NULL));
        echo esc_html($options['after'])."</div>";
        echo $after_widget;
    }

    /**
    * WordPress widget administration call 
    */


    public function widgetControl()
    {
        // load widget options
        $options = get_option($this->pluginOptions);
  
        // check if user submitted changes
        if (isset($_POST['widget_qrct_from_goaskle_com-submit']) && $_POST['widget_qrct_from_goaskle_com-submit']) {

            // update widget options array
            $options['widget']['title'] = sanitize_text_field($_POST['widget_qrct_from_goaskle_com-title']);
            
            if (isset($_POST['widget_qrct_from_goaskle_com-before']) ){
            $options['widget']['before'] = sanitize_text_field($_POST['widget_qrct_from_goaskle_com-before']);
            
            } else {
                $options['widget']['before'] = 'qrcode';
            }


            $options['widget']['text'] = sanitize_text_field($_POST['widget_qrct_from_goaskle_com-text']);

if (isset($_POST['widget_qrct_from_goaskle_com-before']) && isset($_POST['widget_qrct_from_goaskle_com-after'])) {
    $options['widget']['after'] = sanitize_text_field($_POST['widget_qrct_from_goaskle_com-after']);
} else{
    $options['widget']['after']  = '';
}
            
    
            // we also update the options in the WordPress database
            update_option($this->pluginOptions, $options);
        }
     
        echo '<p><label><input id="widget_qrct_from_goaskle_com-title" class="widefat" type="text" value="'.esc_html($options['widget']['title']).'" name="widget_qrct_from_goaskle_com-title"></p>';
        echo '<p><label>'.__('Before QR Code:', 'qr-code-tag-for-wc-from-goaskle-com').'<input id="widget_qrct_from_goaskle_com-before" class="widefat" type="text" value="'.esc_html($options['widget']['before']).'" name="widget_qrct_from_goaskle_com-before"></p>';
        echo '<p><label>'.__('Content (blank for current URL):', 'qr-code-tag-for-wc-from-goaskle-com').'<input id="widget_qrct_from_goaskle_com-text" class="widefat" type="text" value="'.esc_html($options['widget']['text']).'" name="widget_qrct_from_goaskle_com-text"></p>';
        echo '<p><label>'.__('After QR Code:', 'qr-code-tag-for-wc-from-goaskle-com').'<input id="widget_qrct_from_goaskle_com-after" class="widefat" type="text" value="'.esc_html($options['widget']['after']).'" name="widget_qrct_from_goaskle_com-after"></p>';
        echo '<input type="hidden" id="widget_qrct_from_goaskle_com-submit" name="widget_qrct_from_goaskle_com-submit" value="1" />';
    }

    /**
    * Create a wrap if the option is specified else use default string
    * 
    * @param  string  $option   option name
    * @param  string  $name     wrap name
    * @param  string  $default  default value if option is empty
    * @return string
    */
    public function optionWrap($option, $name, $default)
    {
        // if option isn't empty wrap it
        if ($option != '') {
            return ' '.$name.'="'.$option.'"';
        } else { // else return default value
            return $default;
        }
    }
  
    /**
    * Return a QR Code, this is subfunction used by Widget and Shortcode
    * 
    * @param  string   $content  QR Code content
    * @param  array    $options  options array    
    * @param  mixed    $atts     param attributes    
    * @return string
    */
    public function createTag($content, $options, $atts)
    {
        // extract attributes into the current symbol table, with default settings if not specified
        extract(shortcode_atts(array(  
            'size' => $options['size'],
            'enc' => $options['enc'],
            'ecc' => $options['ecc'],
            'margin' => $options['margin'],
            'version' => $options['version'],
            'imageparam' => $options['imageparam'],
            'link' => $options['link'],
            'atagparam' => $options['atagparam'],
            'tooltip' => $options['tooltip']), 
        $atts));
    
        // tooltip mode check
        $tooltipMode = ($tooltip != '');
    
        // convert html entities back to characters  
        $atagparam = htmlspecialchars_decode($atagparam);
        $imageparam = htmlspecialchars_decode($imageparam);
        
        // if no or empty content then use current url as content
        if ((is_null($content)) || ($content == '') || ($content =='_')) {
            $content = $this->currentUrl();
        } else {
            $content = do_shortcode($content); // be sure to resolve other shortcodes as well
        }

        // if tooltip mode is enabled, switch content and visible Text, then load tooltip options
        if ($tooltipMode) {
            // switch content
            $tooltipText = $content;
            $content = $tooltip;
      
            // load tooltip options
            $pluginOptions = get_option("qrct_from_goaskle_com_options");
            $options = $pluginOptions['tooltip'];
      
            // apply tooltip options
            extract(shortcode_atts(array(  
                'size' => $options['size'],
                'enc' => $options['enc'],
                'ecc' => $options['ecc'],
                'margin' => $options['margin'],
                'version' => $options['version']),
            $atts));
        }
        

        // create QR Code URL
        
                //print_r($content);exit;
                
        //echo "<pre>"; print_r (htmlspecialchars($content, ENT_QUOTES)); echo "</pre>";
        $url = $this->getQrcodeUrl($content, $size, $enc, $ecc, $margin, $version);
        //echo "<pre>"; print_r (htmlspecialchars($url, ENT_QUOTES)); echo "</pre>";

        // check for styling options
        if ($atagparam) {
            $atagparam = ' class="'.$atagparam.'" ';
        }
    
        // check for image styling options
        if ($imageparam) {
            $imageparam = ''.$imageparam;
        }
    
        // create image tag
        $img = '<img src="'.$url.'" class="'.$imageparam.'" />';
        //$img = '<img src="'.$url.'" />';
    
        // check for link options and set linkTarget
        $linkTarget = '';

        if ($link == 'true') {
            $linkTarget = $content;
        } elseif ($link == 'url') {
            $linkTarget = $this->currentUrl();
        } elseif (($link != '') && ($link != 'false')) {
            $linkTarget = $link;
        }
    
        // if there is a link then create html link wrap
        if ($linkTarget != '') {
            $linkWrap = '<a href="'.$linkTarget.'"'.' target="_blank" '.$atagparam.'>';
            $linkWrapEnd = '</a>';
        } else {
            $linkWrap ='';
            $linkWrapEnd = '';
        }



        // if tooltip mode enabled, wrap with span class for jquery-tooltip
        if ($tooltip != '') {
            $r = "<img src='".$url."'>";
            return '<span data-placement="bottom" data-html="true" data-toggle="tooltip" class="qrcttooltip" title="'.$r.'">'.$tooltipText.'</span>';
        } else {
            return $linkWrap.$img.$linkWrapEnd;
        }
    }
  
    /**
    * WordPress shortcode call, return QR Code HTML image string
    * 
    * @param  mixed   $atts     shortcode attributes
    * @param  string  $content  QR Code content
    * @return string  
    */
    public function shortcode($atts, $content = NULL) 
    {
     
        // load shortcode options
        $pluginOptions = get_option($this->pluginOptions);
        $options = $pluginOptions['shortcode'];
        
        //goaskle.com 01 11 2021 add to wc qr code
        //var_dump($content);
        
        if (!isset($atts['tooltip'])) {
      global $woocommerce;

if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'])){
$disable_auto_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['disable_auto_wc'];	
} else {
	$disable_auto_wc = 0;
}
   

if (function_exists( 'WC' ) && (class_exists( 'woocommerce' )  || !empty($woocommerce) || null !== WC()) && $content !== NULL && !$content && $disable_auto_wc == 0) {
$wcid = 0;$goa_customer = '';$goa_total = 0;$goa_tax_total = 0;$goa_date = '';$goa_time = '';$goa_vat = '';$goa_domain ='';$order = '';

global $wp;

if ( (isset($wp->query_vars['order-received']) || isset($_GET['order_ids']) ) && !isset($wp->query_vars['print-order']) && !isset($_GET['print-order']) && !isset($wp->query_vars[get_option( 'wcdn_print_order_page_endpoint', 'print-order' )])){

        if (isset($_GET['order_ids']) && $_GET['order_ids'] > 0) {
                $wcid = (int)sanitize_text_field($_GET['order_ids']);
                $options['size'] = 150;
        } else {
               $wcid = absint($wp->query_vars['order-received']);   
    }

    $order    = wc_get_order( $wcid ); // The WC_Order object
    $wc_ord_numb = $order->get_order_number();
 
    //fields 
    $goa_customer = $order->get_billing_first_name()." ".$order->get_billing_last_name();
    $goa_total = html_entity_decode(strip_tags($order->get_formatted_order_total()));
    $goa_total_nocurr = html_entity_decode(strip_tags($order->get_total()));
    $goa_tax_total = ($order->get_total_tax()>0) ? "VAT AMOUNT ".html_entity_decode(strip_tags(wc_price($order->get_total_tax())))."\n" : '';
    $goa_tax_total_nolabel = ($order->get_total_tax()>0) ? html_entity_decode(strip_tags(wc_price($order->get_total_tax()))) : '';
    $goa_date = date("d/m/Y",strtotime($order->get_date_created()));
    $goa_date_day = date("d",strtotime($order->get_date_created()));
    $goa_date_mon = date("m",strtotime($order->get_date_created()));
    $goa_date_year = date("Y",strtotime($order->get_date_created()));
    
    $goa_time = date("H:i",strtotime($order->get_date_created()));
    $goa_vat = $options['vat_number'];
    $goa_wc_dat_tem = $options['wc_data_template'];
    $goa_domain = get_site_url();

//hash
    if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'])){
	 $hash_order_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'];	
	 } else {
	 $hash_order_wc = 0;
	 }
	 if ($hash_order_wc == 1) {
    $goa_hash = get_post_meta( $wcid, '_goaskle_com_qr_code_wc_order_hash', true );
	 } else {
	 	$goa_hash = '';
	 }
//    hash-gaqr





    /* product items variables 09 11 2022 */
foreach ( $order->get_items() as $item ){
    
    $goa_order_items_id[] = $item->get_product_id();
    $goa_order_items_name[] = $item->get_name();
    $goa_order_items_qty[] = html_entity_decode(strip_tags($item->get_quantity()));
    
    $product        = $item->get_product();
    $active_price   = $product->get_price(); // The product active raw price

    //$regular_price  = $product->get_sale_price(); // The product raw sale price
    //$sale_price     = $product->get_regular_price();
    
    $goa_order_items_price[] = strip_tags($active_price);
    $goa_order_items_subtot[] = strip_tags($item->get_total());
}    

} else {
    if (isset($email) && isset($email->object) && $email->object->get_id() > 0){
       
        $order = $email->object;
        $wc_ord_numb = $order->get_order_number();
        $wcid = $order->get_id();

    $goa_customer = $order->get_billing_first_name()." ".$order->get_billing_last_name();
    $goa_total = html_entity_decode(strip_tags($order->get_formatted_order_total()));
    $goa_total_nocurr = html_entity_decode(strip_tags($order->get_total()));    
    $goa_tax_total = ($order->get_total_tax()>0) ? "VAT AMOUNT ".html_entity_decode(strip_tags(wc_price($order->get_total_tax())))."\n" : '';
    $goa_tax_total_nolabel = ($order->get_total_tax()>0) ? html_entity_decode(strip_tags(wc_price($order->get_total_tax()))) : '';
    $goa_date = date("d/m/Y",strtotime($order->get_date_created()));
    $goa_date_day = date("d",strtotime($order->get_date_created()));
    $goa_date_mon = date("m",strtotime($order->get_date_created()));
    $goa_date_year = date("Y",strtotime($order->get_date_created()));
    
    $goa_time = date("H:i",strtotime($order->get_date_created()));
    $goa_vat = $options['vat_number'];
    $goa_wc_dat_tem = $options['wc_data_template'];
    $goa_domain = get_site_url();

//hash
    if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'])){
	 $hash_order_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'];	
	 } else {
	 $hash_order_wc = 0;
	 }
	 if ($hash_order_wc == 1) {
    $goa_hash = get_post_meta( $wcid, '_goaskle_com_qr_code_wc_order_hash', true );
	 } else {
	 	$goa_hash = '';
	 }
//    hash-gaqr    

    /* product items variables 09 11 2022 */
foreach ( $order->get_items() as $item ){
    
    $goa_order_items_id[] = $item->get_product_id();
    $goa_order_items_name[] = $item->get_name();
    $goa_order_items_qty[] = html_entity_decode(strip_tags($item->get_quantity()));
    
    $product        = $item->get_product();
    $active_price   = $product->get_price(); // The product active raw price

    //$regular_price  = $product->get_sale_price(); // The product raw sale price
    //$sale_price     = $product->get_regular_price();
    
    $goa_order_items_price[] = strip_tags($active_price);
    $goa_order_items_subtot[] = strip_tags($item->get_total());
} 
   
} elseif (isset($woocommerce) && null !== WC() && isset(WC()->session) && WC()->session->order_awaiting_payment > 0 ){

        $order = wc_get_order( WC()->session->order_awaiting_payment );
        $wc_ord_numb = $order->get_order_number();

            if (function_exists('WPO_WCPDF') && null !== WPO_WCPDF()) {$options['size'] = 150;}

        if ( $order->get_id() > 0 ) {
            $wcid = $order->get_id();

    $goa_customer = $order->get_billing_first_name()." ".$order->get_billing_last_name();
    $goa_total = html_entity_decode(strip_tags($order->get_formatted_order_total()));
    $goa_total_nocurr = html_entity_decode(strip_tags($order->get_total()));    
    $goa_tax_total = ($order->get_total_tax()>0) ? "VAT AMOUNT ".html_entity_decode(strip_tags(wc_price($order->get_total_tax())))."\n" : '';
    $goa_tax_total_nolabel = ($order->get_total_tax()>0) ? html_entity_decode(strip_tags(wc_price($order->get_total_tax()))) : '';
    $goa_date = date("d/m/Y",strtotime($order->get_date_created()));
    $goa_date_day = date("d",strtotime($order->get_date_created()));
    $goa_date_mon = date("m",strtotime($order->get_date_created()));
    $goa_date_year = date("Y",strtotime($order->get_date_created()));
    
    $goa_time = date("H:i",strtotime($order->get_date_created()));
    $goa_vat = $options['vat_number'];
    $goa_wc_dat_tem = $options['wc_data_template'];
    $goa_domain = get_site_url();
    
//hash
    if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'])){
	 $hash_order_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'];	
	 } else {
	 $hash_order_wc = 0;
	 }
	 if ($hash_order_wc == 1) {
    $goa_hash = get_post_meta( $wcid, '_goaskle_com_qr_code_wc_order_hash', true );
	 } else {
	 	$goa_hash = '';
	 }
//    hash-gaqr    

    /* product items variables 09 11 2022 */
foreach ( $order->get_items() as $item ){
    
    $goa_order_items_id[] = $item->get_product_id();
    
    $goa_order_items_name[] = $item->get_name();
    $goa_order_items_qty[] = html_entity_decode(strip_tags($item->get_quantity()));
    
    $product        = $item->get_product();
    $active_price   = $product->get_price(); // The product active raw price

    //$regular_price  = $product->get_sale_price(); // The product raw sale price
    //$sale_price     = $product->get_regular_price();
    
    $goa_order_items_price[] = strip_tags($active_price);
    $goa_order_items_subtot[] = strip_tags($item->get_total());
}

        }
  
    //}
    } elseif (null !== WC() && WC()->order_factory && WC()->order_factory->get_order() && WC()->order_factory->get_order()->get_id() > 0){
        $wcid = WC()->order_factory->get_order()->get_id();


    $order = wc_get_order($wcid);
    $wc_ord_numb = $order->get_order_number();
           
            if (function_exists('WPO_WCPDF') && null !== WPO_WCPDF()) {$options['size'] = 150;}

    //fields
    $goa_customer = $order->get_billing_first_name()." ".$order->get_billing_last_name();
    $goa_total = html_entity_decode(strip_tags($order->get_formatted_order_total()));
    $goa_total_nocurr = html_entity_decode(strip_tags($order->get_total()));    
    $goa_tax_total = ($order->get_total_tax()>0) ? "VAT AMOUNT ".html_entity_decode(strip_tags(wc_price($order->get_total_tax())))."\n" : '';
    $goa_tax_total_nolabel = ($order->get_total_tax()>0) ? html_entity_decode(strip_tags(wc_price($order->get_total_tax()))) : '';
    $goa_date = date("d/m/Y",strtotime($order->get_date_created()));
    $goa_date_day = date("d",strtotime($order->get_date_created()));
    $goa_date_mon = date("m",strtotime($order->get_date_created()));
    $goa_date_year = date("Y",strtotime($order->get_date_created()));
    
    $goa_time = date("H:i",strtotime($order->get_date_created()));
    $goa_vat = $options['vat_number'];
    $goa_wc_dat_tem = $options['wc_data_template'];
    $goa_domain = get_site_url();

//hash
    if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'])){
	 $hash_order_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'];	
	 } else {
	 $hash_order_wc = 0;
	 }
	 if ($hash_order_wc == 1) {
    $goa_hash = get_post_meta( $wcid, '_goaskle_com_qr_code_wc_order_hash', true );
	 } else {
	 	$goa_hash = '';
	 }
//    hash-gaqr    
    
    /* product items variables 09 11 2022 */
foreach ( $order->get_items() as $item ){
    
    $goa_order_items_id[] = $item->get_product_id();
    
    $goa_order_items_name[] = $item->get_name();
    $goa_order_items_qty[] = html_entity_decode(strip_tags($item->get_quantity()));
    
    $product        = $item->get_product();
    $active_price   = $product->get_price(); // The product active raw price

    //$regular_price  = $product->get_sale_price(); // The product raw sale price
    //$sale_price     = $product->get_regular_price();
    
    $goa_order_items_price[] = strip_tags($active_price);
    $goa_order_items_subtot[] = strip_tags($item->get_total());
} 
	
} elseif (null !== WC() && WC()->structured_data && WC()->structured_data->get_data() && isset(WC()->structured_data->get_data()[0]) && isset(WC()->structured_data->get_data()[0]['orderNumber']) && WC()->structured_data->get_data()[0]['orderNumber'] > 0){
    
        $wcid = WC()->structured_data->get_data()[0]['orderNumber'];
        $wc_ord_numb = $wcid;
    $order = wc_get_order($wcid);
    
            if (function_exists('WPO_WCPDF') && null !== WPO_WCPDF()) {$options['size'] = 150;}

    //fields
    $goa_customer = $order->get_billing_first_name()." ".$order->get_billing_last_name();
    $goa_total = html_entity_decode(strip_tags($order->get_formatted_order_total()));
    $goa_total_nocurr = html_entity_decode(strip_tags($order->get_total()));    
    $goa_tax_total = ($order->get_total_tax()>0) ? "VAT AMOUNT ".html_entity_decode(strip_tags(wc_price($order->get_total_tax())))."\n" : '';
    $goa_tax_total_nolabel = ($order->get_total_tax()>0) ? html_entity_decode(strip_tags(wc_price($order->get_total_tax()))) : '';
    $goa_date = date("d/m/Y",strtotime($order->get_date_created()));
    $goa_date_day = date("d",strtotime($order->get_date_created()));
    $goa_date_mon = date("m",strtotime($order->get_date_created()));
    $goa_date_year = date("Y",strtotime($order->get_date_created()));
    
    $goa_time = date("H:i",strtotime($order->get_date_created()));
    $goa_vat = $options['vat_number'];
    $goa_wc_dat_tem = $options['wc_data_template'];
    $goa_domain = get_site_url();  

//hash
    if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'])){
	 $hash_order_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'];	
	 } else {
	 $hash_order_wc = 0;
	 }
	 if ($hash_order_wc == 1) {
    $goa_hash = get_post_meta( $wcid, '_goaskle_com_qr_code_wc_order_hash', true );
	 } else {
	 	$goa_hash = '';
	 }
//    hash-gaqr    
    
    /* product items variables 09 11 2022 */
foreach ( $order->get_items() as $item ){
    
    $goa_order_items_id[] = $item->get_product_id();
    $goa_order_items_name[] = $item->get_name();
    $goa_order_items_qty[] = html_entity_decode(strip_tags($item->get_quantity()));
    
    $product        = $item->get_product();
    $active_price   = $product->get_price(); // The product active raw price

    //$regular_price  = $product->get_sale_price(); // The product raw sale price
    //$sale_price     = $product->get_regular_price();
    
    $goa_order_items_price[] = strip_tags($active_price);
    $goa_order_items_subtot[] = strip_tags($item->get_total());
}
	
}   elseif (null !== WC() && class_exists( 'WCDN_Component' ) && isset($wp->query_vars['print-order']) || isset($_GET['print-order']) || isset($wp->query_vars[get_option( 'wcdn_print_order_page_endpoint', 'print-order' )]) ){

        if (isset($_GET['print-order']) && $_GET['print-order'] > 0 && isset($_GET['action']) && $_GET['action'] == 'print_order') {
                $wcid = (int)sanitize_text_field($_GET['print-order']);
                $options['size'] = 150;
        } else {
               $wcid = absint($wp->query_vars['print-order']);   
    }
    if (!$wcid){
        $wcid = $wp->query_vars[get_option( 'wcdn_print_order_page_endpoint', 'print-order' )];
    }

    $order    = wc_get_order( $wcid ); // The WC_Order object
    $wc_ord_numb = $order->get_order_number();

    //fields 
    $goa_customer = $order->get_billing_first_name()." ".$order->get_billing_last_name();
    $goa_total = html_entity_decode(strip_tags($order->get_formatted_order_total()));
    $goa_total_nocurr = html_entity_decode(strip_tags($order->get_total()));
    $goa_tax_total = ($order->get_total_tax()>0) ? "VAT AMOUNT ".html_entity_decode(strip_tags(wc_price($order->get_total_tax())))."\n" : '';
    $goa_tax_total_nolabel = ($order->get_total_tax()>0) ? html_entity_decode(strip_tags(wc_price($order->get_total_tax()))) : '';
    $goa_date = date("d/m/Y",strtotime($order->get_date_created()));
    $goa_date_day = date("d",strtotime($order->get_date_created()));
    $goa_date_mon = date("m",strtotime($order->get_date_created()));
    $goa_date_year = date("Y",strtotime($order->get_date_created()));
    
    $goa_time = date("H:i",strtotime($order->get_date_created()));
    $goa_vat = $options['vat_number'];
    $goa_wc_dat_tem = $options['wc_data_template'];
    $goa_domain = get_site_url();
    
//hash
    if (isset(get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'])){
	 $hash_order_wc = get_option('qrct_from_goaskle_com_options')['shortcode']['hash_order_wc'];	
	 } else {
	 $hash_order_wc = 0;
	 }
	 if ($hash_order_wc == 1) {
    $goa_hash = get_post_meta( $wcid, '_goaskle_com_qr_code_wc_order_hash', true );
	 } else {
	 	$goa_hash = '';
	 }
//    hash-gaqr

    /* product items variables 09 11 2022 */
        foreach ( $order->get_items() as $item ){
    
            $goa_order_items_id[] = $item->get_product_id();
            $goa_order_items_name[] = $item->get_name();
            $goa_order_items_qty[] = html_entity_decode(strip_tags($item->get_quantity()));
    
            $product        = $item->get_product();
            $active_price   = $product->get_price(); // The product active raw price

            //$regular_price  = $product->get_sale_price(); // The product raw sale price
            //$sale_price     = $product->get_regular_price();
    
            $goa_order_items_price[] = strip_tags($active_price);
            $goa_order_items_subtot[] = strip_tags($item->get_total());
        }        
   
   
    	
   } else {
       
        //return;
    }
    
}

if (isset($wcid) && $wcid > 0){
    // 250822 goaskle.com added support for wc data template manually edited in admin settings
    if (isset($goa_wc_dat_tem) && !empty($goa_wc_dat_tem) && $goa_wc_dat_tem && !$goa_wc_dat_tem == ''){


 $goa_wc_dat_tem = str_replace('{order_id}', $wcid, $goa_wc_dat_tem);  //replace {order_id}
 $goa_wc_dat_tem = str_replace('{wc_order_number}', $wc_ord_numb, $goa_wc_dat_tem);  //replace {wc_order_number} //197


 $goa_wc_dat_tem = str_replace('{full_customer_name}', $goa_customer, $goa_wc_dat_tem);  //replace {full_customer_name}
 $goa_wc_dat_tem = str_replace('{total_amount}', $goa_total, $goa_wc_dat_tem);  //replace {total_amount}
 $goa_wc_dat_tem = str_replace('{total_amount_no_curr}', $goa_total_nocurr, $goa_wc_dat_tem);  //replace {total_amount_no_curr}
 $goa_wc_dat_tem = str_replace('{vat_amount}', $goa_tax_total_nolabel, $goa_wc_dat_tem);  //replace {vat_amount}
 
 if (strpos($goa_vat, 'Enter you VAT') === false) {
    $goa_wc_dat_tem = str_replace('{vat_number}', $goa_vat, $goa_wc_dat_tem);  //replace {vat_number}
 } else {
    $goa_wc_dat_tem = str_replace('{vat_number}', '', $goa_wc_dat_tem);  //replace {vat_number}
 }
 $goa_wc_dat_tem = str_replace('{order_date}', $goa_date, $goa_wc_dat_tem);  //replace {order_date}
 $goa_wc_dat_tem = str_replace('{order_date_day}', $goa_date_day, $goa_wc_dat_tem);  //replace {order_date_day}
 $goa_wc_dat_tem = str_replace('{order_date_mon}', $goa_date_mon, $goa_wc_dat_tem);  //replace {order_date_mon}
 $goa_wc_dat_tem = str_replace('{order_date_year}', $goa_date_year, $goa_wc_dat_tem);  //replace {order_date_year}
 
 $goa_wc_dat_tem = str_replace('{order_time}', $goa_time, $goa_wc_dat_tem);  //replace {order_time}
 $goa_wc_dat_tem = str_replace('{wc_website}', $goa_domain, $goa_wc_dat_tem);  //replace {wc_website}
 $goa_wc_dat_tem = str_replace('{hash_gaqr}', $goa_hash, $goa_wc_dat_tem);  //replace {hash_gaqr}
 
 
/* 091122*/
$wc_order_items_template1 = '';
/*29122022 add acf support*/
    //if( class_exists('ACF') ){
        $count_acf = substr_count($goa_wc_dat_tem,'wc_prod_acf_');
    //}

if (!function_exists('get_string_between')) {

            function get_string_between($string, $start, $end, $pos){
                $string = ' ' . $string;
                $ini = strpos($string, $start,$pos);

                if ($ini == 0) return '';
                $ini += strlen($start);
                $len = strpos($string, $end, $ini) - $ini;
                
                return substr($string, $ini, $len);
            }
}

foreach ($goa_order_items_name as $keyn => $nms){

        if( class_exists('ACF') ){

            if ($count_acf == 1){
                $parsed = get_string_between($goa_wc_dat_tem, '{wc_prod_acf_', '}', 0);
                $fullparsed = 'wc_prod_acf_'.$parsed;
                $acffield_data = get_field( $parsed, $goa_order_items_id[$keyn] );
                $goa_wc_dat_tem = str_replace('{'.$fullparsed.'}', $acffield_data, $goa_wc_dat_tem);  //replace 1 {wc_prod_acf_-....}
            }

            if ($count_acf > 1){
                
                $html = $goa_wc_dat_tem;
                $needle = "{wc_prod_acf_";
                $lastPos = 0;
                $positions = array();

                while (($lastPos = strpos($html, $needle, $lastPos))!== false) {
                    $positions[] = $lastPos;
                    $lastPos = $lastPos + strlen($needle);
                }
                
                //rsort($positions);
                $fullparsedm = array();
                $acffield_datam =  array();
                
                foreach ($positions as $value) {
                    
                    $parsed = get_string_between($goa_wc_dat_tem, '{wc_prod_acf_', '}', $value);
  //                  $parsed = preg_replace('/[[:^print:]]/', '', $parsed);

                    
                    $fullparsedm[] = '/{wc_prod_acf_'.$parsed.'}/';
                    $acffield_datam[] =  get_field( $parsed, $goa_order_items_id[$keyn] );
                    
                    //echo $value ."<br />";
                }      
                //print_r(json_encode($goa_wc_dat_tem));

                $goa_wc_dat_tem = preg_replace($fullparsedm, $acffield_datam, $goa_wc_dat_tem);  //replace 1 {wc_prod_acf_-....}
               // $goa_wc_dat_tem = str_replace("\r\n", " \r\n ", $goa_wc_dat_tem);
               // $goa_wc_dat_tem = preg_replace('/[[:^print:]]/', '', $goa_wc_dat_tem);

                //print_r(json_encode($goa_wc_dat_tem));

            }




        } else {
            //cleaning all ACF variables since acf not enabled
            if ($count_acf >= 1){
                
                $html = $goa_wc_dat_tem;
                $needle = "{wc_prod_acf_";
                $lastPos = 0;
                $positions = array();

                while (($lastPos = strpos($html, $needle, $lastPos))!== false) {
                    $positions[] = $lastPos;
                    $lastPos = $lastPos + strlen($needle);
                }
                

                $fullparsedm = array();
                $acffield_datam =  array();
                
                foreach ($positions as $value) {
                    
                    $parsed = get_string_between($goa_wc_dat_tem, '{wc_prod_acf_', '}', $value);

                    $fullparsedm[] = '/{wc_prod_acf_'.$parsed.'}/';
                    $acffield_datam[] =  '';
                    
                }      

                $goa_wc_dat_tem = preg_replace($fullparsedm, $acffield_datam, $goa_wc_dat_tem);  //replace 1 {wc_prod_acf_-....}
                //print_r( json_encode(preg_match('/[\w]+/',$goa_wc_dat_tem)));
            }   
            if (preg_match('/[\w]+/',$goa_wc_dat_tem) == 0 && mb_strlen($goa_wc_dat_tem)>0 ){
                $goa_wc_dat_tem = preg_replace('/[[:^print:]]/', '', $goa_wc_dat_tem);    
//                print_r(json_encode($goa_wc_dat_tem));
            }
        }

$goa_wc_dat_tem = str_replace('{wc_order_items_name'.($keyn+1).'}', $nms, $goa_wc_dat_tem);  //replace {wc_order_items_name1-....}
$goa_wc_dat_tem = str_replace('{wc_order_items_qty'.($keyn+1).'}', $goa_order_items_qty[$keyn], $goa_wc_dat_tem);  //replace {wc_order_items_qty1-....}
$goa_wc_dat_tem = str_replace('{wc_order_items_price'.($keyn+1).'}', $goa_order_items_price[$keyn], $goa_wc_dat_tem);  //replace {wc_order_items_price1-....}    
$goa_wc_dat_tem = str_replace('{wc_order_items_subtot'.($keyn+1).'}', $goa_order_items_subtot[$keyn], $goa_wc_dat_tem);  //replace {wc_order_items_subtot1-....}

$wc_order_items_template1 .= $nms ." ( ". $goa_order_items_qty[$keyn]." X ".html_entity_decode(strip_tags(wc_price($goa_order_items_price[$keyn]))). " = ".html_entity_decode(strip_tags(wc_price($goa_order_items_subtot[$keyn])))." )"."\n";

}

//clean empty products fix 12.12.2022

$cnt_ey_it_nms = substr_count($goa_wc_dat_tem,'wc_order_items_name');
$cnt_ey_it_qty = substr_count($goa_wc_dat_tem,'wc_order_items_qty');
$cnt_ey_it_prc = substr_count($goa_wc_dat_tem,'wc_order_items_price');
$cnt_ey_it_subt = substr_count($goa_wc_dat_tem,'wc_order_items_subtot');



if ($cnt_ey_it_nms > 0 || $cnt_ey_it_qty > 0 || $cnt_ey_it_prc > 0 || $cnt_ey_it_subt > 0){
for ($keyn = 1; $keyn <= 99999; $keyn++) {

if (substr_count($goa_wc_dat_tem,'{wc_order_items_name'.($keyn+1).'}')){
$goa_wc_dat_tem = str_replace('{wc_order_items_name'.($keyn+1).'}', '', $goa_wc_dat_tem);  //empty {wc_order_items_name1-....}
}
if (substr_count($goa_wc_dat_tem,'{wc_order_items_qty'.($keyn+1).'}')){
$goa_wc_dat_tem = str_replace('{wc_order_items_qty'.($keyn+1).'}', '', $goa_wc_dat_tem);  //empty {wc_order_items_qty1-....}
}
if (substr_count($goa_wc_dat_tem,'{wc_order_items_price'.($keyn+1).'}')){
$goa_wc_dat_tem = str_replace('{wc_order_items_price'.($keyn+1).'}', '', $goa_wc_dat_tem);  //empty {wc_order_items_price1-....}    
}
if (substr_count($goa_wc_dat_tem,'{wc_order_items_subtot'.($keyn+1).'}')){
$goa_wc_dat_tem = str_replace('{wc_order_items_subtot'.($keyn+1).'}', '', $goa_wc_dat_tem);  //empty {wc_order_items_subtot1-....}
}
}
}

//fix
//print_r($wc_order_items_template1);
$goa_wc_dat_tem = str_replace('{wc_order_items_template1}', $wc_order_items_template1, $goa_wc_dat_tem);  //replace {wc_order_items_subtot1-....}    


        $content = $goa_wc_dat_tem/*."\n"*/;
        
    } else {
    $goa_vat_auto = (strpos($goa_vat, 'Enter you VAT') === false) ?  "VAT NUMBER ".$goa_vat."\n" : '';
    $content = $goa_customer."\n"."TOTAL AMOUNT ".$goa_total."\n".$goa_tax_total.$goa_vat_auto."DATE ".$goa_date."\n"."TIME ".$goa_time."\n".$goa_domain."\n";
    }
//$options['link'] = false;
} else{
    //return false;
}

    }    
    
    //goasle.com 01 11 2021
    //check tooltip or not
    }
    //
        // call QR Code creation subfunction
        return $this->createTag($content, $options, $atts);
    }

    /**
    * Update a list value if exists in allowedEntries, subfunction for AdminSetting
    * 
    * @param  string  $updateValue      value that needs to be updated in the options
    * @param  array   $allowedEntries   allowed values for updateValue
    * @param  string  &$options         reference to updated option
   */
    public function updateValueList($updateValue, $allowedEntries, &$options) 
    {
        // update only if value is allowed
        if (in_array($updateValue, $allowedEntries)) {
            $options = $updateValue;        
        }
    }

    /**
    * Update an integer value if exists in specified bounds, subfunction for AdminSetting
    * 
    * @param  string  $updateValue   value that needs to be updated in the options
    * @param  integer $min           minimum valid value
    * @param  integer $max           maximum valid value
    * @param  string  &$options      reference to updated option
    */
    public function updateValueInteger($updateValue, $min, $max, &$options)
    {
        // update only if value is numeric and inbetween bounds
        if ((is_numeric($updateValue)) && ($updateValue >= $min) && ($updateValue <= $max)) {
            $options = $updateValue;        
        }
    }

    /**
    * Update a string value if exists in specified bounds, subfunction for AdminSetting
    * 
    * @param  string   $updateValue  value that needs to be updated in the options
    * @param  string   &$options     reference to updated option
    * @param  integer  $maxLength    (optional) maximum valid value (defaults to 1024)
    * @param  boolean  $allowEmpty   (optional) empty value allowed (defaults to TRUE)
    */
    public function updateValueString($updateValue, &$options, $maxLength = 1024, $allowEmpty = TRUE, $varia ='')
    {

//$options = $updateValue;
//filter_input(INPUT_POST,$varia, FILTER_SANITIZE_STRING);
       //$options = htmlspecialchars(stripslashes(str_replace(array('>','<'),array('',''),$updateValue)));
    }
  
    /**
    * WordPress Admin configuration page for the plugin call 
    */
    public function adminSettingsPage()
    {
        // load options
        $options = get_option($this->pluginOptions);
    
        if (isset($_POST['update_options'])) // save changes 
        {
            // global, code generation
            $this->updateValueList($_POST['qrct_from_goaskle_com_generator'], array('google','lib'), $options['global']['generator']); 

            // image type
            $this->updateValueList($_POST['qrct_from_goaskle_com_imagetype'], array('gif','png','jpg'), $options['global']['ext']); 
      
      //goaskle.com 01 11 2021 add vat number field
      //$this->updateValueString($_POST['qrct_from_goaskle_com_sc_vat_number'],$options['shortcode']['vat_number'],'','','qrct_from_goaskle_com_sc_vat_number');
      $options['shortcode']['vat_number'] = sanitize_text_field($_POST['qrct_from_goaskle_com_sc_vat_number']);

      $options['shortcode']['main_color'] = sanitize_hex_color($_POST['qrct_from_goaskle_com_sc_main_color']);      
      $options['shortcode']['dots_color'] = sanitize_hex_color($_POST['qrct_from_goaskle_com_sc_dots_color']);            
      
      //250822 goaskle.com adding manual template setting and disable auto checkbox
      $options['shortcode']['wc_data_template'] = sanitize_textarea_field($_POST['qrct_from_goaskle_com_sc_wc_data_template']);
      //disable auto
      $options['shortcode']['disable_auto_wc'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_disable_auto_wc']))) ? 1 : 0;
      

      //170223 new checkbox for adding hash into every wc order
      $options['shortcode']['hash_order_wc'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_enable_hash_order_wc']))) ? 1 : 0;

      //170223 new checkbox for disable pages and keep emails only for wc 
      $options['shortcode']['disable_pages_auto_wc'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_disable_for_pages_auto_wc']))) ? 1 : 0;
      

      $options['shortcode']['disable_all_wc_emails_except_standard'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_disable_all_wc_emails_except_standard']))) ? 1 : 0;
      /*120123 adding new options enable emails */
      $options['shortcode']['qrct_from_goaskle_com_sc_new_order_wc_emails_enable'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_new_order_wc_emails_enable']))) ? 1 : 0;
      $options['shortcode']['qrct_from_goaskle_com_sc_failed_order_wc_emails_enable'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_failed_order_wc_emails_enable']))) ? 1 : 0;
      $options['shortcode']['qrct_from_goaskle_com_sc_customer_on_hold_order_wc_emails_enable'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_customer_on_hold_order_wc_emails_enable']))) ? 1 : 0;
      $options['shortcode']['qrct_from_goaskle_com_sc_customer_processing_order_wc_emails_enable'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_customer_processing_order_wc_emails_enable']))) ? 1 : 0;
      $options['shortcode']['qrct_from_goaskle_com_sc_customer_completed_order_wc_emails_enable'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_customer_completed_order_wc_emails_enable']))) ? 1 : 0;
      $options['shortcode']['qrct_from_goaskle_com_sc_customer_refunded_order_wc_emails_enable'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_customer_refunded_order_wc_emails_enable']))) ? 1 : 0;
      $options['shortcode']['qrct_from_goaskle_com_sc_customer_partially_refunded_order_wc_emails_enable'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_customer_partially_refunded_order_wc_emails_enable']))) ? 1 : 0;
      $options['shortcode']['qrct_from_goaskle_com_sc_cancelled_order_wc_emails_enable'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_cancelled_order_wc_emails_enable']))) ? 1 : 0;
      $options['shortcode']['qrct_from_goaskle_com_sc_customer_invoice_wc_emails_enable'] = (sanitize_text_field(isset($_POST['qrct_from_goaskle_com_sc_customer_invoice_wc_emails_enable']))) ? 1 : 0;
      
      
      //htmlspecialchars(stripslashes(str_replace(array('>','<'),array('',''),$_POST['qrct_from_goaskle_com_sc_vat_number'])));
      
            // default options, size
            $this->updateValueInteger($_POST['qrct_from_goaskle_com_sc_size'], 0, 1400, $options['shortcode']['size']); 
            $this->updateValueInteger($_POST['qrct_from_goaskle_com_tt_size'], 0, 1400, $options['tooltip']['size']); 
            $this->updateValueInteger($_POST['qrct_from_goaskle_com_wg_size'], 0, 1400, $options['widget']['size']); 
      
            // encoding
            $this->updateValueList($_POST['qrct_from_goaskle_com_sc_enc'], array('UTF-8','Shift_JIS','ISO-8859-1'), $options['shortcode']['enc']); 
            $this->updateValueList($_POST['qrct_from_goaskle_com_tt_enc'], array('UTF-8','Shift_JIS','ISO-8859-1'), $options['tooltip']['enc']); 
            $this->updateValueList($_POST['qrct_from_goaskle_com_wg_enc'], array('UTF-8','Shift_JIS','ISO-8859-1'), $options['widget']['enc']); 

            // error corrtion
            $this->updateValueList($_POST['qrct_from_goaskle_com_sc_ecc'], array('L','M','Q','H'), $options['shortcode']['ecc']); 
            $this->updateValueList($_POST['qrct_from_goaskle_com_tt_ecc'], array('L','M','Q','H'), $options['tooltip']['ecc']); 
            $this->updateValueList($_POST['qrct_from_goaskle_com_wg_ecc'], array('L','M','Q','H'), $options['widget']['ecc']); 
      
            // version
            $this->updateValueInteger($_POST['qrct_from_goaskle_com_sc_ver'], 0, 40, $options['shortcode']['version']); 
            $this->updateValueInteger($_POST['qrct_from_goaskle_com_tt_ver'], 0, 40, $options['tooltip']['version']); 
            $this->updateValueInteger($_POST['qrct_from_goaskle_com_wg_ver'], 0, 40, $options['widget']['version']); 
      
            // margin
            $this->updateValueInteger($_POST['qrct_from_goaskle_com_sc_margin'], 0, 10, $options['shortcode']['margin']); 
            $this->updateValueInteger($_POST['qrct_from_goaskle_com_tt_margin'], 0, 10, $options['tooltip']['margin']); 
            $this->updateValueInteger($_POST['qrct_from_goaskle_com_wg_margin'], 0, 10, $options['widget']['margin']);

            // imageparam
            $options['shortcode']['imageparam'] = sanitize_text_field($_POST['qrct_from_goaskle_com_sc_imageparam']);
            $options['widget']['imageparam'] = sanitize_text_field($_POST['qrct_from_goaskle_com_wg_imageparam']);
            //$options['shortcode']['imageparam'] = filter_input(INPUT_POST,'qrct_from_goaskle_com_sc_imageparam', FILTER_SANITIZE_STRING);
            //$options['widget']['imageparam'] = filter_input(INPUT_POST,'qrct_from_goaskle_com_wg_imageparam', FILTER_SANITIZE_STRING);
            
            //$this->updateValueString($_POST['qrct_from_goaskle_com_sc_imageparam'],$options['shortcode']['imageparam']);
            //$this->updateValueString($_POST['qrct_from_goaskle_com_wg_imageparam'],$options['widget']['imageparam']);
      
            // link
            $options['shortcode']['link'] = sanitize_text_field($_POST['qrct_from_goaskle_com_sc_link']);
            $options['widget']['link'] = sanitize_text_field($_POST['qrct_from_goaskle_com_wg_link']);
            $options['widget']['link'] = filter_input(INPUT_POST,'qrct_from_goaskle_com_wg_link', FILTER_SANITIZE_STRING);
            
            //$this->updateValueString($_POST['qrct_from_goaskle_com_sc_link'],$options['shortcode']['link']);
            //$this->updateValueString($_POST['qrct_from_goaskle_com_wg_link'],$options['widget']['link']);
      
            // atagparam
            $options['shortcode']['atagparam'] = sanitize_text_field($_POST['qrct_from_goaskle_com_sc_atagparam']);
            $options['widget']['atagparam'] = sanitize_text_field($_POST['qrct_from_goaskle_com_wg_atagparam']);
            //$options['widget']['atagparam'] = filter_input(INPUT_POST,'qrct_from_goaskle_com_wg_atagparam', FILTER_SANITIZE_STRING);
            
            //$this->updateValueString($_POST['qrct_from_goaskle_com_sc_atagparam'],$options['shortcode']['atagparam']);
            //$this->updateValueString($_POST['qrct_from_goaskle_com_wg_atagparam'],$options['widget']['atagparam']);

            // update options in the WordPress options database
            update_option($this->pluginOptions, $options);
      
            // write out header message
            echo '<div id="message" class="updated fade"><p><strong>' . __('Options saved.', 'qr-code-tag-for-wc-from-goaskle-com') . '</strong></p></div>';

            require_once(dirname(__FILE__).'/Qrcode.php');
            $qrcode = new Qrcode_from_Goaskle_Com();
            $qrcode->clearCache();
  
        } elseif (isset($_POST['reset_options'])) {  // if Reset options pressed

            // write default options to WordPress options database
            update_option($this->pluginOptions, $this->defaultOptions);

            // and reload it afterwards
            $options = get_option($this->pluginOptions);

            // write out header message
            echo '<div id="message" class="updated fade"><p><strong>' . __('Default options loaded.', 'qr-code-tag-for-wc-from-goaskle-com') . '</strong></p></div>';

        } elseif (isset($_POST['clear_cache'])) { // if Clear Cache button pressed

            // create dummy QR Code object
            require_once(dirname(__FILE__).'/Qrcode.php');
            $qrcode = new Qrcode_from_Goaskle_Com();
            $qrcode->clearCache();

            // write out header message
            echo '<div id="message" class="updated fade"><p><strong>' . __('Cache cleared.', 'qr-code-tag-for-wc-from-goaskle-com') . '</strong></p></div>';
        }
    
        // include default settings page
        require_once(dirname(__FILE__).'/QrctWp-admin.inc.php');
    }
  
}

if (!class_exists('GoAskle_dynamic_page')){
    /**
    * GoAskle_dynamic_page
    * Usage: 
    *   $args = array(
    *       'slug' => 'any_slug',
    *       'post_title' => 'Any Page Title',
    *       'post content' => 'This is the any page content'
    *   );
    *   new GoAskle_dynamic_page($args);
    */
    class GoAskle_dynamic_page
    {

        public $slug ='';
        public $args = array();
        /**
         * __construct
         * @param array $arg post to create on the fly
         * 
         */
        function __construct($args){
            add_filter('the_posts',array($this,'fly_page'));
            $this->args = $args;
            $this->slug = $args['slug'];
        }

        /**
         * any_page 
         * @param  array $posts 
         * @return array 
         */
        public function fly_page($posts){
            global $wp,$wp_query;
            $page_slug = $this->slug;

            //check if user is requesting our fake page
            if(count($posts) == 0 && (strtolower($wp->request) == $page_slug || (isset($wp->query_vars['page_id']) && $wp->query_vars['page_id'] == $page_slug))){

                //create a fake post
                $post = new stdClass;
                $post->post_author = 1;
                $post->post_name = $page_slug;
                $post->guid = get_bloginfo('wpurl' . '/' . $page_slug);
                $post->post_title = 'page title';
                //put your custom content here
                $post->post_content = "Any Content";
                //just needs to be a number - negatives are fine
                $post->ID = -42;
                $post->post_status = 'static';
                $post->comment_status = 'closed';
                $post->ping_status = 'closed';
                $post->comment_count = 0;
                //dates may need to be overwritten if you have a "recent posts" widget or similar - set to whatever you want
                $post->post_date = current_time('mysql');
                $post->post_date_gmt = current_time('mysql',1);

                $post = (object) array_merge((array) $post, (array) $this->args);
                $posts = NULL;
                $posts[] = $post;

                $wp_query->is_page = true;
                $wp_query->is_singular = true;
                $wp_query->is_home = false;
                $wp_query->is_archive = false;
                $wp_query->is_category = false;
                unset($wp_query->query["error"]);
                $wp_query->query_vars["error"]="";
                $wp_query->is_404 = false;
            }

            return $posts;
        }
    }//end class
}//end if