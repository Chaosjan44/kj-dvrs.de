<?php chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
$disheadercheck = true;
$user = check_user();
if (!isset($user)) {
    error_log("sdbgfvjhedsgfjhgsdgf");
    print("<script>location.href='/login.php'</script>");
}
if ($user['perm_admin'] != 1) {
    error('Unzureichende Berechtigungen!');
}

$stmt = $pdo->prepare('SELECT * FROM kolpingjugend');
$stmt->execute();
if ($stmt->rowCount() == 0) {
    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
}
$kjs = $stmt->fetchAll();
$total_kjs = $stmt->rowCount();
require_once("templates/header.php");
?>
<div class="container p-3">
    <div style="min-height: 75vh;">
        <div class="d-flex justify-content-between">
            <div class="col">
                <h1>Häuser der Kolpingjugenden</h1>
            </div>
        </div>
        <p><?php print($total_kjs); ?> Häuser</p>
        <div class="row row-cols-<?=isMobile() ? '1' : '4' ?> gy-4">
            <?php foreach ($kjs as $kj):
                $stmt = $pdo->prepare('SELECT * FROM houses WHERE kolpingjugend_id = ?');
                $stmt->bindValue(1, $kj['kolpingjugend_id'], PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() != 1) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }
                $house = $stmt->fetch();
                
                $stmt = $pdo->prepare('SELECT * FROM rooms WHERE house_id = ?');
                $stmt->bindValue(1, $house['house_id'], PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() < 16) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }
                $rooms = $stmt->fetchAll();
                $rooms_done = 0;
                foreach ($rooms as $room) {
                    if ($room['room_done'] == 1) {
                        $rooms_done += 1;
                    }
                }
            ?>
            <div class="col">
                <div class="card cbg2">
                    <div class="card-body">
                        <div class="card-title row">
                            <h4 class="col-9 ctext"><?=$house['house_name']?></h4>
                            <form action="houses.php" method="post" class="col-3 d-flex justify-content-end">
                                <div class="">
                                    <input type="number" value="<?=$kj['kolpingjugend_id']?>" name="kj_id" style="display: none;" required>
                                    <button type="submit" name="action" value="mod" class="btn btn-kolping"><i class="bi bi-pencil text-light"></i></button>
                                </div>
                            </form>
                        </div>
                        <span class="card-text ctext">
                            KJ: <?=$kj['kolpingjugend_name']?><br>
                            Adresse: <?=$house['house_address']?><br>
                            Räume: <?=$rooms_done?> / 16
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach?>
        </div>
    </div>
</div>

<?php
$disheadercheck = false;
require_once("templates/footer.php"); ?>