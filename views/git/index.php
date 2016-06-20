<form class="studip_form">

    <label>
        <?= _('Git') ?>
        <input type="text" size="50" name="git" placeholder="<?= _('Git') ?>">
    </label>

    <label>
        <input type="checkbox" name="register" checked>
        <?= _('Registrieren') ?>
    </label>

    <label>
        <input type="checkbox" name="activate" checked>
        <?= _('Aktivieren') ?>
    </label>

    <?= \Studip\Button::create('Laden') ?>
</form>
<?= $this->answer ?>