<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

    <h2>
        <?php foreach($this->tabs as $key => $tab){ ?>
            <a class="nav-tab <?php echo ($key == $current ? ' nav-tab-active' : ''); ?>" href="?page=rule_list&tab=<?php echo $key; ?>">
                <i class="fa"></i><?php echo $tab['name']; ?>
            </a>
        <?php }?>
    </h2>
