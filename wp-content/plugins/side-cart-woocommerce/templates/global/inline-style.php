<?php

/* Main */
$cartWidth 		= (int) $sy['scm-width'];
$cartheight 	= $sy['scm-height'];
$openFrom 		= $sy['scm-open-from'];
$fontFamily 	= $sy['scm-font'];

/* Basket */
$BasketMobile 	= $sy['sck-show-mobile'];
$basketPosition = $sy['sck-position'];
$basketShape 	= $sy['sck-shape'];
$basketSize 	= $sy['sck-size'];
$basketOffset 	= $sy['sck-offset'];
$basketHOffset 	= $sy['sck-hoffset'];
$countPosition 	= $sy['sck-count-pos'];
$basketBG 		= $sy['sck-basket-bg'];
$basketColor 	= $sy['sck-basket-color'];
$basketShadow 	= $sy['sck-basket-sh'];
$countBG 		= $sy['sck-count-bg'];
$countColor 	= $sy['sck-count-color'];

/* Header */
$closeIconSize 	= $sy['sch-close-fsize'];
$closeIconAlign = $sy['sch-close-align'];
$headingAlign 	= $sy['sch-head-align'];
$headFontSize 	= $sy['sch-head-fsize'];
$headBGColor 	= $sy['sch-bgcolor'];
$headTxtColor 	= $sy['sch-txtcolor'];
$headBarColor 	= $sy['sch-sbcolor'];

/* Body */
$bodyFontSize 	= $sy['scb-fsize'];
$bodyBGColor 	= $sy['scb-bgcolor'];
$bodyTxtColor 	= $sy['scb-txtcolor'];
$bPpadding 		= $sy['scbp-padding'];
$bPimgwidth		= (int) $sy['scbp-imgw'];
$bpDisplay 		= $sy['scbp-display'];

/* Quantity */
$qtyStyle 		= $sy['scbq-style'];
$qtyWidth 		= $sy['scbq-width'];
$qtyHeight 		= $sy['scbq-height'];
$qtyBorsize 	= $sy['scbq-bsize'];
$inputBorColor 	= $sy['scbq-input-bcolor'];
$btnBorColor 	= $sy['scbq-box-bcolor'];
$inputBgColor 	= $sy['scbq-input-bgcolor'];
$inputTxtColor 	= $sy['scbq-input-txtcolor'];
$btnBgColor 	= $sy['scbq-box-bgcolor'];
$btnTxtColor 	= $sy['scbq-box-txtcolor'];

/* Footer */
$footerStick 	= $sy['scf-stick'];
$buttonsOrder  	= $sy['scf-button-pos'];
$buttonPadding 	= $sy['scf-btn-padding'];
$buttonRows 	= $sy['scf-btns-row'];
$buttonTheme 	= $sy['scf-btns-theme'];
$buttonbgColor 	= $sy['scf-btn-bgcolor'];
$buttontxtColor = $sy['scf-btn-txtcolor'];
$buttonBorder 	= $sy['scf-btn-border'];

$HVbuttonbgColor 	= $sy['scf-btnhv-bgcolor'];
$HVbuttontxtColor 	= $sy['scf-btnhv-txtcolor'];
$HVbuttonBorder 	= $sy['scf-btnhv-border'];

$ftrPadding 	= $sy['scf-padding'];
$ftrBgColor 	= $sy['scf-bgcolor'];
$ftrTxtColor 	= $sy['scf-txtcolor'];
$ftrFsize 		= $sy['scf-fsize'];

/* Suggested Products */
$spImgWidth 	= (int) $sy['scsp-imgw'];
$spFontSize 	= (int) $sy['scsp-fsize'];
$spBGColor 		= $sy['scsp-bgcolor'];


/* Shortcode */
$SCbasketSize 	= $sy['shbk-size'];
$SCbasketColor 	= $sy['shbk-color'];
$SCcountBG 		= $sy['shbk-count-bg'];
$SCcountColor 	= $sy['shbk-count-color'];
$SCtxtColor 	= $sy['shbk-txt-color'];

if( $buttonRows === 'three' ){
	$gridCols = '1fr 1fr 1fr';
}
elseif ( $buttonRows === 'two_one' ) {
	$gridCols = '2fr 2fr';
	echo 'a.xoo-wsc-ft-btn:nth-child(3){
		grid-column: 1/-1;
	}';
}
elseif ( $buttonRows === 'one_two' ) {
	$gridCols = '2fr 2fr';
	echo 'a.xoo-wsc-ft-btn:nth-child(1){
		grid-column: 1/-1;
	}';
}
else{
	$gridCols = 'auto';
}

?>

.xoo-wsc-sp-left-col img{
	max-width: <?php echo $spImgWidth ?>px;
}

.xoo-wsc-sp-right-col{
	font-size: <?php echo $spFontSize ?>px;
}

.xoo-wsc-sp-container{
	background-color: <?php echo $spBGColor ?>;
}


<?php if( $buttonTheme === 'custom' ): ?>

.xoo-wsc-ft-buttons-cont a.xoo-wsc-ft-btn, .xoo-wsc-container .xoo-wsc-btn {
	background-color: <?php echo $buttonbgColor ?>;
	color: <?php echo $buttontxtColor ?>;
	border: <?php echo $buttonBorder ?>;
	padding: <?php echo $buttonPadding ?>;
}

.xoo-wsc-ft-buttons-cont a.xoo-wsc-ft-btn:hover, .xoo-wsc-container .xoo-wsc-btn:hover {
	background-color: <?php echo $HVbuttonbgColor ?>;
	color: <?php echo $HVbuttontxtColor ?>;
	border: <?php echo $HVbuttonBorder ?>;
}

