<?php 
add_action( 'vc_before_init', 'dtlms_classes_listing_vc_map' );

function dtlms_classes_listing_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );

	$dtlms_pages_list = array ();
	$dtlms_pages_list[esc_html__('Default - Ajax Output', 'dtlms')] = '';
	$pages = get_pages(); 
	foreach ( $pages as $page ) {
		$dtlms_pages_list[$page->post_title] = $page->ID;
	}

	$class_singular_label = apply_filters( 'class_label', 'singular' );
	$class_plural_label = apply_filters( 'class_label', 'plural' );

	vc_map( array(
		"name" => sprintf( esc_html__( '%1$s Listing', 'dtlms' ), $class_plural_label ),
		"base" => "dtlms_classes_listing",
		"icon" => "dtlms_classes_listing",
		"category" => DTLMSADDON_TITLE,
		"params" => array(

			// Disable All Filter Options
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Disable All Filter Options','dtlms'),
				'param_name' => 'disable-all-filters',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can disable all filter options and only class content will be displayed.', 'dtlms' ),
				'std' => ''				
			),

			// Enable Search Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Search Filter','dtlms'),
				'param_name' => 'enable-search-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable search filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',				
				'std' => ''				
			),

			// Enable Display Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Display Filter','dtlms'),
				'param_name' => 'enable-display-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable display filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',				
				'std' => ''				
			),

			// Enable Class Type Filter
			array(
				'type' => 'dropdown',
				'heading' => sprintf( esc_html__( 'Enable %1$s Type Filter', 'dtlms' ), $class_singular_label ),
				'param_name' => 'enable-classtype-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable class type filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',				
				'std' => ''				
			),			

			// Enable Order By Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Order By Filter','dtlms'),
				'param_name' => 'enable-orderby-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable orderby filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',				
				'std' => ''				
			),

			// Enable Instructor Filter
			array(
				'type' => 'dropdown',
				'heading' => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_label),
				'param_name' => 'enable-instructor-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable instructor filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',				
				'std' => ''				
			),	

			// Enable Cost Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Cost Filter','dtlms'),
				'param_name' => 'enable-cost-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable cost filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',				
				'std' => ''				
			),		

			// Enable Date Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Date Filter','dtlms'),
				'param_name' => 'enable-date-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable date filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'std' => ''				
			),			

			// Listing Output Page
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Listing Output Page','dtlms'),
				'param_name' => 'listing-output-page',
				'value' => $dtlms_pages_list,			
				'description' => esc_html__( 'If you choose a page here class search result will be outputed in that page. For that you have to add this class listing shortcode again in that page.', 'dtlms' ),
				'std' => ''				
			),

			// Default Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Default Filter','dtlms'),
				'param_name' => 'default-filter',
				'value' => array(
					esc_html__('None', 'dtlms') => '',
					sprintf( esc_html__( 'Upcoming %1$s', 'dtlms' ), $class_plural_label ) => 'upcoming-classes',
					sprintf( esc_html__( 'Recent %1$s', 'dtlms' ), $class_plural_label ) => 'recent-classes',
					sprintf( esc_html__( 'Highest Rated %1$s', 'dtlms' ), $class_plural_label ) => 'highest-rated-classes',
					sprintf( esc_html__( 'Most Membered %1$s', 'dtlms' ), $class_plural_label ) => 'most-membered-classes',
					sprintf( esc_html__( 'Paid %1$s', 'dtlms' ), $class_plural_label ) => 'paid-classes',
					sprintf( esc_html__( 'Free %1$s', 'dtlms' ), $class_plural_label ) => 'free-classes',
				),				
				'description' => sprintf( esc_html__( 'Choose default filter you like to apply in %1$s listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ), strtolower($class_plural_label) ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'true'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Class Item Ids
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Class Item Ids','dtlms'),
				'param_name' => 'class-item-ids',
				'value' => '',
				'description' => esc_html__( 'Enter class item ids separated by comma to display from. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'true'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Instructor Ids
			array(
				'type' => 'textfield',
				'heading' => sprintf(esc_html__('%s Ids', 'dtlms'), $instructor_label),
				'param_name' => 'instructor-ids',
				'value' => '',
				'description' => sprintf(esc_html__('Enter %s ids separated by comma to display from. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms'), $instructor_label),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'true'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),			


			// Apply Isotope
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Apply Isotope','dtlms'),
				'param_name' => 'apply-isotope',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you like to apply isotope for your classes listing, choose "True". This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page". "Apply Isotope" won\'t work along with "Carousel".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Display Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Display Type','dtlms'),
				'param_name' => 'default-display-type',
				'value' => array(
					esc_html__('Grid', 'dtlms') => 'grid',
					esc_html__('List', 'dtlms') => 'list',
				),				
				'description' => esc_html__( 'Choose display type for your classes listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 'grid'				
			),
	
			// Post Per Page
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Post Per Page', 'dtlms' ),
				'param_name' => 'post-per-page',
				'description' => esc_html__( 'Number of posts to show. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Columns
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Columns', 'dtlms'),
				'param_name' => 'columns',
				'value' => array( 
							esc_html__('I Column', 'dtlms') => 1 ,
							esc_html__('II Columns', 'dtlms') => 2 ,
							esc_html__('III Columns', 'dtlms') => 3,
						),
				'description' => esc_html__( 'Number of columns you like to display your classes. III Columns option will work only if "Enable Fullwidth" is set to "True". Also III Columns option is applicable for "Grid View" only when all filters are disabled. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'std' => 1,
				'dependency' => array( 'element' => 'default-display-type', 'value' => 'grid'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Enable Fullwidth
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Fullwidth','dtlms'),
				'param_name' => 'enable-fullwidth',
				'value' => array( 
							esc_html__('False','dtlms') => '', 
							esc_html__('True','dtlms') => 'true', 
						),
				'description' => esc_html__( 'If you wish you can enable fullwidth for your class listings. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''
			),	

			// Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Type','dtlms'),
				'param_name' => 'type',
				'value' => array(
					esc_html__('Type 1', 'dtlms') => 'type1',
					esc_html__('Type 2', 'dtlms') => 'type2',
					esc_html__('Type 3', 'dtlms') => 'type3',
				),
				'description' => esc_html__( 'Choose any of the available design types.', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 'type1'				
			),						

			// Class
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Class', 'dtlms' ),
				'param_name' => 'class',
				'description' => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'admin_label' => true
			),


			// Carousel Options

			// Enable Carousel
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Carousel','dtlms'),
				'param_name' => 'enable-carousel',
				'value' => array( 
							esc_html__('False','dtlms') => '', 
							esc_html__('True','dtlms') => 'true', 
						),
				'description' => esc_html__( 'If you wish you can enable carousel for class listings. "Carousel" won\'t work along with "Apply Isotope".', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'apply-isotope', 'value' => 'false'),	
				'std' => ''
			),

			/*// Effect
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Effect', 'dtlms'),
				'param_name' => 'carousel-effect',
				'value' => array( 
							esc_html__('Default', 'dtlms') => '', 
							esc_html__('Fade', 'dtlms') => 'fade', 
						),
				'description' => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Cube and Fade effect.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''
			),*/

			// Auto Play
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Auto Play', 'dtlms'),
				'param_name' => 'carousel-autoplay',
				'description' => esc_html__( 'Delay between transitions ( in ms, ex. 1000 ). Leave empty if you don\'t want to auto play.', 'dtlms' ),	
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
			),

			// Slides Per View
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Slides Per View','dtlms'),
				'param_name' => 'carousel-slidesperview',
				'value' => array( 
							1 => 1, 
							2 => 2, 
							3 => 3, 
						),
				'description' => esc_html__( 'Number slides of to show in view port. If display type is "List", 2 & 3 option in "Slides Per View" won\'t work.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => 2
			),	

			// Enable loop mode
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Loop Mode','dtlms'),
				'param_name' => 'carousel-loopmode',
				'value' => array(
					esc_html__('False','dtlms') => 'false',
					esc_html__('True','dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable continous loop mode for your carousel.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),	

			// Enable mousewheel control
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Mousewheel Control', 'dtlms'),
				'param_name' => 'carousel-mousewheelcontrol',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),	

			// Enable Bullet Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Bullet Pagination', 'dtlms'),
				'param_name' => 'carousel-bulletpagination',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'To enable bullet pagination.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),

			// Enable Arrow Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Arrow Pagination', 'dtlms'),
				'param_name' => 'carousel-arrowpagination',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'To enable arrow pagination.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),

			// Space Between Sliders
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Space Between Sliders','dtlms'),
				'param_name' => 'carousel-spacebetween',
				'description' => esc_html__( 'Space between sliders can be given here.', 'dtlms' ),	
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
			),								

		)
	) );
}
?>