<div class="wrap">
    <h2>Simple AutoPOP</h2>
    <form method="post" action="options.php">
        <?php @settings_fields(Simple_AutoPOP::PREFIX . '-group'); ?>
        <?php @do_settings_fields(Simple_AutoPOP::PREFIX . '-group', 'default'); ?>

        <?php do_settings_sections(Simple_AutoPOP::PREFIX . '_general'); ?>
        <?php @submit_button(); ?>
    </form>
</div>