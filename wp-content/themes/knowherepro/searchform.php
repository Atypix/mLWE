<form class="kw-lineform" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="s" class="screen-reader-text"></label>
	<input type="text" autocomplete="off" id="s" name="s" placeholder="<?php esc_attr_e( 'Search', 'knowherepro' ) ?>" value="<?php echo get_search_query(); ?>">
	<button class="kw-lineform-btn" type="submit"><?php esc_html_e('Search', 'knowherepro') ?></button>
</form>