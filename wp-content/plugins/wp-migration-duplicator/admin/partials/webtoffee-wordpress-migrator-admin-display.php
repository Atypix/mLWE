<div class="wrap">
    <div id="alerts">

    </div>
    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e('WordPress Migrator','wp-migration-duplicator') ?></h2>
    <?php settings_errors(); ?>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=webtoffee-wordpress-migrator&tab=export_migrations') ?>"
           class="nav-tab  <?php echo ($tab == 'export_migrations') ? 'nav-tab-active' : ''; ?> "> <?php _e('Export', 'wp-migration-duplicator'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=webtoffee-wordpress-migrator&tab=import_migrations') ?>"
           class="nav-tab <?php echo ($tab == 'import_migrations') ? 'nav-tab-active' : ''; ?> "> <?php _e('Import', 'wp-migration-duplicator'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=webtoffee-wordpress-migrator&tab=backup_migrations') ?>"
           class="nav-tab <?php echo ($tab == 'backup_migrations') ? 'nav-tab-active' : ''; ?> "> <?php _e('Backup', 'wp-migration-duplicator'); ?></a>
    <!--    <a href="<?php /*echo admin_url('admin.php?page=webtoffee-wordpress-migrator&tab=help_migrations') */?>"
           class="nav-tab <?php /*echo ($tab == 'help_migrations') ? 'nav-tab-active' : ''; */?> " > <?php /*_e('Help', 'wp-migration-duplicator'); */?></a>
    --></h2>


</div>
<div class="wrap">
    <?php
    switch ($tab) {
        case "export_migrations" :
            $this->export_migrations();
            break;
        case "import_migrations" :
            $this->import_migrations();
            break;
        case "backup_migrations" :
            $this->backup_migrations();
            break;
        case "help_migrations" :
            $this->help_migrations();
            break;
        default :
            $this->export_migrations();
            break;
    }
    ?>
</div>

