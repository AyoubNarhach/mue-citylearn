<?php
namespace uncanny_learndash_reporting;

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! empty( TinCannyShortcode::$groups_query ) && 0 < count( TinCannyShortcode::$groups_query ) ) {
	?>
	<div class="reporting-group-selector" id="reporting-group-selector-container">
		<form method="GET" class="reporting-group-selector__form">
			<?php if ( is_admin() ) { ?>
				<input type="hidden" name="page"
					   value="<?php echo esc_attr( htmlspecialchars( ultc_get_filter_var( 'page', '' ) ) ); ?>">
			<?php } ?>

			<input type="hidden" name="tab"
				   value="<?php echo esc_attr( htmlspecialchars( ultc_get_filter_var( 'tab', 'courseReportTab' ) ) ); ?>">

			<div class="reporting-group-selector__label-container">
				<label for="reporting-group-selector">
					<?php echo esc_html( 'Groupe' ); ?>
				</label>
			</div>
			<div class="reporting-group-selector__select-container">
				<select name="group_id" id="reporting-group-selector" class="reporting-group-selector__select">
					<option value="all"><?php echo esc_html( 'Tous les utilisateurs' ); ?></option>
					<?php
					foreach ( TinCannyShortcode::$groups_query as $group ) {
						?>
						<option
							<?php
							if ( absint( $group->ID ) === absint( TinCannyShortcode::$isolated_group ) ) {
								echo 'selected="selected"';
							}
							?>
							value="<?php echo esc_attr( $group->ID ); ?>"><?php echo esc_html( $group->post_title ); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="reporting-group-selector__submit-container">
				<input value="<?php echo esc_html( 'Filtrer' ); ?>" type="submit"
					   id="reporting-group-selector__submit">
			</div>
		</form>
	</div>
	<?php
}
?>
