<?php extract(wp_parse_args($instance, $this->defaults)); ?>

	<p>
		<label for="<?php $this->field_id('title'); ?>"><?php esc_html_e('Title', 'knowherepro'); ?>
			<input class="widefat" id="<?php $this->field_id('title'); ?>" name="<?php $this->field_name('title') ?>" type="text" value="<?php echo esc_attr( $title ) ?>" />
		</label>
	</p>

	<div class="popw-tabs">

		<h4 class="popw-collapse"><?php esc_html_e( 'Type:', 'knowherepro'); ?><span></span></h4>
		<div style="display: block" class="popw-inner sort-type">
			<p>
				<label for="<?php $this->field_id('type-popular'); ?>">
					<input id="<?php $this->field_id('type-popular'); ?>" name="<?php $this->field_name('type'); ?>" value="popular" type="radio" <?php checked( $type, 'popular' ) ?> />
					<abbr title="Display the most viewed posts"><?php esc_html_e('Popular', 'knowherepro')?></abbr>
				</label> <br /><small><?php esc_html_e( 'Display the most viewed posts', 'knowherepro') ?></small><br />

				<label for="<?php $this->field_id( 'type-latest' )?>">
					<input id="<?php $this->field_id( 'type-latest' )?>" name="<?php $this->field_name('type'); ?>" value="latest" type="radio" <?php checked( $type, 'latest' ) ?> />
					<abbr title="Display the latest posts"><?php esc_html_e( 'Latest', 'knowherepro' )?></abbr>
				</label><br /><small><?php esc_html_e( 'Display the latest posts', 'knowherepro' ) ?></small>
			</p>
		</div>

	</div>

	<div class="popw-tabs <?php echo ($type == 'latest') ? 'disabled' : '' ?>" data-tab="calculate">
		<h4 class="popw-collapse"><?php esc_html_e('Calculate:', 'knowherepro')?><span></span></h4>
		<div class="popw-inner">
			<p>
				<label for="<?php $this->field_id('calculate-views'); ?>">
					<input id="<?php $this->field_id('calculate-views'); ?>" name="<?php $this->field_name('calculate'); ?>" value="views" type="radio" <?php checked($calculate, 'views') ?> />
					<abbr title="Every time the user views the page"><?php esc_html_e('Views', 'knowherepro'); ?></abbr>
				</label><br /><small><?php esc_html_e('Every time user views the post.', 'knowherepro'); ?></small><br />

				<label for="<?php $this->field_id('calculate-visits'); ?>">
					<input id="<?php $this->field_id('calculate-visits'); ?>" name="<?php $this->field_name('calculate'); ?>" value="visits" type="radio" <?php checked($calculate, 'visits') ?> />
					<abbr title="Every time the user visits the site"><?php esc_html_e('Visits', 'knowherepro'); ?></abbr>
				</label><br /><small><?php esc_html_e('Calculate only once per visit.', 'knowherepro'); ?></small>
			</p>
		</div>
	</div>

<?php do_action( 'pop_admin_form' ) ?>