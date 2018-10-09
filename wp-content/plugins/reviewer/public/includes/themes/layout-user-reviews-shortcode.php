<!-- reviewer plugin user reviews -->
<div id="<?php echo $component_id; ?>" class="rwp-component rwp-rosu" data-user="<?php echo $user; ?>" data-order="<?php echo $order; ?>" data-limit="<?php echo $limit; ?>">

    <span class="rwp-rosu__loader" v-show="loading"></span>
    <p class="rwp-rosu__notice" v-if="!loading && !success && notice.length" v-text="notice"></p>

    <?php if( $show_stats ): ?>
    <div class="rwp-rosu__user" v-show="!loading && success" v-cloak>
      <a href="#" class="rwp-rosu__avatar">
        <img src="<?php echo $user_obj->avatar(160)?>" alt="Avatar" />
      </a>
      <div class="rwp-rosu__info">
        <span class="rwp-rosu__displayname"><?php echo $user_obj->display_name; ?></span>
        <ul class="rwp-rosu__badges">
          <li class="rwp-rosu__badge-icon-star">
            <i class="dashicons dashicons-star-filled"></i>
            <span><strong>{{ userStats.reviews }}</strong> <?php _e('reviews', 'reviewer') ?></span>
          </li>
           <li class="rwp-rosu__badge-icon-camera">
            <i class="dashicons dashicons-camera"></i>
            <span><strong>{{ userStats.photos }}</strong> <?php _e('photos', 'reviewer') ?></span>
          </li>
          <li class="rwp-rosu__badge-icon-thumbup">
            <i class="dashicons dashicons-thumbs-up"></i>
            <span><strong>{{ userStats.judgements }}</strong> <?php _e('received judgements', 'reviewer') ?></span>
          </li>
          <li class="rwp-rosu__badge-icon-verified">
            <i class="dashicons dashicons-awards"></i>
            <span><strong>{{ userStats.badges }}</strong> <?php _e('verified badges', 'reviewer') ?></span>
          </li>
        </ul>
      </div>
    </div>
    <!-- /user info -->
    <?php endif ?>

    <div class="rwp-rosu__seperator" v-show="!loading && success" v-cloak>
      <span><?php _e('Reviews', 'reviewer') ?></span>
    </div>

    <div class="rwp-rosu__review" v-for="review in reviews " v-show="!loading && success" v-cloak>
      <?php if( empty( $url ) ): ?>
      <a v-bind:href="review.post.permalink + '?rwpurid=' + review.id">
      <?php else: ?>
      <a href="<?php echo esc_url( $url ); ?>?rwpurid={{ review.id }}">
      <?php endif; ?>
        <i class="dashicons dashicons-share-alt2 rwp-rosu__review-show"></i>
      </a>

      <a v-bind:href="review.post.permalink" class="rwp-rosu__review-post" v-text="review.post.title"></a>

      <div class="rwp-rosu__review-score">

      <?php $numeric_rating = $this->preferences_field('preferences_numeric_rating_in_user_review', true); ?>
      <?php if( $numeric_rating === 'yes' ): ?>
        <div class="rwp-rosu__numeric-score">
          <span v-text="review.score"></span>
          <i v-cloak>/ {{ review.template.score.maximum }}</i>
        </div>
        <?php endif ?>

        <?php
					$mode = $this->preferences_field('preferences_rating_mode', true);
					switch ( $mode ) {
						case 'five_stars':
              echo'<rwp-score-5-star
                v-bind:score="review.score"
                v-bind:min="review.template.score.minimum"
                v-bind:max="review.template.score.maximum"
                v-bind:icon="review.template.score.icon"
              ></rwp-score-5-star>';
							break;

						case 'full_five_stars':
							echo'<rwp-score-star v-for="o in review.template.criteria.order"
                v-bind:score="review.criteria[o]"
                v-bind:min="review.template.score.minimum"
                v-bind:max="review.template.score.maximum"
                v-bind:label="review.template.criteria.labels[o]"
                v-bind:icon="review.template.score.icon"
              ></rwp-score-star>';
							break;

						default:
              echo'<rwp-score-bar v-for="o in review.template.criteria.order"
                v-bind:score="review.criteria[o]"
                v-bind:min="review.template.score.minimum"
                v-bind:max="review.template.score.maximum"
                v-bind:label="review.template.criteria.labels[o]"
                v-bind:low="review.template.score.percentages.low"
                v-bind:high="review.template.score.percentages.high"
                v-bind:color-low="review.template.score.colors.low"
                v-bind:color-medium="review.template.score.colors.medium"
                v-bind:color-high="review.template.score.colors.high"
              ></rwp-score-bar>';
							break;
					}
				?>
      </div>

      <span class="rwp-rosu__review-badge" v-if="review.verified" style="background: <?php echo $verified_badge['color'] ?>;"><?php echo $verified_badge['label'] ?></span>

      <span class="rwp-rosu__review-title" v-if="reviewHasField(review, 'rating_option_title')">{{{ review.title }}}</span>

      <p class="rwp-rosu__review-comment" v-if="reviewHasField(review, 'rating_option_comment')">{{{review.comment | nl2br}}}</p>

      <?php $imagesField = $this->preferences_field('preferences_user_review_images', true); if( $imagesField['field_enabled'] ): ?>
      <div class="rwp-rosu__review-images" v-if="review.images.length">
        <span v-for="image in review.images" v-bind:style="{ backgroundImage: 'url('+image.thumb_url+')', height: image.thumb_height + 'px', width: image.thumb_width + 'px' }" v-on:click.prevent="openPhotoSwipeGallery(review.images, $index)"></span>
      </div>
      <?php endif; ?>

      <span class="rwp-rosu__review-date" v-text="review.date.visual"></span>

    </div><!-- /review -->

</div>
<!-- reviewer plugin user reviews -->