<?php endif; ?> 

.xoo-wsc-footer{
	background-color: <?php echo $ftrBgColor ?>;
	color: <?php echo $ftrTxtColor ?>;
	padding: <?php echo $ftrPadding ?>;
}

.xoo-wsc-footer, .xoo-wsc-footer a, .xoo-wsc-footer .amount{
	font-size: <?php echo $ftrFsize ?>px;
}

.xoo-wsc-ft-buttons-cont{
	grid-template-columns: <?php echo $gridCols ?>;
}

.xoo-wsc-basket{
	<?php echo $basketPosition ?>: <?php echo $basketOffset ?>px;
	<?php echo $openFrom ?>: <?php echo $basketHOffset ?>px;
	background-color: <?php echo $basketBG ?>;
	color: <?php echo $basketColor ?>;
	box-shadow: <?php echo $basketShadow ?>;
	border-radius: <?php echo $basketShape === 'round' ? '50%' : '14px' ?>
}

<?php if( $BasketMobile !== 'yes' ): ?>

@media only screen and (max-width: 600px) {
	.xoo-wsc-basket {
		display: none!important;
	}
}

<?php endif; ?>

.xoo-wsc-bki{
	font-size: <?php echo $basketSize.'px' ?>
}

.xoo-wsc-items-count{
	<?php echo $countPosition === 'top_right' || $countPosition === 'top_left' ? 'top' : 'bottom' ?>: -12px;
	<?php echo $countPosition === 'top_right' || $countPosition === 'bottom_right' ? 'right' : 'left' ?>: -12px;
}

.xoo-wsc-items-count{
	background-color: <?php echo $countBG ?>;
	color: <?php echo $countColor ?>;
}

.xoo-wsc-container, .xoo-wsc-slider{
	max-width: <?php echo $cartWidth ?>px;
	<?php echo $openFrom ?>: <?php echo -$cartWidth ?>px;
	<?php echo $cartheight === 'full' ? 'top: 0;bottom: 0' : 'max-height: 100vh' ?>;
	<?php echo $basketPosition ?>: 0;
	font-family: <?php echo $fontFamily; ?>
}


.xoo-wsc-cart-active .xoo-wsc-container, .xoo-wsc-slider-active .xoo-wsc-slider{
	<?php echo $openFrom ?>: 0;
}

<?php if( $footerStick !== 'yes' ): ?>

.xoo-wsc-container {
    display: block;
    overflow: auto;
}

<?php endif; ?>

.xoo-wsc-cart-active .xoo-wsc-basket{
	<?php echo $openFrom ?>: <?php echo $cartWidth ?>px;
}

.xoo-wsc-slider{
	right: -<?php echo $cartWidth ?>px;
}

span.xoo-wsch-close {
    font-size: <?php echo $closeIconSize ?>px;
    <?php echo $closeIconAlign ?>: 10px;
}

.xoo-wsch-top{
	justify-content: <?php echo $headingAlign ?>;
}

.xoo-wsch-text{
	font-size: <?php echo $headFontSize ?>px;
}

.xoo-wsc-header{
	color: <?php echo $headTxtColor ?>;
	background-color: <?php echo $headBGColor ?>;
}

.xoo-wsc-sb-bar > span{
	background-color: <?php echo $headBarColor ?>;
}

.xoo-wsc-body{
	background-color: <?php echo $bodyBGColor ?>;
}

.xoo-wsc-body, .xoo-wsc-body span.amount, .xoo-wsc-body a{
	font-size: <?php echo $bodyFontSize ?>px;
	color: <?php echo $bodyTxtColor ?>;
}

.xoo-wsc-product{
	padding: <?php echo $bPpadding ?>;
}

.xoo-wsc-img-col{
	width: <?php echo $bPimgwidth ?>%;
}
.xoo-wsc-sum-col{
	width: <?php echo 100-$bPimgwidth ?>%;
}

<?php if( $bpDisplay === 'stretched' ): ?>
.xoo-wsc-sm-info{
	flex-grow: 1;
    align-self: stretch;
}
<?php else: ?>
.xoo-wsc-sum-col{
	justify-content: <?php echo $bpDisplay ?>;
}
<?php endif; ?>

/***** Quantity *****/

.xoo-wsc-qty-box{
	max-width: <?php echo $qtyWidth ?>px;
}

.xoo-wsc-qty-box.xoo-wsc-qtb-square{
	border-color: <?php echo $btnBorColor ?>;
}

input[type="number"].xoo-wsc-qty{
	border-color: <?php echo $inputBorColor ?>;
	background-color: <?php echo $inputBgColor ?>;
	color: <?php echo $inputTxtColor ?>;
	height: <?php echo $qtyHeight ?>px;
	line-height: <?php echo $qtyHeight ?>px;
}

input[type="number"].xoo-wsc-qty, .xoo-wsc-qtb-square{
	border-width: <?php echo $qtyBorsize ?>px;
	border-style: solid;
}
.xoo-wsc-chng{
	background-color: <?php echo $btnBgColor ?>;
	color: <?php echo $btnTxtColor ?>;
}

/** Shortcode **/
.xoo-wsc-sc-count{
	background-color: <?php echo $SCcountBG ?>;
	color: <?php echo $SCcountColor ?>;
}

.xoo-wsc-sc-bki{
	font-size: <?php echo $SCbasketSize ?>px;
	color: <?php echo $SCbasketSize ?>;
}
.xoo-wsc-sc-cont{
	color: <?php echo $SCtxtColor ?>;
}

<?php if( $gl['m-viewcart-del'] === 'yes' ): ?>
.added_to_cart{
	display: none!important;
}
<?php endif; ?>