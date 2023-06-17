<?php
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
$user = check_user();
$disheadercheck = true;
if (!isset($user)) {
    print("<script>location.href='/login.php'</script>");
}
if ($user['perm_admin'] != 1) {
    error('Unzureichende Berechtigungen!');
}
$stmt = $pdo->prepare('SELECT * FROM users ORDER BY user_id');
$result = $stmt->execute();
if (!$result) {
    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
}
$total_users = $stmt->rowCount();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(isset($_POST['action'])) {
    if ($_POST['action'] == 'deleteconfirm') {
        if ($user['perm_admin'] != 1) {
            error('Unzureichende Berechtigungen!');
        }
        if(isset($_POST['user_id']) and !empty($_POST['user_id'])) {
            if ($_POST['user_id'] != 0) {
                $stmt = $pdo->prepare('DELETE FROM securitytokens WHERE user_id = ?');
                $stmt->bindValue(1, $_POST['user_id'], PDO::PARAM_INT);
                $result = $stmt->execute();
                if (!$result) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }

                $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
                $stmt->bindValue(1, $_POST['user_id'], PDO::PARAM_INT);
                $result = $stmt->execute();
                if (!$result) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }
                echo("<script>location.href='user.php'</script>");
                exit;
            }
        }
    }
    // Wenn action "mod" ist
    if($_POST['action'] == 'mod') {
        // Zeit die Error Seite wenn der User keine Berechtigungen hat
        if ($user['perm_admin'] != 1 && $_POST['user_id'] != 0) {
            error('Unzureichende Berechtigungen!');
        }
        // Ziehe alle Daten zu gegebenen User aus der Datenbank
        $stmt = $pdo->prepare('SELECT * FROM users where user_id = ?');
        $stmt->bindValue(1, $_POST['user_id'], PDO::PARAM_INT);
        $result = $stmt->execute();
        if (!$result) {
            error('Datenbank Fehler !', pdo_debugStrParams($stmt));
        }
        $user1 = $stmt->fetch();

        $stmt = $pdo->prepare('SELECT * FROM kolpingjugend ORDER BY kolpingjugend_name');
        $result = $stmt->execute();
        if (!$result) {
            error('Datenbank Fehler!', pdo_debugStrParams($stmt));
        }
        $kolpingjugenden = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(isset($_POST['vorname']) and isset($_POST['nachname']) and isset($_POST['passwortNeu']) and isset($_POST['passwortNeu2']) and !empty($_POST['vorname']) and !empty($_POST['nachname'])) {
            $stmt = $pdo->prepare("UPDATE users SET vorname = ?, nachname = ?, perm_login = ?, perm_admin = ?, perm_edit_kj = ?, email = ?, kolpingjugend_id = ? WHERE user_id = ?");
            $stmt->bindValue(1, $_POST['vorname']);
            $stmt->bindValue(2, $_POST['nachname']);
            $stmt->bindValue(3, (isset($_POST['perm_login']) ? "1" : "0"), PDO::PARAM_INT);
            $stmt->bindValue(4, (isset($_POST['perm_admin']) ? "1" : "0"), PDO::PARAM_INT);
            $stmt->bindValue(5, (isset($_POST['perm_edit_kj']) ? "1" : "0"), PDO::PARAM_INT);
            $stmt->bindValue(6, $_POST['email']);
            $stmt->bindValue(7, $_POST['kj_id']);
            $stmt->bindValue(8, $_POST['user_id'], PDO::PARAM_INT);
            $result = $stmt->execute();
            if (!$result) {
                error('Datenbank Fehler!', pdo_debugStrParams($stmt));
            }
            // Überprüfe ob die eingegebenen Passwörter übereinstimmen
            if($_POST['passwortNeu'] == $_POST['passwortNeu2']) {
                // überprüft das die Passwörter nicht leer sind
                if (!empty($_POST['passwortNeu']) and !empty($_POST['passwortNeu2'])) {
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                    $stmt->bindValue(1, password_hash($_POST['passwortNeu'], PASSWORD_DEFAULT));
                    $stmt->bindValue(2, $_POST['user_id'], PDO::PARAM_INT);
                    $result = $stmt->execute();
                    if (!$result) {
                        error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                    }                    
                }
            } else {
                error('Passwörter stimmen nicht überein!');
            }
            echo("<script>location.href='user.php'</script>");
            exit;
        } else {
            ob_start();
            require_once("templates/header.php"); 
            $buffer=ob_get_contents();
            ob_end_clean();
    
            $title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Anwender*in bearbeiten";
            $buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
            echo $buffer;
        ?>
        <!-- Formular zur Bearbeitung des Users anzeigen -->
        <div class="container p-3">
            <div style="min-height: 75vh;">
                <form action="user.php" method="post">
                    <div class="card cbg2">
                        <div class="card-body">
                            <h3 class="card-title ctext text-center">Einstellungen für <?=$user1['login']?></h3>
                            <div class="card-text">
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="inputVorname" name="vorname" type="text" placeholder="Vorname" value="<?=$user1['vorname']?>" required>
                                    <label for="inputVorname" class="text-dark">Vorname</label>
                                </div>
                                <div class="form-floating my-3">
                                    <input class="form-control" id="inputNachname" name="nachname" type="text" placeholder="Nachname" value="<?=$user1['nachname']?>" required>
                                    <label for="inputNachname" class="text-dark">Nachname</label>
                                </div>
                                <div class="form-floating my-3">
                                    <input class="form-control" id="inputEmail" name="email" type="email" placeholder="E-Mail" value="<?=$user1['email']?>" required>
                                    <label for="inputEmail" class="text-dark">E-Mail</label>
                                </div>
                                <div class="form-floating my-3">
                                    <select class="form-select" id="kj-id" name="kj_id">
                                        <?php foreach ($kolpingjugenden as $kolpingjugend):?>
                                            <option class="text-dark" value="<?=$kolpingjugend['kolpingjugend_id']?>" <?php if ($kolpingjugend['kolpingjugend_id'] == $user1['kolpingjugend_id']) { print("selected"); }?>><?=$kolpingjugend['kolpingjugend_name']?></option>
                                        <?php endforeach;?>
                                    </select>
                                    <label for="kj-id" class="text-dark">Kolpingjugend</label>
                                </div>
                                <div class="form-floating my-3">
                                    <input class="form-control" id="inputPasswortNeu" name="passwortNeu" placeholder="Neues Passwort wiederholen" type="password">
                                    <label for="inputPasswortNeu" class="text-dark">Neues Passwort</label>
                                </div>
                                <div class="form-floating my-3">
                                    <input class="form-control" id="inputPasswortNeu2" name="passwortNeu2" placeholder="Neues Passwort wiederholen" type="password">
                                    <label for="inputPasswortNeu2" class="text-dark">Neues Passwort wiederholen</label>
                                </div>
                                <div class="col my-3">
                                    <div class="input-group justify-content-center">
                                        <label for="perm_login" class="input-group-text">Anmelde Berechtigungen?</label>
                                        <div class="input-group-text">
                                            <input value="perm_login" id="perm_login" type="checkbox" name="perm_login" value="1" class="form-check-input checkbox-kolping my-0" <?php if ($user1['perm_login'] == 1) print("checked");?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="col my-3">
                                    <div class="input-group justify-content-center">
                                        <label for="perm_admin" class="input-group-text">Admin Berechtigungen?</label>
                                        <div class="input-group-text">
                                            <input value="perm_admin" id="perm_admin" type="checkbox" name="perm_admin" value="1" class="form-check-input checkbox-kolping my-0" <?php if ($user1['perm_admin'] == 1) print("checked");?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="col my-3">
                                    <div class="input-group justify-content-center">
                                        <label for="perm_edit_kj" class="input-group-text">Kolpingjugend Admin?</label>
                                        <div class="input-group-text">
                                            <input value="perm_edit_kj" id="perm_edit_kj" type="checkbox" name="perm_edit_kj" value="1" class="form-check-input checkbox-kolping my-0" <?php if ($user1['perm_edit_kj'] == 1) print("checked");?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    <input type="number" value="<?=$_POST['user_id']?>" name="user_id" style="display: none;" required>
                                    <button type="submit" name="action" value="mod" class="btn btn-success mx-2" aria-label="Speichern"><i class="bi bi-sd-card text-light"></i></button>
                                    <button class="btn btn-danger mx-2" aria-label="Abbrechen" onclick="window.location.href = '/admin/user.php';"><i class="bi bi-x-circle text-light"></i></button>
                                </div>
                            </div>
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
        echo("<script>location.href='user.php'</script>");
        exit;
    }
}
ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Anwender*innen";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
?>
<div class="container users content-wrapper p-3">
    <div class="row" style="min-height: 75vh;">
        <div class="py-3 px-3 cbg ctext rounded">
            <div class="d-flex justify-content-between">
                <div class="col-4">
                    <h1>Anwender*innen<?php if (isMobile()) print("-<br>");?>verwaltung</h1>
                </div>
                <div class="col-4 d-flex justify-content-end">
                    <div>
                        <button class="btn btn-success" onclick="window.location.href = 'register.php';"><i class="bi bi-person-add text-light"></i></button>
                    </div>
                </div>
            </div>
            <p><?php print($total_users); ?> Anwender*innen</p>
            <?php if (!isMobile()): ?>
            <div class="table-responsive rounded">
                <table class="table align-middle table-borderless table-hover <?php if (check_style() == 'dark') print('table-dark');?>">
                    <thead>
                        <tr>
                            <div class="">
                                <th scope="col" class="border-0 text-start">
                                    <div class="py-2 text-uppercase">ID</div>
                                </th>
                                <th scope="col" class="border-0 text-center">
                                    <div class="p-2 px-3 text-uppercase">Vorname</div>
                                </th>
                                <th scope="col" class="border-0 text-center">
                                    <div class="p-2 px-3 text-uppercase">Nachname</div>
                                </th>
                                <th scope="col" class="border-0 text-center">
                                    <div class="p-2 px-3 text-uppercase">E-Mail</div>
                                </th>
                                <th scope="col" class="border-0 text-center">
                                    <div class="p-2 px-3 text-uppercase">Kolpingjugend</div>
                                </th>
                                <th scope="col" class="border-0 text-center">
                                    <div class="p-2 px-3 text-uppercase">Admin</div>
                                </th>
                                <th scope="col" class="border-0 text-center">
                                    <div class="p-2 px-3 text-uppercase">KJ Admin</div>
                                </th>
                                <th scope="col" class="border-0 text-end">
                                    <div class="py-2 text-uppercase">Erstellt</div>
                                </th>
                                <th scope="col" class="border-0" style="width: 15%"></th>
                            </div>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user1): 
                            $stmt = $pdo->prepare('SELECT * FROM kolpingjugend WHERE kolpingjugend_id = ?');
                            $stmt->bindValue(1, $user1['kolpingjugend_id'], PDO::PARAM_INT);
                            $result = $stmt->execute();
                            if (!$result) {
                                error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                            }
                            $kolpingjugend = $stmt->fetch();
                            ?>
                            <tr>
                                <td class="border-0 text-start">
                                    <strong><?=$user1['user_id']?></strong>
                                </td>
                                <td class="border-0 text-center">
                                    <strong class="text-break"><?=$user1['vorname']?></strong>
                                </td>
                                <td class="border-0 text-center">
                                    <strong class="text-break"><?=$user1['nachname']?></strong>
                                </td>
                                <td class="border-0 text-center">
                                    <strong class="text-break"><?=$user1['email']?></strong>
                                </td>
                                <td class="border-0 text-center">
                                    <strong class="text-break"><?=$kolpingjugend['kolpingjugend_name']?></strong>
                                </td>
                                <td class="border-0 text-center">
                                    <strong><?=$user1['perm_admin']?'Ja':'Nein'?></strong>
                                </td>
                                <td class="border-0 text-center">
                                    <strong><?=$user1['perm_edit_kj']?'Ja':'Nein'?></strong>
                                </td>
                                <td class="border-0 text-end">
                                    <strong><?=$user1['created_at']?></strong>
                                </td>
                                <td class="border-0 actions text-center">
                                    <?php if ($user1['user_id'] != 0):?>
                                    <form action="user.php" method="post" class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <div class="">
                                            <input type="number" value="<?=$user1['user_id']?>" name="user_id" style="display: none;" required>
                                            <button type="submit" name="action" value="mod" class="btn btn-kolping"><i class="bi bi-pencil text-light"></i></button>
                                        </div>
                                        <div class="">
                                            <input type="number" value="<?=$user1['user_id']?>" name="user_id" style="display: none;" required>
                                            <button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas<?=$user1['user_id']?>" aria-controls="offcanvas<?=$user1['user_id']?>"><i class="bi bi-person-dash text-light"></i></button>
                                            <div class="offcanvas offcanvas-end cbg" data-bs-scroll="true" tabindex="-1" id="offcanvas<?=$user1['user_id']?>" aria-labelledby="offcanvas<?=$user1['user_id']?>Label">
                                                <div class="offcanvas-header">
                                                    <h2 class="offcanvas-title ctext" id="offcanvas<?=$user1['user_id']?>Label">Wirklich Löschen?</h2>
                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                </div>
                                                <div class="offcanvas-body">
                                                    <span class="pb-3">Eine Löschung lässt sich nicht rückgängig machen!<br></span>
                                                    <button class="btn btn-success mx-2" type="submit" name="action" value="deleteconfirm">Ja</button>
                                                    <button class="btn btn-danger mx-2" type="button" data-bs-dismiss="offcanvas" aria-label="Close">Nein</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div> 
            <?php else: ?>
                <?php foreach ($users as $user1): 
                    $stmt = $pdo->prepare('SELECT * FROM kolpingjugend WHERE kolpingjugend_id = ?');
                    $stmt->bindValue(1, $user1['kolpingjugend_id'], PDO::PARAM_INT);
                    $result = $stmt->execute();
                    if (!$result) {
                        error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                    }
                    $kolpingjugend = $stmt->fetch();?>
                    <div class="card cbg2 p-0 my-3">
                        <div class="card-body">
                            <h3 class="card-title ctext text-center"><?=$user1['login']?></h3>
                            <div class="card-text">
                                User ID: <?=$user1['user_id']?><br>
                                Vorname: <?=$user1['vorname']?><br>
                                Nachname: <?=$user1['nachname']?><br>
                                E-Mail: <?=$user1['email']?><br>
                                Kolpingjugend: <?=$kolpingjugend['kolpingjugend_ort']?><br>
                                Admin: <?=$user1['perm_admin']?'Ja':'Nein'?><br>
                                Kolpingjugend Admin: <?=$user1['perm_edit_kj']?'Ja':'Nein'?><br>
                                Erstellt: <?=$user1['created_at']?>
                                <?php if ($user1['user_id'] != 0):?>
                                    <form action="user.php" method="post" class="d-flex justify-content-between mt-2">
                                        <div class="">
                                            <input type="number" value="<?=$user1['user_id']?>" name="user_id" style="display: none;" required>
                                            <button type="submit" name="action" value="mod" class="btn btn-kolping"><i class="bi bi-pencil text-light"></i></button>
                                        </div>
                                        <div class="">
                                            <input type="number" value="<?=$user1['user_id']?>" name="user_id" style="display: none;" required>
                                            <button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas<?=$user1['user_id']?>" aria-controls="offcanvas<?=$user1['user_id']?>"><i class="bi bi-person-dash text-light"></i></button>
                                            <div class="offcanvas offcanvas-end cbg" data-bs-scroll="true" tabindex="-1" id="offcanvas<?=$user1['user_id']?>" aria-labelledby="offcanvas<?=$user1['user_id']?>Label">
                                                <div class="offcanvas-header">
                                                    <h2 class="offcanvas-title ctext" id="offcanvas<?=$user1['user_id']?>Label">Wirklich Löschen?</h2>
                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                </div>
                                                <div class="offcanvas-body">
                                                    <span class="pb-3">Eine Löschung lässt sich nicht rückgängig machen!<br></span>
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
                        <td class="border-0 actions text-center">
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>        
        </div>
    </div>
</div> 
<?php 
$disheadercheck = false;
require_once("templates/footer.php"); 
?>