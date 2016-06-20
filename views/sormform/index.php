<form class="studip_form" method='post'>
    <fieldset>
        <legend>Sorm</legend>
        <label>
            IncludePath
            <input type='text' name="path" value="<?= Request::get('path') ? : 'plugins_packages/origin/pluginclassname/models/' ?>">
        </label>
        <label>
            SimpleORMap
            <input type='text' name="sorm" value="<?= Request::get('sorm') ?>">
        </label>
        <label>
            Trails View Variable
            <input type='text' name="trailsview" value="<?= Request::get('trailsview') ?>">
        </label>
        <label>
            Request Variable
            <input type='text' name="request" value="<?= Request::get('request') ?>">
        </label>
        <?= $this->info ?>
    </fieldset>
    <? if ($metadata): ?>
        <fieldset>
            <legend>Form</legend>
            <? foreach ($metadata['fields'] as $field): ?>
                <fieldset>
                    <legend><?= $field['name'] ?></legend>
                    <label>
                        Volltext
                        <input type='text' name='fulltext[<?= $field['name'] ?>]' value='<?= $fulltext[$field['name']] ? : $field['name'] ?>'>
                    </label>
                    <label>
                        Platzhalter
                        <input type='text' name='placeholder[<?= $field['name'] ?>]' value='<?= $placeholder[$field['name']] ? : '' ?>'>
                    </label>
                    <label>
                        Typ
                        <select name='type[<?= $field['name'] ?>]'>
                            <option value='text' <?= $type[$field['name']] == 'text' ? 'selected' : '' ?>>Text</option>
                            <option value='hidden' <?= $type[$field['name']] == 'hidden' ? 'selected' : '' ?>>Ausgeblendet</option>
                            <option value='password' <?= $type[$field['name']] == 'password' ? 'selected' : '' ?>>Passwort</option>
                            <option value='checkbox' <?= $type[$field['name']] == 'checkbox' ? 'selected' : '' ?>>Checkbox</option>
                        </select>
                    </label>
                </fieldset>
            <? endforeach; ?>
        </fieldset>
    <? endif; ?>
    <?= Studip\Button::create('Aktualisieren') ?>
</form>


<? if($metadata): ?>
    <h1>Code</h1>
    <textarea style='width: 80%; height: 400px;'>
<form class='studip_form' method='post' action=''>
    <? foreach ($metadata['fields'] as $field): ?>
        <? if ($type[$field['name']]): ?>
            <?= $this->render_partial('sormform/'.$type[$field['name']], array(
                'fulltext' => $fulltext[$field['name']] ? : $field['name'],
                'name' => ((Request::get('request') ? : strtolower(Request::get('sorm')))).'['.$field['name'].']',
                'value' => Request::get('trailsview').'->'. $field['name'],
            )) ?>
        <? endif; ?>
    <? endforeach; ?>
</form>
</textarea>
<? endif; ?>
