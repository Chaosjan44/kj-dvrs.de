<?php chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
$disheadercheck = true;
$user = check_user();
if (!isset($user) || $user != true) {
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

if(isset($_POST['action'])) {
    // Wenn action "mod" ist
    if($_POST['action'] == 'mod') {
        // Zeigt die Error Seite wenn der User keine Berechtigungen hat
        if ($user['perm_admin'] != 1) {
            error('Unzureichende Berechtigungen!');
        }
        // Wenn action "set_done" ist
        if($_POST['todo'] == 'set_done') {
            $stmt = $pdo->prepare("UPDATE rooms SET room_done = 1 WHERE room_id = ?");
            $stmt->bindValue(1, $_POST['room_id']);
            $result = $stmt->execute();
            if (!$result) {
                error('Datenbank Fehler!', pdo_debugStrParams($stmt));
            }
        }
        if($_POST['todo'] == 'set_undone') {
            $stmt = $pdo->prepare("UPDATE rooms SET room_done = 0 WHERE room_id = ?");
            $stmt->bindValue(1, $_POST['room_id']);
            $result = $stmt->execute();
            if (!$result) {
                error('Datenbank Fehler!', pdo_debugStrParams($stmt));
            }
        }
        $stmt = $pdo->prepare('SELECT * FROM houses WHERE kolpingjugend_id = ?');
        $stmt->bindValue(1, $_POST['kj_id'], PDO::PARAM_INT);
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
        $kj = $kjs[$_POST['kj_id']];
        ob_start();
        require_once("templates/header.php"); 
        $buffer=ob_get_contents();
        ob_end_clean();
        $title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - ".$house['house_name'];
        $buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
        echo $buffer;?>
        <div class="container p-3">
            <div style="min-height: 75vh;">
                <div class="d-flex justify-content-between">
                    <div class="col">
                        <h1><?=$house['house_name']?></h1>
                    </div>
                </div>
                <p>Räume Erledigt: <?php print($rooms_done);?></p>
                <div class="row row-cols-1 gy-4">
                    <?php foreach ($rooms as $room): ?>
                        <div class="col">
                            <div class="card cbg2">
                                <div class="card-body p-3">
                                    <h4 class="ctext card-title"><?=$room['room_name']?></h4>
                                    <div class="card-text">
                                        <div class="row">
                                            <div class="col-6 justify-content-start">
                                                <?php if ($room['room_done'] == 1) print('<button class="btn btn-success ctext m-0">Erledigt</button>');
                                                else if ($room['room_solved'] == 1) print('<button class="btn btn-kolping ctext m-0">Eingereicht</button>');
                                                else print('<button class="btn btn-secondary ctext m-0">Offen</button>');?>
                                            </div>
                                            <form action="houses.php" method="post" class="col-6 d-flex justify-content-end">
                                                <div class="">
                                                    <input type="number" value="<?=$kj['kolpingjugend_id']?>" name="kj_id" style="display: none;" required>
                                                    <input type="number" value="<?=$room['room_id']?>" name="room_id" style="display: none;" required>
                                                    <?php if ($room['room_done'] == 0) print('<input type="text" value="set_done" name="todo" style="display: none;" required><button type="submit" name="action" value="mod" class="btn btn-kolping ctext">Erledigt setzen</button>');
                                                        else print('<input type="text" value="set_undone" name="todo" style="display: none;" required><button type="submit" name="action" value="mod" class="btn btn-kolping ctext">Offen setzen</button>');
                                                    ?>
                                                </div>
                                            </form>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach?>
                </div>
                <div class="justify-content-center d-flex mt-4">
                    <button class="btn btn-danger m-0" aria-label="Abbrechen" onclick="window.location.href = '/admin/houses.php';"><i class="bi bi-x-circle text-light"></i></button>
                </div>
            </div>
        </div>

        <?php
        $disheadercheck = false;
        require_once("templates/footer.php");
        exit;
    }
}






ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Häuser";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
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