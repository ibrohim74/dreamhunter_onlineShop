<?php
$template_arr=array(
	array(
		'id'=>'template5-new',
		'title'=>__('Basic - 1', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'preview_img'=>'template5-new.png',
	),
	array(
		'id'=>'template5',
		'title'=>__('Basic - 2', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'preview_img'=>'template5.png',
	),
	array(
		'id'=>'template4',
		'title'=>__('Basic - 3', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'preview_img'=>'template4.png',
	),
);

$template_arr = apply_filters("wt_pklist_add_pro_templates",$template_arr,$this->to_customize);