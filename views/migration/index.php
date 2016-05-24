<form class="studip_form" method="post">
    <label>
        <?= _('Plugin') ?>
    <select name="plugin_id">
        <? foreach(PluginManager::getInstance()->getPluginInfos() as $plugin): ?>
            <option value="<?= $plugin['id'] ?>"><?= htmlReady($plugin['name'])?></option>
        <? endforeach ?>
    </select>
    </label>

    <label>
        <?= _('Tableprefix') ?>
        <input type="text" name="prefix" value="" placeholder="<?= _('myplugin_') ?>">
    </label>

    <label>
        <?= _('Migrationname') ?>
        <input type="text" name="migration" value="<?= _('1_setup_db') ?>" placeholder="<?= _('01_Setup_Db') ?>">
    </label>

    <?= \Studip\Button::create(_('Erstellen'), 'create') ?>
</form>