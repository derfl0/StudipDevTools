<form>
    <input placeholder="MD5 ID" size="70" name="query" value="<?= htmlReady(Request::get('query')) ?>">
    <?= \Studip\Button::create('Suchen') ?>
</form>

<? if ($data): ?>
    <table class="default">
        <caption>
            Datensätze
        </caption>
        <tr>
            <th>
                Tabelle</th>
            <th>Spalte</th>
            <th>Id</th>
        </tr>
        <? foreach ($data as $entry): ?>

            <tr>
                <td><?= $entry['table'] ?></td>
                <td><?= $entry['column'] ?></td>
                <td><?= $entry['id'] ?></td>
            </tr>
        <? endforeach; ?>

    </table>
    <p><?= $time ?></p>
    <?

endif;
