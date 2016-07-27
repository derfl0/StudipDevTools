<h2>Backup erstellen</h2>
<a href="<?= $controller->url_for('dbdump/backup') ?>">Neues Backup erstellen</a>

<table class="default">
    <caption>
        Backups
    </caption>
    <thead>
    <tr>
        <th>Name</th>
        <th>Gr��e</th>
        <th>Datum</th>
        <th>Aktionen</th>
    </tr>
    </thead>
    <tbody>

    <? foreach ($backups as $bak): ?>
        <tr>
            <td><?= $bak['name'] ?></td>
            <td><?= $bak['size'] ?></td>
            <td><?= $bak['date'] ?></td>
            <td><a href="<?= $bak['link'] ?>">Wiederherstellen</a>
                <a href="<?= $bak['delete'] ?>">L�schen</a></td>
        </tr>
    <? endforeach; ?>
    </tbody>
</table>
