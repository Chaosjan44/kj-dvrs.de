<?php
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
$user = check_user();
$disheadercheck = true;
if (!isset($user) || $user != true) {
    print("<script>location.href='/login.php'</script>");
}
if ($user['perm_admin'] != 1) {
    error('Unzureichende Berechtigungen!');
}
$stmt = $pdo->prepare('SELECT * FROM kolpingjugend ORDER BY kolpingjugend_id');
$result = $stmt->execute();
if (!$result) {
    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
}
$total_kj = $stmt->rowCount();
$kolpingjugenden = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(isset($_POST['action'])) {
    if ($_POST['action'] == 'deleteconfirm') {
        if ($user['perm_admin'] != 1) {
            error('Unzureichende Berechtigungen!');
        }
        if(isset($_POST['kj_id']) and !empty($_POST['kj_id'])) {
            if ($_POST['kj_id'] != 0) {
                $stmt = $pdo->prepare('UPDATE users SET kolpingjugend_id = 0 WHERE kolpingjugend_id = ?');
                $stmt->bindValue(1, $_POST['kj_id'], PDO::PARAM_INT);
                $result2 = $stmt->execute();
                if (!$result2) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }

                $stmt = $pdo->prepare('SELECT house_id FROM houses WHERE kolpingjugend_id = ?');
                $stmt->bindValue(1, $_POST['kj_id'], PDO::PARAM_INT);
                $result3 = $stmt->execute();
                if (!$result3) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }
                $house_id = $stmt->fetch();

                $stmt = $pdo->prepare('SELECT room_id FROM rooms WHERE house_id = ?');
                $stmt->bindValue(1, $house_id['house_id'], PDO::PARAM_INT);
                $result3 = $stmt->execute();
                if (!$result3) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }
                $rooms = $stmt->fetchAll();
                foreach ($rooms as $room) {
                    $stmt = $pdo->prepare('DELETE FROM solution_pics WHERE room_id = ?');
                    $stmt->bindValue(1, $room['room_id'], PDO::PARAM_INT);
                    $result3 = $stmt->execute();
                    if (!$result3) {
                        error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                    }
                }

                $stmt = $pdo->prepare('DELETE FROM rooms WHERE house_id = ?');
                $stmt->bindValue(1, $house_id['house_id'], PDO::PARAM_INT);
                $result3 = $stmt->execute();
                if (!$result3) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }

                $stmt = $pdo->prepare('DELETE FROM houses WHERE kolpingjugend_id = ?');
                $stmt->bindValue(1, $_POST['kj_id'], PDO::PARAM_INT);
                $result3 = $stmt->execute();
                if (!$result3) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }


                $stmt = $pdo->prepare('DELETE FROM kolpingjugend WHERE kolpingjugend_id = ?');
                $stmt->bindValue(1, $_POST['kj_id'], PDO::PARAM_INT);
                $result4 = $stmt->execute();
                if (!$result4) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }
                echo("<script>location.href='kolpingjugenden.php'</script>");
                exit;
            }
        }
    }
    // Wenn action "mod" ist
    if($_POST['action'] == 'mod') {
        // Zeit die Error Seite wenn der User keine Berechtigungen hat
        if ($user['perm_admin'] != 1 && $_POST['kj_id'] != 0) {
            error('Unzureichende Berechtigungen!');
        }
        // Ziehe alle Daten zu gegebenen User aus der Datenbank
        $stmt = $pdo->prepare('SELECT * FROM kolpingjugend WHERE kolpingjugend_id = ?');
        error_log( $_POST['kj_id']);
        $stmt->bindValue(1, $_POST['kj_id']);
        $result1 = $stmt->execute();
        if (!$result1) {
            error('Datenbank Fehler!', pdo_debugStrParams($stmt));
        }
        $kolpingjugend2 = $stmt->fetch();
        if(isset($_POST['kj_name']) and isset($_POST['kj_ort']) and !empty($_POST['kj_name']) and !empty($_POST['kj_ort'])) {
            $stmt = $pdo->prepare("UPDATE kolpingjugend SET kolpingjugend_name = ?, kolpingjugend_ort = ? WHERE kolpingjugend_id = ?");
            $stmt->bindValue(1, $_POST['kj_name']);
            $stmt->bindValue(2, $_POST['kj_ort']);
            $stmt->bindValue(3, $_POST['kj_id']);
            $result5 = $stmt->execute();
            if (!$result5) {
                error('Datenbank Fehler!', pdo_debugStrParams($stmt));
            }     
            echo("<script>location.href='kolpingjugenden.php'</script>");
            exit;
        } else {
            ob_start();
            require_once("templates/header.php"); 
            $buffer=ob_get_contents();
            ob_end_clean();
    
            $title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Kolpingjugend bearbeiten";
            $buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
            echo $buffer;
        ?>
        <!-- Formular zur Bearbeitung des Users anzeigen -->
        <div class="p-3">
            <div style="min-height: 75vh;">
                <form action="kolpingjugenden.php" method="post">
                    <div class="container cbg2 p-3 rounded">
                        <h1 class="text-center text-kolping-orange pb-2">Einstellungen für <?=$kolpingjugend2['kolpingjugend_name']?></h1>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputVorname" name="kj_name" type="text" placeholder="Kolpingjugend Name" value="<?=$kolpingjugend2['kolpingjugend_name']?>" required>
                            <label for="inputVorname" class="text-dark">Kolpingjugend Name</label>
                        </div>
                        <div class="form-floating my-3">
                            <input class="form-control" id="inputNachname" name="kj_ort" type="text" placeholder="Kolpingjugend Ort" value="<?=$kolpingjugend2['kolpingjugend_ort']?>" required>
                            <label for="inputNachname" class="text-dark">Kolpingjugend Ort</label>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <input type="number" value="<?=$kolpingjugend2['kolpingjugend_id']?>" name="kj_id" style="display: none;" required>
                            <button type="submit" name="action" value="mod" class="btn btn-success mx-2" aria-label="Speichern"><i class="bi bi-sd-card text-light"></i></button>
                            <button class="btn btn-danger mx-2" aria-label="Abbrechen" onclick="window.location.href = '/admin/kolpingjugenden.php';"><i class="bi bi-x-circle text-light"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php 
        include_once("templates/footer.php");
        exit;
        } 
    }
    if ($_POST['action'] == 'cancel') {
        echo("<script>location.href='kolpingjugenden.php'</script>");
        exit;
    }
}
ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Kolpingjugenden";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
?>
<div class="container p-3">
    <div style="min-height: 75vh;">
        <div class="d-flex justify-content-between">
            <div class="col-4">
                <h1>Kolpingjugenden</h1>
            </div>
            <div class="col-4 d-flex justify-content-end">
                <div>
                    <button class="btn btn-success" onclick="window.location.href = 'registerkj.php';"><i class="bi bi-plus-circle text-light"></i></button>
                </div>
            </div>
        </div>
        <p><?php print($total_kj); ?> Kolpingjugenden</p>
        <div class="row row-cols-<?=isMobile() ? '1' : '4' ?> gy-4">
            <?php foreach ($kolpingjugenden as $kolpingjugend1): ?>
                <div class="col">
                    <div class="card cbg2">
                        <div class="card-body">
                            <h3 class="card-title ctext text-center"><?=$kolpingjugend1['kolpingjugend_name']?></h3>
                            <div class="card-text">
                                Kolpingjugend ID: <?=$kolpingjugend1['kolpingjugend_id']?><br>
                                Kolpingjugend Ort: <?=$kolpingjugend1['kolpingjugend_ort']?><br>
                                Erstellt: <?=$kolpingjugend1['created_at']?>
                                <?php if ($kolpingjugend1['kolpingjugend_id'] != 0):?>
                                    <form action="kolpingjugenden.php" method="post" class="d-flex justify-content-between mt-2">
                                        <div class="">
                                            <input type="number" value="<?=$kolpingjugend1['kolpingjugend_id']?>" name="kj_id" style="display: none;" required>
                                            <button type="submit" name="action" value="mod" class="btn btn-kolping"><i class="bi bi-pencil text-light"></i></button>
                                        </div>
                                        <div class="">
                                            <input type="number" value="<?=$kolpingjugend1['kolpingjugend_id']?>" name="kj_id" style="display: none;" required>
                                            <button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas<?=$kolpingjugend1['kolpingjugend_id']?>" aria-controls="offcanvas<?=$kolpingjugend1['kolpingjugend_id']?>"><i class="bi bi-trash3 text-light"></i></button>
                                            <div class="offcanvas offcanvas-end cbg" data-bs-scroll="true" tabindex="-1" id="offcanvas<?=$kolpingjugend1['kolpingjugend_id']?>" aria-labelledby="offcanvas<?=$kolpingjugend1['kolpingjugend_id']?>Label">
                                                <div class="offcanvas-header">
                                                    <h2 class="offcanvas-title ctext" id="offcanvas<?=$kolpingjugend1['kolpingjugend_id']?>Label">Wirklich Löschen?</h2>
                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                </div>
                                                <div class="offcanvas-body">
                                                    <span class="pb-3">Alle Daten der Kolpingjugend werden an die KJ DVRS gegeben.<br>Eine Löschung lässt sich nicht rückgängig machen!<br></span>
                                                    <button class="btn btn-success mx-2" type="submit" name="action" value="deleteconfirm">Ja</button>
                                                    <button class="btn btn-danger mx-2" type="button" data-bs-dismiss="offcanvas" aria-label="Close">Nein</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div> 
<?php 
$disheadercheck = false;
require_once("templates/footer.php"); 
?>