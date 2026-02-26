<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Field: Upload
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class CSFramework_Option_upload extends CSFramework_Options {

  public function __construct( $field, $value = '', $unique = '' ) {
    parent::__construct( $field, $value, $unique );
  }

  public function output() {

    
    echo apply_filters( 'cs_element_before', $this->element_before() );

    if( isset( $this->field['settings'] ) ) { extract( $this->field['settings'] ); }

    $upload_type  = ( isset( $upload_type  ) ) ? $upload_type  : 'image';
    $button_title = ( isset( $button_title ) ) ? $button_title : esc_html__( 'Upload', 'elearni' );
    $frame_title  = ( isset( $frame_title  ) ) ? $frame_title  : esc_attr__( 'Upload', 'elearni' );
    $insert_title = ( isset( $insert_title ) ) ? $insert_title : esc_attr__( 'Use Image', 'elearni' );

    echo '<input type="text" name="'. $this->element_name() .'" value="'. $this->element_value() .'"'. $this->element_class() . $this->element_attributes() .'/>';
    echo '<a href="#" class="button cs-add" data-frame-title="'. $frame_title .'" data-upload-type="'. $upload_type .'" data-insert-title="'. $insert_title .'">'. $button_title .'</a>';

    echo apply_filters( 'cs_element_after', $this->element_after() );
  }
}
