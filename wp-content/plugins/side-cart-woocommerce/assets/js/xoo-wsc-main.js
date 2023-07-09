jQuery(document).ready(function($){

	var isCartPage 		= xoo_wsc_params.isCart == '1',
		isCheckoutPage 	= xoo_wsc_params.isCheckout == '1';

	var get_wcurl = function( endpoint ) {
		return xoo_wsc_params.wc_ajax_url.toString().replace(
			'%%endpoint%%',
			endpoint
		);
	};


	class Notice{

		constructor( $modal ){
			this.$modal = $modal;
			this.timeout = null;
		}

		add( notice, type = 'success', clearPrevious = true ){

			var $noticeCont = this.$modal.find('.xoo-wsc-notice-container');

			if( clearPrevious ){
				$noticeCont.html('');
			}

			var noticeHTML = type === 'success' ? xoo_wsc_params.html.successNotice.toString().replace( '%s%', notice ) : xoo_wsc_params.html.errorNotice.toString().replace( '%s%', notice );

			$noticeCont.html( noticeHTML );

		}

		showNotification(){

			var $noticeCont = this.$modal.find('.xoo-wsc-notice-container');

			if( !$noticeCont.length || $noticeCont.children().length === 0 ) return;

			$noticeCont.slideDown();
			
			clearTimeout(this.timeout);

			this.timeout = setTimeout(function(){
				$noticeCont.slideUp('slow',function(){
					//$noticeCont.html('');
				});
			},xoo_wsc_params.notificationTime )

		}

		hideNotification(){
			this.$modal.find('.xoo-wsc-notice-container').hide();
		}
	}


	class Container{

		constructor( $modal, container ){
			this.$modal 	= $modal;
			this.container 	= container || 'cart';
			this.notice 	= new Notice( this.$modal );
		}

		eventHandlers(){
			$(document.body).on( 'wc_fragments_refreshed updated_checkout', this.onCartUpdate.bind(this) );
		}

		onCartUpdate(){
			this.unblock();
			this.notice.showNotification();
		}

		setAjaxData( data, noticeSection ){

			var ajaxData = {
				container: this.container,
				noticeSection: noticeSection || this.noticeSection || this.container,
				isCheckout: isCheckoutPage,
				isCart: isCartPage
			}


			if( typeof data === 'object' ){

				$.extend( ajaxData, data );

			}
			else{

				var serializedData = data;

				$.each( ajaxData, function( key, value ){
					serializedData += ( '&'+key+'='+value );
				} )
		
				ajaxData = serializedData;

			}

			return ajaxData;
		}


		toggle( type ){

			var $activeEls 	= this.$modal.add( 'body' ).add('html'),
				activeClass = 'xoo-wsc-'+ this.container +'-active';

			if( type === 'show' ){
				$activeEls.addClass(activeClass);
			}
			else if( type === 'hide' ){
				$activeEls.removeClass(activeClass);
			}
			else{
				$activeEls.toggleClass(activeClass);
			}

			$(document.body).trigger( 'xoo_wsc_' + this.container + '_toggled', [ type ] );

			this.notice.hideNotification();

		}


		block(){
			this.$modal.addClass('xoo-wsc-loading');
		}

		unblock(){
			this.$modal.removeClass('xoo-wsc-loading');
		}


		refreshMyFragments(){

			if( xoo_wsc_params.refreshCart === "yes" && typeof wc_cart_fragments_params !== 'undefined' ){
				$( document.body ).trigger( 'wc_fragment_refresh' );
				return;
			}

			this.block();

			$.ajax({
				url: get_wcurl( 'xoo_wsc_refresh_fragments' ),
				type: 'POST',
				context: this,
				data: {},
				success: function( response ){
					this.updateFragments(response);
				},
				complete: function(){
					this.unblock();
				}
			})

		}


		updateCartCheckoutPage(){

			//Refresh checkout page
			if( isCheckoutPage ){
				if( $( 'form.checkout' ).length === 0 ){
					location.reload();
					return;
				}
				$(document.body).trigger("update_checkout");
			}

			//Refresh Cart page
			if( isCartPage ){
				$(document.body).trigger("wc_update_cart");
			}

		}

		updateFragments( response ){

			console.log('updated');

			if( response.fragments ){

				$( document.body ).trigger( 'xoo_wsc_before_loading_fragments', [ response ] );

				this.block();

				//Set fragments
		   		$.each( response.fragments, function( key, value ) {
					$( key ).replaceWith( value );
				});

		   		if( typeof wc_cart_fragments_params !== 'undefined' && ( 'sessionStorage' in window && window.sessionStorage !== null ) ){

		   			sessionStorage.setItem( wc_cart_fragments_params.fragment_name, JSON.stringify( response.fragments ) );
					localStorage.setItem( wc_cart_fragments_params.cart_hash_key, response.cart_hash );
					sessionStorage.setItem( wc_cart_fragments_params.cart_hash_key, response.cart_hash );

					if ( response.cart_hash ) {
						sessionStorage.setItem( 'wc_cart_created', ( new Date() ).getTime() );
					}

				}

				$( document.body ).trigger( 'wc_fragments_refreshed' );

				this.unblock();

			}

			if( xoo_wsc_params.refreshCart === "yes" && typeof wc_cart_fragments_params !== 'undefined' ){
				this.block();
				$( document.body ).trigger( 'wc_fragment_refresh' );
				return;
			}

		}

	}


	class Cart extends Container{

		constructor( $modal ){

			super( $modal, 'cart' );

			this.baseQty 		= 1;
			this.qtyUpdateDelay = null;

			this.refreshFragmentsOnPageLoad();
			this.eventHandlers();
			this.initSlider();

		}


		refreshFragmentsOnPageLoad(){
			setTimeout(function(){
				this.refreshMyFragments();
			}.bind(this), xoo_wsc_params.fetchDelay )
		}

		eventHandlers(){

			super.eventHandlers();

			this.$modal.on( 'click', '.xoo-wsc-chng', this.toggleQty.bind(this) );
			this.$modal.on( 'change', '.xoo-wsc-qty', this.changeInputQty.bind(this) );
			this.$modal.on( 'click', '.xoo-wsc-undo-item', this.undoItem.bind(this) );
			this.$modal.on( 'focusin', '.xoo-wsc-qty', this.saveQtyFocus.bind(this) );
			this.$modal.on( 'click', '.xoo-wsc-smr-del', this.deleteIconClick.bind(this) );
			this.$modal.on( 'click', '.xoo-wsch-close, .xoo-wsc-opac, .xoo-wsc-cart-close', this.closeCartOnClick.bind(this) );
			this.$modal.on( 'click', '.xoo-wsc-basket', this.toggle.bind(this) );

			$(document.body).on( 'xoo_wsc_cart_updated', this.updateCartCheckoutPage.bind(this) );
			$(document.body).on( 'click', 'a.added_to_cart, .xoo-wsc-cart-trigger', this.openCart.bind(this) );
			$(document.body).on( 'added_to_cart', this.addedToCart.bind(this) );

			if( xoo_wsc_params.autoOpenCart === 'yes' && xoo_wsc_params.addedToCart === 'yes'){
				this.openCart();
			}

			if( xoo_wsc_params.ajaxAddToCart === 'yes' ){
				$(document.body).on( 'submit', 'form.cart', this.addToCartFormSubmit.bind(this) );
			}

			if( typeof wc_cart_fragments_params === 'undefined' ){
				$( window ).on( 'pageshow' , this.onPageShow );
			}

			if( xoo_wsc_params.triggerClass ){
				$(document.body).on( 'click', '.'+xoo_wsc_params.triggerClass, this.openCart.bind(this) );
			}


			if( isCheckoutPage || isCartPage ){
				$(document.body).on( 'updated_shipping_method', this.refreshMyFragments.bind(this) );
			}

			//Animate shipping bar
			$(document.body).on( 'xoo_wsc_before_loading_fragments', this.storeShippingBarWidth.bind(this) );

		}


		openCart(e){
			if( e ){
				e.preventDefault();
				e.stopImmediatePropagation();
			}
			this.toggle('show');
		}

		addToCartFormSubmit(e){

			var $form = $(e.currentTarget);

			if( $form.closest('.product').hasClass('product-type-external') ) return;

			e.preventDefault();

			var $button  		= $form.find( 'button[type="submit"]'),
				productData 	= $form.serializeArray(),
				hasProductId 	= false;

			//Check for woocommerce custom quantity code 
			//https://docs.woocommerce.com/document/override-loop-template-and-show-quantities-next-to-add-to-cart-buttons/
			$.each( productData, function( key, form_item ){
				if( form_item.name === 'productID' || form_item.name === 'add-to-cart' ){
					if( form_item.value ){
						hasProductId = true;
						return false;
					}
				}
			})

			//If no product id found , look for the form action URL
			if( !hasProductId ){
				var is_url = $form.attr('action').match(/add-to-cart=([0-9]+)/),
					productID = is_url ? is_url[1] : false; 
			}

			// if button as name add-to-cart get it and add to form
	        if( $button.attr('name') && $button.attr('name') == 'add-to-cart' && $button.attr('value') ){
	            var productID = $button.attr('value');
	        }

	        if( productID ){
	        	productData.push({name: 'add-to-cart', value: productID});
	        }

	        productData.push({name: 'action', value: 'xoo_wsc_add_to_cart'});

			this.addToCartAjax( $button, productData );//Ajax add to cart
		}


		addToCartAjax( $button, productData ){

			this.block();

			$button.addClass('loading');

			// Trigger event.
			$( document.body ).trigger( 'adding_to_cart', [ $button, productData ] );

			$.ajax({
				url: get_wcurl( 'xoo_wsc_add_to_cart' ),
				type: 'POST',
				context: this,
				data: $.param(productData),
			    success: function(response){

					if(response.fragments){
						// Trigger event so themes can refresh other areas.
						$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $button ] );
					}else{
						window.location.reload();
					}

			    },
			    complete: function(){
			    	this.unblock();
			    	$button
			    		.removeClass('loading')
			    		.addClass('added');
			    }
			})
		}

		addedToCart( e, response, hash, $button ){

			this.updateFragments( { fragments: response } );

			this.onCartUpdate();
	
			var _this = this;

			this.flyToCart( $button, function(){
				if( xoo_wsc_params.autoOpenCart === "yes" ){
					setTimeout(function(){
						_this.openCart();	
					},20 )
				}
			} );
		}


		flyToCart( $atcEL, callback ){

			var $basket = this.$modal.find('.xoo-wsc-basket').length ? this.$modal.find('.xoo-wsc-basket') : $(document.body).find('.xoo-wsc-sc-cont');

			if( !$basket.length || xoo_wsc_params.flyToCart !== 'yes' ){
				callback();
				return;
			}

			var customDragImgClass 	= '',
				$dragIMG 			= null,
				$product 			= $atcEL.parents('.product');


			//If has product container
			if( $product.length ){

				var $productGallery = $product.find('.woocommerce-product-gallery');

				if( customDragImgClass && $product.find( customDragImgClass ).length ){
					$dragIMG = $product.find( customDragImgClass );
				}
				else if( $product.find( 'img[data-xooWscFly="fly"]' ).length ){
					if( $productGallery.length ){
						$dragIMG = $productGallery.find( '.flex-active-slide img[data-xooWscFly="fly"]' ).length ? $productGallery.find( '.flex-active-slide img[data-xooWscFly="fly"]' ) : $productGallery.find( 'img[data-xooWscFly="fly"]' )
					}
					else{
						$dragIMG = $product.find( 'img[data-xooWscFly="fly"]' );
					}
				}
				else if( $productGallery.length ){
					$dragIMG = $productGallery;
				}
				else{
					$dragIMG = $product;
				}

			}
			else if( customDragImgClass ){
				var moveUp = 4;
				for ( var i = moveUp; i >= 0; i-- ) {
					var $foundImg = $atcEL.parent().find( customDragImgClass );
					if( $foundImg.length ){
						$dragIMG = $foundImg;
						return false;
					}
				}
			}


			if( !$dragIMG || !$dragIMG.length ){
				callback();
				return;
			}

			var $imgclone = $dragIMG
				.clone()
	    		.offset({
		            top: $dragIMG.offset().top,
		            left: $dragIMG.offset().left
		        })
	        	.addClass( 'xoo-wsc-fly-animating' )
	            .appendTo( $('body') )
	            .animate({
	            	'top': $basket.offset().top - 10,
		            'left': $basket.offset().left - 10,
		            'width': 75,
		            'height': 75
		        }, 1000, 'easeInOutExpo' );
	        
	        setTimeout(function () {
	        	callback()
	        }, 1500 );

	        $imgclone.animate({
	        	'width': 0,
	        	'height': 0
	        }, function () {
	        	$(this).detach();
	        });

		}


		toggleQty(e){

			var $toggler 	= $(e.currentTarget),
				$input 		= $toggler.siblings('.xoo-wsc-qty');

			if( !$input.length ) return;

			var baseQty = this.baseQty = parseFloat( $input.val() ),
				step 	= parseFloat( $input.attr('step') ),
				action 	= $toggler.hasClass( 'xoo-wsc-plus' ) ? 'add' : 'less',
				newQty 	= action === 'add' ? baseQty + step : baseQty - step;

			
			$input.val(newQty).trigger('change');

		}

		changeInputQty(e){

			this.notice.hideNotification();

			var $_this	= this,
 				$input 	= $(e.currentTarget),
				newQty 	= parseFloat( $input.val() ),
				step 	= parseFloat( $input.attr('step') ),
				min 	= parseFloat( $input.attr('min') ),
				max 	= parseFloat( $input.attr('max') ),
				invalid = false,
				message = false;


			//Validation
			
			if( isNaN( newQty )  || newQty < 0 || newQty < min  ){
				invalid = true;
			}
			else if( newQty > max ){
				invalid = true;
				message = xoo_wsc_params.strings.maxQtyError.replace( '%s%', max );
			}
			else if( ( newQty % step ) !== 0 ){
				invalid = true;
				message = xoo_wsc_params.strings.stepQtyError.replace( '%s%', step );
			}
			
			//Set back to default quantity
			if( invalid ){
				$input.val( this.baseQty );
				if( message ){
					this.notice.add( message, 'error' );
					this.notice.showNotification();
				}
				return;
			}

			//Update
			$input.val( newQty );

			clearTimeout( this.qtyUpdateDelay );

			this.qtyUpdateDelay = setTimeout(function(){
				$_this.updateItemQty( $input.parents('.xoo-wsc-product').data('key'), newQty )
			}, xoo_wsc_params.qtyUpdateDelay );
			
			
		}

		updateItemQty( cart_key, qty ){

			if( !cart_key || qty === undefined ) return;

			this.block();

			var formData = {
				cart_key: cart_key,
				qty: qty
			}

			$.ajax({
				url: get_wcurl( 'xoo_wsc_update_item_quantity' ),
				type: 'POST',
				context: this,
				data: this.setAjaxData(formData),
				success: function(response){
					this.updateFragments( response );
					$(document.body).trigger( 'xoo_wsc_quantity_updated', [response] );
					$(document.body).trigger( 'xoo_wsc_cart_updated', [response] );
					this.unblock();
				}

			})
		}


		closeCartOnClick(e){
			e.preventDefault();
			this.toggle( 'hide' );
		}


		saveQtyFocus(e){
			this.baseQty = $(e.currentTarget).val();
		}


		onPageShow(e){
			if ( e.originalEvent.persisted ) {
				this.refreshMyFragments();
				$( document.body ).trigger( 'wc_fragment_refresh' );
			}
		}

		deleteIconClick(e){
			this.updateItemQty( $( e.currentTarget ).parents('.xoo-wsc-product').data('key'), 0 );
		}

		undoItem(e){

			var $undo 		= $(e.currentTarget),
				formData 	= {
					cart_key: $undo.data('key')
				}

			this.block();

			$.ajax({
				url: get_wcurl('xoo_wsc_undo_item'),
				type: 'POST',
				context: this,
				data: this.setAjaxData(formData),
				success: function(response){
					this.updateFragments( response );
					$(document.body).trigger( 'xoo_wsc_item_restored', [response] );
					$(document.body).trigger( 'xoo_wsc_cart_updated', [response] );
					this.unblock();
				}

			})
		}

		storeShippingBarWidth( e ){
			var $bar = $(document.body).find( '.xoo-wsc-sb-bar > span' );
			if( !$bar.length ) return;
			this.shippingBarWidth = $bar.prop('style').width;
		}

		onCartUpdate(){
			super.onCartUpdate();
			this.animateShippingBar();
			this.initSlider();
			this.showBasket();
		}

		showBasket(){

			var $basket = $('.xoo-wsc-basket'),
				show 	= xoo_wsc_params.showBasket;

			if( show === "always_show" ){
				$basket.show();	
			}
			else if( show === "hide_empty" ){
				if( this.$modal.find('.xoo-wsc-product').length ){
					$basket.show();
				}
				else{
					$basket.hide();
				}
			}
			else{
				$basket.hide();
			}
		}

		animateShippingBar(){
			if( isCartPage || isCheckoutPage ) return;
			var $bar = $(document.body).find( '.xoo-wsc-sb-bar > span' );
			if( !$bar.length || !this.shippingBarWidth ) return;
			var newWidth = $bar.prop('style').width;
			$bar
				.width( this.shippingBarWidth )
				.animate({ width: newWidth }, 400, 'linear')
		}


		initSlider(){

			if( !$.isFunction( $.fn.lightSlider ) ) return;

			$('ul.xoo-wsc-sp-slider').lightSlider({
				item: 1,
			});
			
		}

	}

	

	class Slider extends Container{

		constructor( $modal ){

			super( $modal, 'slider' );

			if( xoo_wsc_params.sliderAutoClose ) this.noticeSection = 'cart';

			this.eventHandlers();

			this.shipping = xoo_wsc_params.shippingEnabled ? Shipping.init( this ) : null;
		}

		eventHandlers(){

			super.eventHandlers();


			$( document.body ).on( 'click', '.xoo-wsc-toggle-slider', this.triggerSlider.bind(this) );
			$( document.body ).on( 'xoo_wsc_cart_toggled', this.closeSliderOnCartClose.bind(this) );

			if( xoo_wsc_params.sliderAutoClose ){
				$( document.body ).on( 'xoo_wsc_coupon_applied xoo_wsc_shipping_calculated updated_shipping_method xoo_wsc_coupon_removed', this.closeSlider.bind(this) );
			}

			this.$modal.on( 'submit', 'form.xoo-wsc-sl-apply-coupon', this.submitCouponForm.bind(this) );
			this.$modal.on( 'click', '.xoo-wsc-coupon-apply-btn', this.applyCouponFromBtn.bind(this) );
			$(document.body).on( 'click', '.xoo-wsc-remove-coupon', this.removeCoupon.bind(this) );
		}


		removeCoupon(e){

			e.preventDefault();

			var $removeEl 	= $(e.currentTarget),
				coupon 		= $removeEl.data('code'),
				formData 	= {
					coupon: coupon,
				};

			this.block();	

			$.ajax( {
				url: get_wcurl( 'xoo_wsc_remove_coupon' ),
				type: 'POST',
				context: this,
				data: this.setAjaxData( formData, cart.$modal.find( $removeEl ).length ? 'cart' : 'slider' ),
				success: function( response ) {
					this.updateFragments(response);
				},
				complete: function() {
					this.unblock();
					this.updateCartCheckoutPage();
					$( document.body ).trigger( 'xoo_wsc_coupon_removed' );
				}
			} );
		}

		onCartUpdate(){
			super.onCartUpdate();
			this.toggleContent();
		}

		closeSlider(){
			this.toggle('hide');
		}


		applyCouponFromBtn(e){
			this.applyCoupon( $(e.currentTarget).val() );
		}

		submitCouponForm(e){

			e.preventDefault();

			var $form = $(e.currentTarget);

			this.applyCoupon( $form.find('input[name="xoo-wsc-slcf-input"]').val() );

		}


		closeSliderOnCartClose(e){

			var $this = $(e.currentTarget); 

			if( !cart.$modal.hasClass('xoo-wsc-cart-active') ){
				this.toggle('hide');
			}

		}


		triggerSlider(e){

			var $toggler 	= $(e.currentTarget),
 				slider 		= $toggler.data('slider');

			if( slider === 'shipping' && isCheckoutPage ){
				this.notice.add( xoo_wsc_params.strings.calculateCheckout, 'error' );
				this.notice.showNotification();
				return;
			}


			this.$modal.attr( 'data-slider', slider );
			
			this.toggle();

			this.toggleContent();
		}


		toggleContent(){

			var activeSlider = '';

			$('.xoo-wsc-sl-content').hide();
			
			var activeSlider 	= this.$modal.attr('data-slider'),
				$toggleEl 		= $('.xoo-wsc-sl-content[data-slider="'+activeSlider+'"]');
	
			if( $toggleEl.length ) $toggleEl.show();

			$( document.body ).trigger( 'xoo_wsc_slider_data_toggled', [activeSlider] );
		}


		applyCoupon( coupon ){

			if( !coupon ){
				this.notice.add( xoo_wsc_params.strings.couponEmpty, 'error' );
				this.notice.showNotification();
				return;
			}

			this.block();

			var formData = {
				'action': 'xoo_wsc_apply_coupon'
			}

			$.ajax( {
				url: get_wcurl('xoo_wsc_apply_coupon'),
				type: 'POST',
				context: this,
				data: this.setAjaxData( formData ),
				success: function( response ) {
					this.updateFragments(response);
				},
				complete: function() {
					this.unblock();
					this.updateCartCheckoutPage();
					$( document.body ).trigger( 'xoo_wsc_coupon_applied' );
				}
			} );

		}

	}

	

	var Shipping = {

		init: function( slider ){
			slider.$modal.on( 'change', 'input.xoo-wsc-shipping-method', this.shippingMethodChange );
			slider.$modal.on( 'submit', 'form.woocommerce-shipping-calculator', this.shippingCalculatorSubmit );
			slider.$modal.on( 'click', '.shipping-calculator-button', this.toggleCalculator );
			$(document.body).on( 'wc_fragments_refreshed wc_fragments_loaded xoo_wsc_slider_data_toggled', this.initSelect2 );
		},

		toggleCalculator: function(e){

			e.preventDefault();
			e.stopImmediatePropagation();

			$(this).siblings('.shipping-calculator-form').slideToggle();
			$( document.body ).trigger( 'country_to_state_changed' );
		},

		shippingCalculatorSubmit: function(e){

			e.preventDefault();
			e.stopImmediatePropagation();

			var $form = $(this);

			slider.block();

			// Provide the submit button value because wc-form-handler expects it.
			$( '<input />' )
				.attr( 'type', 'hidden' )
				.attr( 'name', 'calc_shipping' )
				.attr( 'value', 'x' )
				.appendTo( $form );

			var formData = $form.serialize();

			// Make call to actual form post URL.
			$.ajax( {
				url: get_wcurl( 'xoo_wsc_calculate_shipping' ),
				type: 'POST',
				context: this,
				data: slider.setAjaxData(formData),
				success: function( response ) {
					slider.updateFragments(response);
				},
				complete: function() {
					slider.unblock();
					slider.updateCartCheckoutPage();
					$( document.body ).trigger( 'xoo_wsc_shipping_calculated' );
				}
			} );

		},

		shippingMethodChange: function(e){

			e.preventDefault();
			e.stopImmediatePropagation();

			var shipping_methods = {};

			slider.block();

			$( 'select.shipping_method, :input[name^=xoo-wsc-shipping_method][type=radio]:checked, :input[name^=shipping_method][type=hidden]' ).each( function() {
				shipping_methods[ $( this ).data( 'index' ) ] = $( this ).val();
			} );

			var formData = {
				shipping_method: shipping_methods
			}

			$.ajax( {
				type:     'POST',
				url:      get_wcurl( 'xoo_wsc_update_shipping_method' ),
				data:     slider.setAjaxData( formData ),
				success:  function( response ) {
					slider.updateFragments(response);
				},
				complete: function() {
					slider.unblock();
					slider.updateCartCheckoutPage();
					$( document.body ).trigger( 'updated_shipping_method' );
				}
			} );

		},

		initSelect2: function(e){
			$( document.body ).trigger( 'country_to_state_changed' );
		},
	}


	var cart 	= new Cart( $('.xoo-wsc-modal') ),
		slider 	= new Slider( $('.xoo-wsc-slider-modal') );

})