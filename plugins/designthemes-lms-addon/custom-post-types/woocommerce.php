<?php

if(!class_exists('DTLMSWooCommerce')) {
	
	class DTLMSWooCommerce {
		
		// Custom post types for woocommerce functionality 
		private $cpt = array('dtlms_courses', 'dtlms_packages', 'dtlms_classes');
		
		private static $saved_meta_boxes = false;
		
		function __construct() {

			// Customizing data store
			add_filter( 'woocommerce_data_stores', array( $this, 'dtlms_woocommerce_data_stores' ) );
			require_once plugin_dir_path ( __FILE__ ) .  '/class/class.WooCommerce_Product_Data_Store.php';

								
			// To generate woocommerce screen ids
			add_action ( 'woocommerce_screen_ids', array ( $this, 'dtlms_woo_get_screen_ids' ) );
			
			// To custom post type data to 'product'
			add_action ( 'woocommerce_product_object', array ( $this, 'dtlms_woo_generate_product_object' ) );
			
			// To enqueue admin scripts
			add_action ( 'admin_enqueue_scripts', array ( $this, 'dtlms_woo_admin_scripts' ) );
			
			// WooCommerce metabox to add price
			add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_woocommerce_metabox') );


			add_action ( 'woocommerce_add_cart_item_data', array( $this, 'dtlms_add_productid_to_cart_item' ), 10, 2 );
			add_action ( 'woocommerce_add_order_item_meta', array( $this, 'dtlms_add_productid_to_order_item_data' ), 50, 2 );	

			
			// To save WooCommerce price
			add_action ( 'save_post', array ( $this, 'dtlms_woo_save_post_meta' ), 1, 2 );
			add_action ( 'pre_post_update', array ( $this, 'dtlms_woo_save_post_meta' ), 1, 2 );
			
		}

		function dtlms_woocommerce_data_stores ( $stores ) {

			$stores['product'] = 'DT_Product_Data_Store_CPT';		
			return $stores;

		}		
				
		function dtlms_woo_get_screen_ids( $screen_ids = array() ) {

			foreach($this->cpt as $cpt_item) {
				$screen_ids[] = $cpt_item;
				$screen_ids[] = 'edit-'.$cpt_item;
			}
			
			return $screen_ids;

		}
		
		function dtlms_woo_generate_product_object( $the_product ) {
				
			$the_product->cpt_post_type = $the_product->post_type;
			$the_product->post_type = 'product';
			
			return $the_product;
			
		}
		
		function dtlms_woo_admin_scripts() {
			
			global $wp_query, $post;
	
			$screen       = get_current_screen();
			$screen_id    = $screen ? $screen->id : '';
			
			if(in_array($screen_id, $this->cpt)) {
				
				wp_register_script( 'wc-admin-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes.min.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round', 'wc-enhanced-select', 'plupload-all', 'stupidtable', 'jquery-tiptip' ), WC_VERSION );
				wp_enqueue_script( 'wc-admin-meta-boxes' );
			
			}
			
		}	
		
		function dtlms_add_woocommerce_metabox() {
			
			foreach($this->cpt as $cpt_item) {
				add_meta_box ( 'dtlms-woocommerce-metabox', esc_html__( 'WooCommerce Options', 'dtlms' ), array ( $this, 'dtlms_woocommerce_metabox' ), $cpt_item, "normal", "default" );
			}
			
		}
		
		function dtlms_woocommerce_metabox() {
			
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/woocommerce-metabox.php';
			
		}

		function dtlms_add_productid_to_cart_item( $cart_item_meta, $product_id ) {

			if( !in_array( get_post_type( $product_id ), $this->cpt) ) {
				return $cart_item_meta;
			}

			$cart_item_meta['product_id'] = $product_id;

			return $cart_item_meta;
		}

		function dtlms_add_productid_to_order_item_data( $item_id, $values ) {

			wc_add_order_item_meta($item_id, 'dtlms_item_id', $values['product_id'] );
		}

		
		function dtlms_woo_save_post_meta( $post_id, $post ) {
			
			if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
				return;
			}
	
			if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
				
			if ( empty( $_POST['dtlms_woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['dtlms_woocommerce_meta_nonce'], 'dtlms_woocommerce_nonce' ) ) {
				return;
			}
			
			if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
				return;
			}
	
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			
			self::$saved_meta_boxes = true;
			
			if ( in_array( $post['post_type'], $this->cpt ) ) {
				do_action( 'woocommerce_process_product_meta', $post_id, $post );
			}
			
		}

				
	}

}
?>