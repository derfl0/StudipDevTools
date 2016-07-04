<form class="studip_form">
    <fieldset>
        <legend>
            Datenbank anlegen
        </legend>

    <label>
        Name
        <input name="dbname">
    </label>

    <label>
        <input type="checkbox" name="demo" CHECKED value="1">
        Demo Daten einspielen
    </label>

        <?= \Studip\Button::create('Anlegen') ?>

    </fieldset>
</form>