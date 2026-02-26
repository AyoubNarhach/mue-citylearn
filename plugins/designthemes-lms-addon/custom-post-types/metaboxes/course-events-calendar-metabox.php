<?php
global $post;

$event_terms = get_terms('tribe_events_cat');
$course_event_catid = get_post_meta( $post->ID, 'dtlms-course-event-catid', true );

?>
<p><?php esc_html_e( 'Choose course event category for this course.', 'dtlms' ); ?></p>
<select name="dtlms-course-event-catid[]" id="dtlms-course-event-catid" class="dtlms-chosen-select" multiple>
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