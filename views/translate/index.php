<form class="studip_form" method="post" action="<?= $controller->url_for('translate/extract/'.$currentPlugin['id']) ?>">
    <fieldset>

        <legend>
            <?= _('Translation Strings erneuern') ?>
        </legend>

<input type="hidden" name="pluginid" value="<?= $currentPlugin['id'] ?>">
    <label>
        <?= _('Sprachen (getrennt durch ;)') ?>
        <input type="text" name="lang" value="en" placeholder="<?= _('en') ?>">
    </label>

        <label>
            <?= _('Dateiname') ?>
            <input type="text" name="filename" value="<?= strtolower($currentPlugin['name']) ?>" placeholder="<?= strtolower($currentPlugin['name']) ?>">
        </label>

    <?= \Studip\Button::create(_('Erstellen'), 'extract') ?>
    </fieldset>
</form>