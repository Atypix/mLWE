<div class="bg-white">
    <input id="export" type="submit" class="button button-primary" value="Backup Now">
    <br><br>
    <div id="loadingDiv" style="display: none">
        <p><?php _e('Progress','wp-migration-duplicator') ?> :
            <progress id="bar" value="0" max="100"></progress>
        </p>
        <p>
            <span id="percentage"></span> %
        </p>
        <input style="background: red ; color: white" class="button" type="button" value="Cancel" onClick="window.location.reload()">
    </div>


    <table id="backups" border="1" width="100%">
        <tr>
            <th><?php _e('Name','wp-migration-duplicator') ?></th>
            <th><?php _e('Date','wp-migration-duplicator') ?></th>
            <th><?php _e('Size','wp-migration-duplicator') ?></th>
            <th><?php _e('Actions','wp-migration-duplicator') ?></th>
        </tr>
		<?php
		$webtoffee_migrations = WP_CONTENT_DIR."/webtoffee_migrations";
		if(!empty($webtoffee_migrations)) {
			foreach (new DirectoryIterator($webtoffee_migrations) as $file) {
				if ($file->isFile()) {
					?>

                    <tr>
                        <td>
                            <a style="text-decoration: none; font-weight: bold;text-transform: uppercase;" href="<?php echo content_url('webtoffee_migrations')  . $file->getFilename(); ?>">
								<?php echo $file->getFilename(); ?>
                            </a>
                        </td>
                        <td><?php echo get_date($file->getFilename()); ?></td>
                        <td><?php echo formatSizeUnits($file->getSize()) ?></td>
                        <td align="center">
                            <button  class="deleteButton" data-filename="<?php echo $file->getFilename(); ?>"><span
                                        class="dashicons dashicons-no"></span></button>
                        </td>
                    </tr>
					<?php
				}
			}
		}

		function get_date($name)
		{
			$name = substr($name, 0, 10);
			return date("d-M-Y", strtotime($name));
		}

		function formatSizeUnits($bytes)
		{
			if ($bytes >= 1073741824) {
				$bytes = number_format($bytes / 1073741824, 2) . ' GB';
			} elseif ($bytes >= 1048576) {
				$bytes = number_format($bytes / 1048576, 2) . ' MB';
			} elseif ($bytes >= 1024) {
				$bytes = number_format($bytes / 1024, 2) . ' KB';
			} elseif ($bytes > 1) {
				$bytes = $bytes . ' bytes';
			} elseif ($bytes == 1) {
				$bytes = $bytes . ' byte';
			} else {
				$bytes = '0 bytes';
			}
			return $bytes;
		}

		?>
    </table>
</div>




