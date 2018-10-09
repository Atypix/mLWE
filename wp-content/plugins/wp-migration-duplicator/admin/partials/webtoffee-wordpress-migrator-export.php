<style>
    progress {
        color: #0063a6;
        font-size: .6em;
        line-height: 1.5em;
        text-indent: .5em;
        width: 140em;
        height: 4em;
        border: 1px solid #0063a6;

    }
</style>
<div class="bg-white">

    <div class="migrator-export">
        <form id="migrator_export" method="post" action="options.php">
            <div class="fields">
                <div class="filters">
                    <p>
			<span>
				<strong><?php _e('Find','wp-migration-duplicator') ?></strong>
				<small class="" title="Search the database for this text">&lt;<?php _e('text','wp-migration-duplicator') ?>&gt;</small>
				<strong> <?php _e('Replace with','wp-migration-duplicator') ?></strong>
				<small class="" title="<?php _e('Replace the database with this text','wp-migration-duplicator') ?>">&lt;<?php _e('another text','wp-migration-duplicator') ?>&gt;</small>
				<strong><?php _e('in the database','wp-migration-duplicator') ?></strong>
			</span>
                    </p>
                    <div>
                        <input class="find" type="text" placeholder="Find"
                               name="find[]">
                        <input class="replace" type="text" placeholder="Replace with"
                               name="replace[]">
                        <button class="addfilter">
                            <span class="dashicons dashicons-plus-alt"></span>
                        </button>

                    </div>
                </div>
            </div>
            <br>
            <input id="export" type="submit" class="button button-primary" value="<?php _e('Export', 'wp-migration-duplicator');?>">
        </form>
    </div>

    <br>
        <h3  id="progress_info"></h3>

    <div id="loadingDiv" style="display: none">
        <p>Progress:
            <progress id="bar" value="0" max="100"></progress>
        </p>
        <p>
            <span id="percentage"></span> %
        </p>
        <input style="background: red ; color: white" class="button" type="button" value="Cancel" onClick="window.location.reload()">
    </div>
</div>