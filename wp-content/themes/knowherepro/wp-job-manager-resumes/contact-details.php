<?php
global $resume_preview;

if ( $resume_preview ) {
	return;
}

	?>
	<div class="resume_contact">
		<?php do_action( 'resume_manager_contact_details' ); ?>
	</div>