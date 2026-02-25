<?php
global $post;

$event_terms = get_terms('tribe_events_cat');
$course_event_catid = get_post_meta( $post->ID, 'dtlms-class-event-catid', true );

$class_title_singular = apply_filters( 'class_label', 'singular' );

?>
<p><?php echo sprintf( esc_html__('Choose event category for this %1$s', 'dtlms'), $class_title_singular ); ?></p>
<select name="dtlms-class-event-catid[]" id="dtlms-class-event-catid" class="dtlms-chosen-select" multiple>
    <option value=""><?php esc_html_e( 'None', 'dtlms' ); ?></option>
    <?php
    foreach ( $event_terms as $event_term ) {
		$sel_str = '';
		if(!empty($course_event_catid) && in_array($event_term->term_id, $course_event_catid)) {
			$sel_str = 'selected="selected"'; 
		}
		?>
		<option value="<?php echo $event_term->term_id; ?>" <?php echo $sel_str; ?>><?php esc_html_e( $event_term->name, 'dtlms' ); ?></option>
		<?php
    }
    ?>
</select>