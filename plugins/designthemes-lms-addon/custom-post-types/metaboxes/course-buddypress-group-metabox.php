<?php
global $post;

$course_group_id = get_post_meta( $post->ID, 'dtlms-course-group-id', true );
$groups_arr = BP_Groups_Group::get(array(
				'type' => 'alphabetical',
				'per_page' => 999
			));
?>

<p><?php esc_html_e( 'Choose buddypress group for this course.', 'dtlms' ); ?></p>
<select name="dtlms-course-group-id" id="dtlms-course-group-id" class="dtlms-chosen-select">
	<option value=""><?php esc_html_e( 'Select', 'dtlms' ); ?></option>
	<?php
	foreach ( $groups_arr[ 'groups' ] as $group ) {
		$group_status = groups_get_groupmeta( $group->id, 'dtlms-course-group-id', true );
		if ( !empty($group_status) && $course_group_id != $group->id ) {
			continue;
		}
		?>
        <option value="<?php echo $group->id; ?>" <?php echo (( $course_group_id == $group->id )) ? 'selected' : ''; ?>><?php _e( $group->name, 'dtlms' ); ?></option>
		<?php
	}
	?>
</select>