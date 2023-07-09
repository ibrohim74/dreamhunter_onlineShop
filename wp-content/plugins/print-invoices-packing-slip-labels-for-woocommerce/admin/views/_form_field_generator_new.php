<?php
if (!defined('ABSPATH')) {
	exit;
}
Class WT_Form_Field_Builder{

	public function generate_form_fields($settings,$base_id=""){
		$html = "";
		if(!empty($settings)){
			$h_no = 1;
			foreach($settings as $this_setting){
				if(isset($this_setting['type'])){
					$row_full_length = array('wt_hr_line','wt_sub_head','wt_plaintext');
					if(in_array($this_setting['type'],$row_full_length)){
						$html .= $this->{$this_setting["type"]}($this_setting,$base_id);
					}else{
						extract($this->verify_the_fields($this_setting));
						if(trim($tr_id) != ""){
							$tr_id = 'id="'.$tr_id.'"';
						}else{
							$tr_id = "";
						}
						$html .= '<tr valign="top" '.$tr_id.' '.$form_toggler_child.'>'.$this->display_label($this_setting,$base_id).$this->{$this_setting["type"]}($this_setting,$base_id).'
				                </tr>';
					}
				}
			}
		}
		echo $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display the label of the setting field
	 */
	public function display_label($args,$base_id){
		extract($this->verify_the_fields($args));
		$html ="";
		$label_style = "";
		if($tooltip){
        	$html = Wf_Woocommerce_Packing_List_Admin::set_tooltip($name,$base_id);
        }

        if(is_array($label)){
        	$label_style = $label["style"];
        	$label = $label["text"];
        }
        $mandatory_star = ($mandatory ? '<span class="wt_pklist_required_field">*</span>' : '');
        return sprintf('<th scope="row"><label for="" style="%1$s">%2$s</label></th>',esc_attr($label_style),esc_html($label).$mandatory_star.$html);
	}

	/**
	 * @since 4.0.0
	 * Function to display toggle checkbox
	 */
	public function wt_toggle_checkbox($args,$base_id=""){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;
    	$checkbox_label = "";
		if(is_array($checkbox_fields)){
			if(isset($checkbox_fields[$value])){
				$checkbox_label = $checkbox_fields[$value];
			}
		}
    	$html = sprintf('<td>
    			<div class="wf_pklist_dashboard_checkbox">
                	<input type="checkbox" class="wf_slide_switch %1$s" id="%2$s" name="%3$s" value="%4$s" %5$s> %6$s
            	</div>',
            	esc_attr($class),
            	esc_attr($id),
            	esc_attr($name),
            	$value,
            	checked($result,$value,false),
            	esc_html($checkbox_label));
    	$html .=sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display the horziontal dotted line
	 */
	public function wt_hr_line($args,$base_id){
		extract($this->verify_the_fields($args));
		return sprintf('<tr><td colspan="3" style="border-bottom: dashed 1px #ccc;" class="%1$s"></td></tr>',esc_attr($class));
	}

	/**
	 * @since 4.0.0
	 * Function to display the horziontal dotted line
	 */
	/*public function wt_plaintext($args,$base_id){
		extract($this->verify_the_fields($args));
		return sprintf('<tr><td>%1$s</td><td>%2$s</td><td></td></tr>',wp_kses_post($label),wp_kses_post($value));
	}*/

	/**
	 * @since 4.0.0
	 * Function to display sub headings for the fields
	 */
	public function wt_sub_head($args,$base_id){
		extract($this->verify_the_fields($args));
		if(trim($heading_number) != ""){
			$heading_number = sprintf('<span style="background: #3157A6;color: #fff;border-radius: 25px;padding: 4px 9px;margin-right: 5px;">%1$s</span>',$heading_number);
		}
		if($col_3 !== ""){
			return sprintf('<tr><td style=""><div class="%1$s">%2$s %3$s</div></td><td></td><td>%4$s</td></tr>',esc_attr($class),$heading_number,wp_kses_post($label),$col_3);
		}
		return sprintf('<tr><td colspan="3" style=""><div class="%1$s">%2$s %3$s</div></td></tr>',esc_attr($class),$heading_number,wp_kses_post($label));
	}

	/**
	 * @since 4.0.0
	 * Function to display multi selected order status field
	 */
	public function order_multi_select_new($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = $result ? $result : array();

		$html = sprintf('<td>
			<input type="hidden" name="%1$s" value="1"/>
			<select class="wc-enhanced-select" id="%2$s" data-placeholder="%3$s" name="%4$s" multiple="multiple">',$name.'_hidden',
			esc_attr($id),
			esc_attr($placeholder),
			$name.'[]');
		foreach($checkbox_fields as $val_key => $val_label){
			$selected = in_array($val_key, $result) ? 'selected="selected"' : "";
			$html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
				esc_attr($val_key),
				$selected,
				wp_kses_post($val_label));
		}
		$html .= sprintf('</select>%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display single checkbox field
     */
	public function wt_single_checkbox($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = is_string($result) ? stripslashes($result) : $result;
		$checkbox_label = "";
		if(is_array($checkbox_fields)){
			if(isset($checkbox_fields[$value])){
				$checkbox_label = $checkbox_fields[$value];
			}
		}

		$html = sprintf('<td><input type="checkbox" name="%5$s" value="%1$s" id="%2$s" class="%3$s %6$s" %7$s %4$s> %8$s',
			$value,
			esc_attr($id),
			esc_attr($class),
			checked($result,$value,false),
			$name,
			$form_toggler_p_class,
			$form_toggler_register,
			esc_html($checkbox_label));
		$html .= sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display multi checkbox field
	 */
	public function wt_multi_checkbox($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = is_array($result) ? $result : array();
		$html = "<td>";
		foreach($checkbox_fields as $checkbox_key => $checkbox_label){
			$checked = in_array($checkbox_key, $result) ? 'checked' : "";
			$html .= sprintf('<input type="checkbox" name="%1$s" id="%2$s" class="%3$s" value="%4$s" %5$s> %6$s',
				$name.'[]',
				$name.'_'.$checkbox_key,
				esc_attr($class),
				esc_attr($checkbox_key),
				$checked,$checkbox_label);
			if("vertical_with_label" === $alignment){
				$html .= "<br><br>";
			}
		} 
		$html .= sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display select2 dropdown multi checkbox field
	 */
	public function wt_select2_checkbox($args,$base_id)
	{	
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = is_array($result) ? $result : array();
		$html = "<td>";
		$html .= sprintf('<input type="hidden" name="%1$s_hidden" value="1">',$name);
		$html .= sprintf('<select class="wc-enhanced-select" id="%1$s" data-placeholder="%2$s" name="%3$s[]" multiple="multiple" %4$s>',
				esc_attr($id),
				esc_attr($placeholder),
				esc_attr($name),
				$attr
				);
		foreach($checkbox_fields as $checkbox_key => $checkbox_label){
			$selected = in_array($checkbox_key, $result) ? 'selected' : "";
			$html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
					esc_attr($checkbox_key),
					esc_attr($selected),
					$checkbox_label
					);
		}
		$html .= '</select>';
		$html .= sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;

	}

	/**
	 * @since 4.0.0
	 * Function to display single checkbox field
	 */

	public function wt_radio($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result=is_string($result) ? stripslashes($result) : $result;
		$td_sytle = "";
		$radio_opt_name = $name;
		if($name === "woocommerce_wf_generate_for_taxstatus"){
			$radio_opt_name = "woocommerce_wf_generate_for_taxstatus[]";
			// $td_sytle = "width:55%;";
		}
		$html = sprintf('<td style="%1$s">',$td_sytle);
		// echo $result;
		
		foreach($radio_fields as $radio_key => $radio_label){
			$checked = "";
			if((is_array($result) && in_array($radio_key, $result)) || (is_string($result) && $result==$radio_key))
			{
				$checked='checked';
			}
			if(is_rtl() && "vertical_with_label" !== $alignment){
				$html .='<p style="float:right;display:inline-block;margin:auto;">';
			}else{
				$html .='<p style="float:left;display:inline-block;margin:auto;">';
			}
			
			$html .= sprintf('<input type="radio" name="%1$s" id="%8$s" class="%3$s %4$s" %7$s value="%2$s" %5$s> %6$s',
				$radio_opt_name,
				$radio_key,
				$class,
				$form_toggler_p_class,
				$checked,
				$radio_label,
				$form_toggler_register,
				$name.'_'.$radio_key);
			if("vertical_with_label" === $alignment){
				$html .= "<br><br>";
			}else{
				$html .="&nbsp;&nbsp;";
			}
			$html .='</p>';
		}
		if(trim($end_col_call_back) != ""){
			$col3_data = $this->{$end_col_call_back}($base_id,$module_base);
			$row_span = 'rowspan="3" style="position:relative;"';
		}else{
			$col3_data = "";
			$row_span = '';
		}
		
		$html .= sprintf('%1$s</td><td %3$s>%2$s</td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field),$col3_data,$row_span);
		return $html;
	}

	public function wt_select_dropdown($args,$base_id){
		extract($this->verify_the_fields($args));
		$result=Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;

		$html = sprintf('<td><select name="%1$s" id="%1$s" class="%2$s %3$s" %4$s>',esc_attr($name),$class,$form_toggler_p_class,$form_toggler_register);
		foreach($select_dropdown_fields as $select_key => $select_label){
			$selected = ($select_key === $result) ? 'selected' : "";
			$disabled = "";
			if($select_key === "wfte_select_disabled_option"){
				$disabled = "disabled";
			}
			$html .= sprintf('<option value="%1$s" %2$s %4$s>%3$s</option>',
				esc_attr($select_key),
				$selected,
				$select_label,
				$disabled);		
		} 
		$html .=sprintf('</select>%1$s<td></td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	public function wt_text($args,$base_id){
		extract($this->verify_the_fields($args));
		$result=Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;

    	$html = sprintf('<td><input type="text" name="%5$s" id="%1$s" class="%2$s" value="%3$s" %6$s>%4$s<td><td></td>',esc_attr($id),
    		esc_attr($class),
    		esc_attr($result),
    		$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field),
    		esc_attr($name),
    		esc_attr($attr));
		return $html;
	}

	public function wt_textarea($args,$base_id){
		extract($this->verify_the_fields($args));
		$result=Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;
    	$html = sprintf('<td><textarea name="%1$s" id="%2$s" class="%3$s" placeholder="%5$s">%4$s</textarea>',esc_attr($name),
    		esc_attr($id),
    		esc_attr($class),
    		$result,
    		esc_attr($placeholder));

    	$html .=sprintf('%1$s<td></td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	public function wt_number($args,$base_id){
		extract($this->verify_the_fields($args));
		$result=Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;

    	$html = sprintf('<td><input type="number" name="%5$s" id="%1$s" class="%2$s" value="%3$s" %6$s>%4$s<td><td></td>',
    		esc_attr($id),
    		esc_attr($class),
    		esc_attr($result),
    		$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field),
    		esc_attr($name),
    		$attr);
		return $html;
	}

	public function wt_additional_fields($args,$base_id){
		include WF_PKLIST_PLUGIN_PATH."admin/views/_custom_field_editor_form.php";
		extract($this->verify_the_fields($args));
		$fields=array();

        $add_data_flds=Wf_Woocommerce_Packing_List::$default_additional_data_fields; 
        $user_created=Wf_Woocommerce_Packing_List::get_option('wf_additional_data_fields');		            
        $result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result=is_string($result) ? stripslashes($result) : $result;

        if(is_array($user_created))  //user created
        {
            $fields=array_merge($add_data_flds,$user_created);
        }else
        {
            $fields=$add_data_flds; //default
        }
        
    	$user_selected_arr = $result && is_array($result) ? $result : array();

    	// merge all the vat meta key to vat , label to VAT.
    	$vat_fields = array('vat','vat_number','eu_vat_number');
    	$temp = array();
    	foreach($user_selected_arr as $user_val){
    		if(in_array($user_val,$vat_fields)){
    			if(!in_array('vat',$temp)){
    				$temp[] = 'vat';
    			}
    		}else{
    			$temp[] = $user_val;
    		}
    	}
    	$user_selected_arr = $temp;

    	$d_temp = array();
    	foreach($fields as $d_key => $d_val){
    		if(in_array($d_key,$vat_fields)){
    			if(!array_key_exists('vat',$d_temp)){
    				$d_temp[$d_key] = 'VAT';
    			}
    		}else{
    			$d_temp[$d_key] = $d_val;
    		}
    	}
    	$wt_fields = $d_temp;
    	$html = sprintf('<td>
    		<div class="wf_select_multi">
    		<input type="hidden" name="wf_%1$s_contactno_email_hidden" value="1" />
    		<select class="wc-enhanced-select" name="wf_%1$s_contactno_email[]" multiple="multiple">',$module_base);
				foreach ($wt_fields as $wt_fields_key => $wt_field_name) 
	            { 
	                $meta_key_display=Wf_Woocommerce_Packing_List::get_display_key($wt_fields_key);
	                $selected = in_array($wt_fields_key, $user_selected_arr) ? 'selected' : '';
	                $html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
	                	$wt_fields_key,
	                	$selected,
	                	$wt_field_name.$meta_key_display);
	            }
	            $html .=sprintf('</select>
	            	<br>
	            	<button type="button" class="button button-secondary" data-wf_popover="1" data-title="%1$s" data-module-base="%2$s" data-content-container=".wt_pklist_custom_field_form" data-field-type="order_meta" style="margin-top:5px; margin-left:5px; float: right;">%3$s</button>
	            	</div>%4$s</td>
	            	<td></td>',
	            	__("Order Meta","print-invoices-packing-slip-labels-for-woocommerce"),
	            	esc_attr($module_base),
	            	__("Add/Edit Order Meta Field","print-invoices-packing-slip-labels-for-woocommerce"),
	            	$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	public function wt_uploader($args,$base_id){
		$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images/uploader_sample_img.png';
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result=is_string($result) ? stripslashes($result) : $result;
		// echo "hihashd";
		$img_url = $result ? $result : $wf_admin_img_path;
		$html = sprintf('<td>
							<input id="%1$s" type="hidden" name="%2$s" value="%3$s">
							<div class="wf_file_attacher_dv">
								<div class="wf_file_attacher_inner_dv">
									<span class="dashicons dashicons-dismiss wt_logo_dismiss"></span>
									<img class="wf_image_preview_small" src="%4$s">
								</div>
								<p>%5$s</p>
								<span class="size_rec">%6$s</span>
								<input type="button" name="upload_image" class="wf_button button button-primary wf_file_attacher" wf_file_attacher_target="#%1$s" value="Upload">
							</div>',
			esc_attr($id),
			esc_attr($name),
			esc_url($result),
			esc_url($img_url),
			__("Upload your image","print-invoices-packing-slip-labels-for-woocommerce"),
			__("Recommended size is 150x50px.","print-invoices-packing-slip-labels-for-woocommerce"));
		$html .=sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	public function wt_add_help_text($help_text,$conditional_help_html,$after_form_field){
		$html = "";
		if(trim($after_form_field) != ""){
			$html .= $after_form_field;
		}
		if(trim($help_text) != ""){
			$html .= sprintf('<span class="wf_form_help">%1$s</span>',wp_kses_post($help_text));
		}
		if(trim($conditional_help_html) != ""){
			$html .= $conditional_help_html;
		}
		return $html;
	}

	public function wt_invoice_start_number_text_input($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result=is_string($result) ? stripslashes($result) : $result;

		$current_inv_no = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_Current_Invoice_number',$base_id);
		$html = sprintf('<td>
				<div class="wf-form-group">
					<input type="number" min="1" step="1" readonly style="%4$s" name="%1$s" value="%2$s" class="invoice_preview_assert" id="invoice_start_number">
					<input style="float: right;" id="reset_invoice_button" type="button"  class="button button-primary" value="%3$s"/>
				</div>
				<input type="hidden" class="wf_current_invoice_number" value="%5$s" name="woocommerce_wf_Current_Invoice_number" class="invoice_preview_assert">
			</td><td rowspan="2" id="invoice_number_prev_div"></td>',
			$name,
			$result,
			__("Reset","print-invoices-packing-slip-labels-for-woocommerce"),
			"background:#eee; float:left;width:60%;",
			$current_inv_no);
		return $html;
	}

	public function wt_wc_country_dropdown($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = is_string($result) ? stripslashes($result) : $result;
		
		$result=Wf_Woocommerce_Packing_List::get_option('wf_country');
        if( strstr( $result, ':' ))
        {
			$result = explode( ':', $result );
			$country         = current( $result );
			$state           = end( $result );                                            
		}else 
		{
			$country = $result;
			$state   = '*';
		}

		
		$coutries_list = $this->wt_get_country($country,$state);
		$country_options = sprintf('<option value="%1$s"></option>%2$s',
			__("Select country","print-invoices-packing-slip-labels-for-woocommerce"),
			$coutries_list
		);
		
		$html = sprintf('<td>
				<select name="%1$s" placeholder="%2$s" %3$s>
				%4$s
				</select>
			</td>',
			esc_attr($name),
			esc_attr($placeholder),
			esc_attr($attr),
			$country_options);
		return $html;
	}

	public function invoice_number_preview($base_id,$template_type){
		ob_start();
		include WF_PKLIST_PLUGIN_PATH.'public/modules/invoice/views/invoice_number_preview.php';
		$html=ob_get_clean();
		return $html;
	}

	public function wt_get_country($country,$state){
		ob_start();
		WC()->countries->country_dropdown_options($country,$state);
		$html=ob_get_clean();
		return $html;
	}
	/**
	 * @since 4.0.0
	 * Function to verify the arguments before displaying the fields
	 */
	public function verify_the_fields($args){
		$args['id'] 	= isset($args['id']) ? $args['id'] : "";
		$args['name'] 	= isset($args['name']) ? $args['name'] : "";
		$args['class'] 	= isset($args['class']) ? $args['class'] : "";
		$args['label'] 	= isset($args['label']) ? $args['label'] : "";
		$args['col'] 	= isset($args['col']) ? (int)$args['col'] : 3;
		$args['col_3']	= isset($args['col_3']) ? $args['col_3'] : "";

		$args['value'] 	= isset($args['value']) ? $args['value'] : '';
		$args['attr']	=	(isset($args['attr']) ? $args['attr'] : '');
		$args['tooltip']	=	(boolean) (isset($args['tooltip']) ? $args['tooltip'] : false);
		$args['help_text'] 	= 	isset($args['help_text']) ? $args['help_text'] : '';
		$args['mandatory']	=	(boolean) (isset($args['mandatory']) ? $args['mandatory'] : false);
		$args['placeholder'] = 	isset($args['placeholder']) ? $args['placeholder'] : "";
		$args['after_form_field']	=	(isset($args['after_form_field']) ? $args['after_form_field'] : ''); 
		$args['before_form_field']	=	(isset($args['before_form_field']) ? $args['before_form_field'] : '');
		
		$args['select_dropdown_fields'] = isset($args['select_dropdown_fields']) ? $args['select_dropdown_fields'] : array();
		$args['checkbox_fields'] = isset($args['checkbox_fields']) ? $args['checkbox_fields'] : array();
		$args['radio_fields'] = isset($args['radio_fields']) ? $args['radio_fields'] : array();
		$args['alignment'] = isset($args['alignment']) ? $args['alignment'] : "";
		$args['module_base'] = isset($args['module_base']) ? $args['module_base'] : "";
		$args['heading_number'] = isset($args['heading_number']) ? $args['heading_number'] : "";
		$args['tr_id'] = isset($args['tr_id']) ? $args['tr_id'] : "";
		
		$args['end_col_call_back'] =  isset($args['end_col_call_back']) ? $args['end_col_call_back'] : "";

		$args['conditional_help_html']='';
		if(isset($args['help_text_conditional']) && is_array($args['help_text_conditional']))
		{		
			foreach ($args['help_text_conditional'] as $help_text_config)
			{
				if(is_array($help_text_config))
				{
					$condition_attr='';
					if(is_array($help_text_config['condition']))
					{
						$previous_type=''; /* this for avoiding fields without glue */
						foreach ($help_text_config['condition'] as $condition)
						{
							if(is_array($condition))
							{
								if($previous_type!='field')
								{
									$condition_attr.='['.$condition['field'].'='.$condition['value'].']';
									$previous_type='field';
								}
							}else
							{
								if(is_string($condition))
								{
									$condition=strtoupper($condition);
									if(($condition=='AND' || $condition=='OR') && $previous_type!='glue')
									{
										$condition_attr.='['.$condition.']';
										$previous_type='glue';
									}
								}
							}
						}
					}			
					$args['conditional_help_html'].='<span class="wf_form_help wt_pklist_conditional_help_text" data-wt_pklist-help-condition="'.esc_attr($condition_attr).'">'.$help_text_config['help_text'].'</span>';
				}	
			}
		}
		$args['form_toggler_p_class']="";
		$args['form_toggler_register']="";
		$args['form_toggler_child']="";
		if(isset($args['form_toggler']))
		{
			if($args['form_toggler']['type']=='parent')
			{
				$args['form_toggler_p_class']="wf_form_toggle";
				$args['form_toggler_register']=' wf_frm_tgl-target="'.$args['form_toggler']['target'].'"';
			}
			elseif($args['form_toggler']['type']=='child')
			{
				$args['form_toggler_child']=' wf_frm_tgl-id="'.$args['form_toggler']['id'].'" wf_frm_tgl-val="'.$args['form_toggler']['val'].'" '.(isset($args['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$args['form_toggler']['chk'].'"' : '').(isset($args['form_toggler']['lvl']) ? ' wf_frm_tgl-lvl="'.$args['form_toggler']['lvl'].'"' : '');	
			}else
			{
				$args['form_toggler_child']=' wf_frm_tgl-id="'.$args['form_toggler']['id'].'" wf_frm_tgl-val="'.$args['form_toggler']['val'].'" '.(isset($args['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$args['form_toggler']['chk'].'"' : '').(isset($args['form_toggler']['lvl']) ? ' wf_frm_tgl-lvl="'.$args['form_toggler']['lvl'].'"' : '');	
				$args['form_toggler_p_class']="wf_form_toggle";
				$args['form_toggler_register']=' wf_frm_tgl-target="'.$args['form_toggler']['target'].'"';				
			}
			
		}

		if($args['mandatory'])
		{
			$args['attr'].=' required="required"';	
		}
		return $args;
	}
}
?>