
<?php
$id = get_the_author_meta('ID');
$name  = get_the_author_meta('display_name', $id);
$email = get_the_author_meta('email', $id);
$heading = esc_html__("Posted by", 'knowherepro') ." ". $name;
$description = get_the_author_meta('description', $id);

if ( empty($description) ) return;

if ( current_user_can('edit_users') || get_current_user_id() == $id ) {
	$description .= " <a href='" . admin_url( 'profile.php?user_id=' . $id ) . "'>". esc_html__( '[ Edit the profile ]', 'knowherepro' ) ."</a>";
}
?>

<div class="kw-entry-author">

	<div class="kw-author-box">

		<div class="kw-avatar">
			<?php echo get_avatar($email, '100', '', esc_html($name)); ?>
		</div>

		<div class="kw-author-info">

			<div class="kw-author-name"><?php echo esc_html($heading); ?></div>

			<?php if ( !empty($description) ): ?>
				<p><?php echo sprintf('%s', $description); ?></p>
			<?php endif; ?>

			<?php
			$user_profile = new knowhere_admin_user_profile();
			echo $user_profile->output_social_links();
			?>

		</div><!--/ .kw-author-info-->

	</div><!--/ .kw-author-box-->

</div><!--/ .kw-entry-author-->
