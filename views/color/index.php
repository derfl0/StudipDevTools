<form class="studip_form">
    <label>
        <?= _('Stud.IP Farbe') ?>
        <input name="color" type="color" value="<?= $color ?>">
    </label>
    <?= \Studip\Button::create(_('Ändern'), 'change'); ?>
    <?= \Studip\Button::create(_('Zurücksetzen'), 'reset'); ?>
</form>