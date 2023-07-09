<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
include_once WF_PKLIST_PLUGIN_PATH.'/admin/views/premium_extension_listing.php';
?>
<style>
    .wt_pro_addon_col_1{float: left;width: 20%;border: 1px solid #ececec;}
    .wt_pro_addon_col_1 ul{margin:0;}
    .wt_pro_addon_col_1 ul li.pro_list_tab{padding: 1em;border-bottom: 1px solid #ECECEC;}
    .wt_pro_addon_cat_name{display: flex;align-items: center;width: 100%;}
    .wt_pro_addon_cat_name img{width: 7%;height: fit-content;}
    .wt_pro_addon_cat_name p{font-weight: 500;font-size: 14px;margin-left: 1em;width: 90%;}
    .wt_pro_addon_col_1 li:last-child{border-bottom: none;}
    .wt_pro_addon_col_2{width: 78%;float: left;}
    .wt_pro_addon_tile_pro_ad {border: 1.3px solid #E8E8E8;background: #FFFFFF;border-radius: 7px;display: flex;flex-direction: column;min-width: 0;margin: 0px 10px 20px 10px;min-height: 404px;word-wrap: break-word;width: 30%;float: left;position: relative;}
    .wt_pro_addon_widget_pro_ad{margin-bottom: 5em;}
    .wt_pro_addon_widget_pro_ad .wt_pro_addon_widget_wrapper_pro_ad {display: flex;padding: 0em 2em;background: #F0F8FF;}
    .wt_pro_addon_widget_column_pro_ad_1 {padding-top: 18px;}
    .wt_pro_addon_widget_column_pro_ad_1 img {width: 60px;height: 60px;}
    .wt_pro_addon_widget_column_pro_ad_2 {font-size: 15px;padding: 0 10px;width: 100%;height: 100px;}
    .wt_pro_addon_features_list_pro_ad li {font-style: normal;font-weight: 500;font-size: 13px;line-height: 17px;color: #001A69;list-style: none;position: relative;padding-left: 49px;margin: 0 0 15px 0;display: flex;align-items: center;}
    .wt_pro_addon_features_list li:before{content: '';position: absolute;height: 15px;width: 15px;background-image: url(<?php echo esc_url($wf_admin_img_path.'/tick.svg'); ?>);background-size: contain;background-repeat: no-repeat;background-position: center;left: 15px;} 
    .wt_pro_show_more_less_pro_ad {text-align: center;left: 33%;}
    .wt_pro_show_more_less_pro_ad a p {margin: 2px 0 0 5px;display: inline-block;color: #3171FB;}
    .wt_pro_addon_show_less_pro_ad {display: none;}
    .wt_pro_addon_premium_link_div_pro_ad {text-align: center;padding: 1.3em 1em;background: #fff;color: #1DA5F8;margin-top: 1em;position: absolute;right: 0;left: 0;bottom: 0;font-weight: 500;font-size: 15px;border: 1px solid;text-decoration: none;}
    .wt_pro_addon_premium_link_div_pro_ad:hover{background-color: #1DA5F8;color: #fff;}
    .wt_pro_addon_row_pro_ad {float: left;width: 100%;margin: 10px 1.5em;}
    .wt_pro_addon_tile_pro_ad_row{float: left;width: 100%;margin-left: 1.7em;}
    .wt_pro_addon_title{font-size: 13px;}
    .wt_pro_ad_tab_show{font-weight: 500;font-size: 14px;color: #007FFF;}
    .wt_pro_addon_gurantee_pro_ad,.wt_pro_addon_support_pro_ad{display: flex;padding: 0.7em 1em;align-items: center;}
    .wt_pro_addon_gurantee_pro_ad_column_1 img,.wt_pro_addon_support_pro_ad_column_1 img{width: 3em;}
    .wt_pro_addon_gurantee_pro_ad_column_2,.wt_pro_addon_support_pro_ad_column_2{padding: 0 1em;}
</style>
<style type="text/css">
    .wf-tab-head{width: 66%;}
    .wt_pro_addon_features_list_pro_ad ul li:nth-child(n + 4){display: none;}
    .wt_pro_addon_features_list_pro_ad li{font-style: normal;font-weight: 500;font-size: 13px;line-height: 20px;color: #001A69;list-style: none;position: relative;padding-left: 49px;padding-right: 20px; margin: 0 0 15px 0;display: flex;align-items: center;}
    .wt_pro_addon_features_list_pro_ad li:before{content: '';position: absolute;height: 15px;width: 15px;background-image: url(<?php echo esc_url($wf_admin_img_path.'/tick.svg'); ?>);background-size: contain;background-repeat: no-repeat;background-position: center;left: 15px;}
    .wt_pro_ad_tab_hide .wt_pro_addon_cat_name img{filter: grayscale(1);}
</style>
<div class="wt_wrap">
    <?php
    $pro_ad_head = 0;
    foreach($premium_ext_lists as $key => $key_val){
        $dv_dis_head = "";
        if(0 !== $pro_ad_head){
            $dv_dis_head = "display:none;";
        }
    ?>
    <div class="wt_heading_section wt_heading_section_pro_ad <?php echo esc_attr($key).'_pro_ad_list_div_head'; ?>" style="<?php echo esc_attr($dv_dis_head); ?>">
        <h2 class="wp-heading-inline">
        <?php echo esc_html($key_val['domain_name']); ?>
        </h2>
        <?php
            //webtoffee branding
            include WF_PKLIST_PLUGIN_PATH.'/admin/views/admin-settings-branding.php';
        ?>
        <p style="margin-left: 1em !important;"><?php echo esc_html($key_val['domain_caption']); ?></p>
    </div>
    <?php
    $pro_ad_head++;
    }
    ?>
    <div class="wt_pro_addon_container">
        <div class="wt_pro_addon_row_pro_ad">
            <div class="wt_pro_addon_col_1">
                <ul>
                    <?php
                        $t = 0;
                        $plugins_there = false;
                        foreach($premium_ext_lists as $key => $key_val){
                            $plugins_list_tab = $key_val['plugins'];
                            $not_activated_plugin_tab = array();
                            foreach($plugins_list_tab as $pl => $pl_attr){
                                if(isset($pl_attr['file_path']) && !is_plugin_active($pl_attr['file_path'])){
                                    $plugins_there = true;
                                    $not_activated_plugin_tab[$pl] = $pl_attr;
                                }
                            }
                            if(!empty($not_activated_plugin_tab)){
                            ?>
                    <li class="pro_list_tab <?php echo (0 === $t) ? 'wt_pro_ad_tab_show' : 'wt_pro_ad_tab_hide'; ?>" data-tab-target="<?php echo esc_attr($key).'_pro_ad_list_div'; ?>">
                        <div class="wt_pro_addon_cat_name">
                            <img src="<?php echo esc_url($key_val['domain_logo']); ?>">
                            <p><?php echo esc_html($key_val['domain_name']);   ?></p>
                        </div>
                    </li>
                            <?php                      
                            $t++;
                            }
                        }
                    if($plugins_there){
                    ?>
                    <li>
                        <div class="wt_pro_addon_gurantee_pro_ad">
                            <div class="wt_pro_addon_gurantee_pro_ad_column_1">
                                <img src="<?php echo esc_url($wf_admin_img_path).'/gurantee_doc.png'; ?>">
                            </div>
                            <div class="wt_pro_addon_gurantee_pro_ad_column_2">
                                <p><?php echo __("You are covered by our 30-Day Money Back Guarantee","print-invoices-packing-slip-labels-for-woocommerce"); ?></p>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="wt_pro_addon_support_pro_ad">
                            <div class="wt_pro_addon_support_pro_ad_column_1">
                                <img src="<?php echo esc_url($wf_admin_img_path).'/cs_support_doc.png'; ?>">
                            </div>
                            <div class="wt_pro_addon_support_pro_ad_column_2">
                                <p><?php _e("Supported by a team with 99% Customer Satisfaction Score","print-invoices-packing-slip-labels-for-woocommerce"); ?></p>
                            </div>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="wt_pro_addon_col_2">
                <?php
                $tl = 0;
                foreach($premium_ext_lists as $pl_key => $key){
                    $dv_dis = "";
                    if(0 !== $tl){
                        $dv_dis = "display:none;";
                    }
                    $plugins_list = $key['plugins'];
                    $not_activated_plugin = array();
                    foreach($plugins_list as $pl => $pl_attr){
                        if(isset($pl_attr['file_path']) && !is_plugin_active($pl_attr['file_path'])){
                            $not_activated_plugin[$pl] = $pl_attr;
                        }
                    }
                    $pro_plugin_list_arr = array_chunk($not_activated_plugin,3,true);
                    echo '<div class="'.esc_attr($pl_key).'_pro_ad_list_div pro_list_div" style="float:left;width:100%;'.$dv_dis.'">';
                    foreach($pro_plugin_list_arr as $this_plugin_list){
                    echo '<div class="wt_pro_addon_tile_pro_ad_row">';
                        foreach($this_plugin_list as $this_pro_plugin){
                        ?>
                    <div class="wt_pro_addon_tile_pro_ad">
                        <div class="wt_pro_addon_widget_pro_ad">
                            <div class="wt_pro_addon_widget_wrapper_pro_ad">
                                <div class="wt_pro_addon_widget_column_pro_ad_1">
                                    <img src="<?php echo esc_url($this_pro_plugin['logo']); ?>">
                                </div>
                                <div class="wt_pro_addon_widget_column_pro_ad_2">
                                    <h4 class="wt_pro_addon_title"><?php echo esc_html($this_pro_plugin['title']); ?></h4>
                                </div>
                            </div>
                            <div class="wt_pro_addon_features_list_pro_ad">
                                <ul>
                                    <?php
                                        foreach($this_pro_plugin['features_list'] as $p_feature){
                                            ?>
                                            <li><?php echo esc_html($p_feature); ?></li>
                                            <?php
                                        }
                                    ?>
                                </ul>
                            </div>
                            <div class="wt_pro_show_more_less_pro_ad">
                                <a class="wt_pro_addon_show_more_pro_ad"><p><? echo __("Show More","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
                                <a class="wt_pro_addon_show_less_pro_ad"><p><? echo __("Show Less","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
                            </div>
                        </div>
                        <a class="wt_pro_addon_premium_link_div_pro_ad" href="<?php echo esc_url($this_pro_plugin['page_link']); ?>" target="_blank">
                            <?php _e("Checkout Premium","print-invoices-packing-slip-labels-for-woocommerce"); ?>
                        </a>
                    </div>
                        <?php
                         }
                         echo '</div>';
                    }
                    echo '</div>';
                    $tl++;
                }
                ?>
            </div>
        </div>
    </div>
</div>