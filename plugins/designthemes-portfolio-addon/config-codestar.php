<?php
if ( ! class_exists( 'DTPortfolioCodeStar' ) ) {

	class DTPortfolioCodeStar {

		function __construct() {

			# Framework Options
			add_filter( 'cs_framework_options', array( $this, 'dtportfolio_cs_framework_options' ) );

			# Metabox Options
			add_filter( 'cs_metabox_options', array( $this, 'dtportfolio_cs_metabox_options'), 1 );
		}

		function dtportfolio_cs_framework_options( $options ) {

			$options[] = array(
				'name'     => 'dtportfolio',
				'title'    => esc_html__( 'Portfolio', 'dtportfolio' ),
				'icon'     => 'fa fa-photo',
				'sections' => array(

					# Archive
						array (
							'name'   => 'portfolio-archive-pages',
							'title'  => esc_html__('Archive Pages', 'dtportfolio'),
							'icon'   => 'fa fa-photo',
							'fields' => array (

								array (
									'id'      => 'portfolio-archives-post-layout',
									'type'    => 'image_select',
									'title'   => esc_html__('Post Layout', 'dtportfolio'),
									'default' => 'dtportfolio-one-fourth-column',
									'options' => array(
										'dtportfolio-one-column'        => plugins_url('designthemes-portfolio-addon') . '/images/one-column.png',
										'dtportfolio-one-half-column'   => plugins_url('designthemes-portfolio-addon') . '/images/one-half-column.png',
										'dtportfolio-one-third-column'  => plugins_url('designthemes-portfolio-addon') . '/images/one-third-column.png',
										'dtportfolio-one-fourth-column' => plugins_url('designthemes-portfolio-addon') . '/images/one-fourth-column.png',
									),
								),

								array (
									'id'      => 'portfolio-hover-style',
									'type'    => 'select',
									'title'   => esc_html__('Hover Style', 'dtportfolio'),
									'class'   => 'chosen',
									'info'    => esc_html__('Choose post style to display archive page portfolio items.', 'dtportfolio'),
									'default' => '',
									'options' => array(
										''                    => esc_html__('Default','dtportfolio'), 
										'modern-title'        => esc_html__('Modern Title','dtportfolio'), 
										'title-icons-overlay' => esc_html__('Title & Icons Overlay','dtportfolio'), 
										'title-overlay'       => esc_html__('Title Overlay','dtportfolio'),
										'icons-only'          => esc_html__('Icons Only','dtportfolio'), 
										'classic'             => esc_html__('Classic','dtportfolio'), 
										'minimal-icons'       => esc_html__('Minimal Icons','dtportfolio'),
										'presentation'        => esc_html__('Presentation','dtportfolio'), 
										'girly'               => esc_html__('Girly','dtportfolio'), 
										'art'                 => esc_html__('Art','dtportfolio'), 
										'extended'            => esc_html__('Extended','dtportfolio'), 
										'boxed'               => esc_html__('Boxed','dtportfolio'), 
										'centered-box'        => esc_html__('Centered Box','dtportfolio'),
										'with-gallery-thumb'  => esc_html__('With Gallery Thumb','dtportfolio'), 
										'with-gallery-list'   => esc_html__('With Gallery List','dtportfolio'), 
										'grayscale'           => esc_html__('Grayscale','dtportfolio'), 
										'highlighter'         => esc_html__('Highlighter','dtportfolio'), 
										'with-details'        => esc_html__('With Details','dtportfolio'), 
										'bottom-border'       => esc_html__('Bottom Border','dtportfolio'),
										'with-intro'          => esc_html__('With Intro','dtportfolio')
									),
								),

								array (
									'id'      => 'portfolio-cursor-hover-style',
									'type'    => 'select',
									'title'   => esc_html__('Cursor Hover Style', 'dtportfolio'),
									'class'   => 'chosen',
									'info'    => esc_html__('Choose cursor hover style for your archive page portfolio items.', 'dtportfolio'),
									'default' => '',
									'options' => array(
										''                    => esc_html__('Default','dtportfolio'), 
										'cursor-hover-style1' => esc_html__('Style 1','dtportfolio'), 
										'cursor-hover-style2' => esc_html__('Style 2','dtportfolio') ,
										'cursor-hover-style3' => esc_html__('Style 3','dtportfolio'),
										'cursor-hover-style4' => esc_html__('Style 4','dtportfolio'),
										'cursor-hover-style5' => esc_html__('Style 5','dtportfolio'),
										'cursor-hover-style6' => esc_html__('Style 6','dtportfolio'), 
									),
								),

								array (
									'id'    => 'portfolio-grid-space',
									'type'  => 'switcher',
									'title' => esc_html__('Allow Grid Space', 'dtportfolio'),
									'label' => esc_html__('YES! to allow grid space', 'dtportfolio')
								),

								array (
									'id'    => 'portfolio-allow-full-width',
									'type'  => 'switcher',
									'title' => esc_html__('Allow Full Width', 'dtportfolio'),
									'label' => esc_html__('YES! to allow full width', 'dtportfolio')
								),

								array (
									'id'    => 'portfolio-post-per-page',
									'type'  => 'number',
									'title' => esc_html__('Post Per Page', 'dtportfolio'),
								),

								array (
									'id'    => 'portfolio-disable-item-options',
									'type'  => 'switcher',
									'title' => esc_html__('Disable Individual Portfolio Item Options', 'dtportfolio'),
									'label' => esc_html__('YES! to disable individual portfolio item options.', 'dtportfolio')
								),
							),
						),
					# Archive
					
					# Permalinks
						array (
							'name'   => 'portfolio-permalinks',
							'title'  => esc_html__('Permalinks', 'dtportfolio'),
							'icon'   => 'fa fa-photo',
							'fields' => array (
								array (
									'id'    => 'single-portfolio-slug',
									'type'  => 'text',
									'title' => esc_html__('Single Portfolio Slug', 'dtportfolio'),
									'after' => '<p class="cs-text-info">'.esc_html__('Do not use characters not allowed in links. Use, eg. portfolio-item ', 'dtportfolio').'<br> <b>'.esc_html__('After made changes save permalinks.', 'dtportfolio').'</b></p>',
								),
								array (
									'id'    => 'portfolio-category-slug',
									'type'  => 'text',
									'title' => esc_html__('Portfolio Category Slug', 'dtportfolio'),
									'after' => '<p class="cs-text-info">'.esc_html__('Do not use characters not allowed in links. Use, eg. portfolio-types ', 'dtportfolio').'<br> <b>'.esc_html__('After made changes save permalinks.', 'dtportfolio').'</b></p>',
								),
							)
						),
					# Permalinks
					
					# Custom Fields
						array (
							'name'   => 'portfolio-custom-fields',
							'title'  => esc_html__('Custom Fields', 'dtportfolio'),
							'icon'   => 'fa fa-photo',
							'fields' => array (
								array (
									'id'              => 'portfolio-custom-fields',
									'type'            => 'group',
									'title'           => esc_html__('Custom Fields', 'dtportfolio'),
									'info'            => esc_html__('Click button to add custom fields like name, url and date etc', 'dtportfolio'),
									'button_title'    => esc_html__('Add New Field', 'dtportfolio'),
									'accordion_title' => esc_html__('Adding New Custom Field', 'dtportfolio'),
									'fields'          => array (
										array (
											'id'    => 'portfolio-custom-fields-text',
											'type'  => 'text',
											'title' => esc_html__('Enter Text', 'dtportfolio')
										),
									)
								),
							)
						),
					# Custom Fields
					
				)
			);

			// Filter to add additional options for themes
			$options = apply_filters( 'dtportfolio_template_framework_options', $options );

			return $options;			
		}

		function dtportfolio_cs_metabox_options( $options ) {

			$options[] = array (
				'id'        => 'dtportfolio_template_settings',
				'title'     => esc_html__('Portfolio Addon Options', 'dtportfolio'),
				'post_type' => 'page',
				'context'   => 'normal',
				'priority'  => 'high',
				'sections'  => array (
					array (
						'name'  => 'dtportfolio_footer_section',
						'fields' => array (
							array (
								'type' 	=> 'switcher',
								'id' 	=> 'dtportfolio-transparent-header',
								'title' => esc_html__('Enable Transparent Header', 'dtportfolio'),
								'desc'  => esc_html__('If you wish you can enable transparent header for your theme.', 'dtportfolio'),
							),
							array (
								'type' 	=> 'switcher',
								'id' 	=> 'dtportfolio-remove-spaces',
								'title' => esc_html__('Remove Additional Spaces', 'dtportfolio'),
								'desc'  => esc_html__('This option is usefull if u like to keep any item in fullscreen.', 'dtportfolio'),
							),							
						)
					)
				)
			);

			return $options;
		}
	}
}