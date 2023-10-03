<?php
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
$user = check_user();
$disheadercheck = true;
if (!isset($user) || $user != true) {
    print("<script>location.href='/login.php'</script>");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM kolpingjugend WHERE kolpingjugend_id = ?");
$stmt->bindValue(1, $user['kolpingjugend_id']);
$stmt->execute();
if ($stmt->rowCount() != 1) {
    error_log("Error while pulling kolpingjugend from user: " + $user['user_id'] + " from the database");
}
$kolpingjugend = $stmt->fetch();

if(isset($_POST['action'])) {
    // Wenn action "save" ist
    if($_POST['action'] == 'save') {
        if(isset($_POST['vorname']) and isset($_POST['nachname']) and isset($_POST['email']) and isset($_POST['passwortNeu']) and isset($_POST['passwortNeu2']) and !empty($_POST['vorname']) and !empty($_POST['nachname']) and !empty($_POST['email'])) {
            $stmt = $pdo->prepare("UPDATE users SET vorname = ?, nachname = ?, email = ? WHERE user_id = ?");
            $stmt->bindValue(1, $_POST['vorname']);
            $stmt->bindValue(2, $_POST['nachname']);
            $stmt->bindValue(3, $_POST['email']);
            $stmt->bindValue(4, $user['user_id'], PDO::PARAM_INT);
            $result0 = $stmt->execute();
            if (!$result0) {
                error('Datenbank Fehler!', pdo_debugStrParams($stmt));
            }
            // Überprüfe ob die eingegebenen Passwörter übereinstimmen
            if($_POST['passwortNeu'] == $_POST['passwortNeu2']) {
                // überprüft das die Passwörter nicht leer sind
                if (!empty($_POST['passwortNeu']) and !empty($_POST['passwortNeu2'])) {
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                    $stmt->bindValue(1, password_hash($_POST['passwortNeu'], PASSWORD_DEFAULT));
                    $stmt->bindValue(2, $user['user_id'], PDO::PARAM_INT);
                    $result1 = $stmt->execute();
                    if (!$result1) {
                        error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                    }                    
                }
            } else {
                error('Passwörter stimmen nicht überein!');
            }
        } if (isset($_POST['KJ_name']) and !empty($_POST['KJ_name']) and isset($_POST['KJ_ort']) and !empty($_POST['KJ_ort']) and isset($_POST['House_name']) and !empty($_POST['House_name']) and isset($_POST['House_address']) and !empty($_POST['House_address'])) {
            if ($user['perm_edit_kj'] == 1) {
                $stmt = $pdo->prepare("UPDATE kolpingjugend SET kolpingjugend_name = ?, kolpingjugend_ort = ? WHERE kolpingjugend_id = ?");
                $stmt->bindValue(1, $_POST['KJ_name']);
                $stmt->bindValue(2, $_POST['KJ_ort']);
                $stmt->bindValue(3, $kolpingjugend['kolpingjugend_id'], PDO::PARAM_INT);
                $result2 = $stmt->execute();
                if (!$result2) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }
                $stmt = $pdo->prepare("UPDATE houses SET house_name = ?, house_address = ? WHERE kolpingjugend_id = ?");
                $stmt->bindValue(1, $_POST['House_name']);
                $stmt->bindValue(2, $_POST['House_address']);
                $stmt->bindValue(3, $kolpingjugend['kolpingjugend_id'], PDO::PARAM_INT);
                $result3 = $stmt->execute();
                if (!$result3) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }
            } else {
                error('Du besitzt die hierfür benötigen Berechtigungen nicht!');
            }
        } else {
            error("nur die Passwort Felder dürfen leer sein!");
        }
        echo("<script>location.href='/settings.php'</script>");
        exit;
    }
    if ($_POST['action'] == 'cancel') {
        echo("<script>location.href='/index.php'</script>");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT house_name, house_address FROM houses WHERE kolpingjugend_id = ?");
$stmt->bindValue(1, $kolpingjugend['kolpingjugend_id'], PDO::PARAM_INT);
$stmt->execute();
if ($stmt->rowCount() != 1) {
    error_log("Error while pulling kolpingjugend from user: " + $user['user_id'] + " from the database");
}
$house = $stmt->fetch();

ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "Verbandsspiel Kolpingjugend DVRS - Einstellungen deines Profils";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
?>
<div class="container py-4" style="min-height: 80vh;">
    <form action="settings.php" method="post" class="d-flex justify-content-center">
        <div class="cbg2 rounded p-3">
            <div class="mb-3">
                    <h2 class="text-center">Deine Einstellungen:</h2>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="inputVorname" name="vorname" placeholder="Vorname" value="<?=$user['vorname']?>" required>
                        <label for="inputVorname" class="text-dark">Vorname</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="inputNachname" name="nachname" placeholder="Nachname" value="<?=$user['nachname']?>" required>
                        <label for="inputNachname" class="text-dark">Nachname</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="inputEmail" name="email" placeholder="E-Mail" value="<?=$user['email']?>" required>
                        <label for="inputEmail" class="text-dark">E-Mail</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="inputPasswortNeu" name="passwortNeu" placeholder="Neues Passwort">
                        <label for="inputPasswortNeu" class="text-dark">Neues Passwort</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="inputPasswortNeu2" name="passwortNeu2" placeholder="Neues Passwort wiederholen">
                        <label for="inputPasswortNeu2" class="text-dark">Neues Passwort wiederholen</label>
                    </div>
            </div>
            <div class="my-3">
                    <h2 class="text-center">Kolpingjugend Einstellungen:</h2>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="inputKJ_name" name="KJ_name" placeholder="Name der Kolpingjugend" value="<?=$kolpingjugend['kolpingjugend_name']?>" <?php if ($user['perm_edit_kj'] != 1) print('disabled');?>>
                        <label for="inputKJ_name" class="text-dark">Name der Kolpingjugend</label>
                    </div>
                    <div class="form-floating my-3">
                        <input type="text" class="form-control" id="inputKJ_ort" name="KJ_ort" placeholder="Ort der Kolpingjugen" value="<?=$kolpingjugend['kolpingjugend_ort']?>" required <?php if ($user['perm_edit_kj'] != 1) print('disabled');?>>
                        <label for="inputKJ_ort" class="text-dark">Ort der Kolpingjugend</span>
                    </div>
                    <div class="form-floating my-3">
                        <input type="text" class="form-control" id="inputHouse_name" name="House_name" placeholder="Name der Kolping-Villa" value="<?=$house['house_name']?>" required <?php if ($user['perm_edit_kj'] != 1) print('disabled');?>>
                        <label for="inputHouse_name" class="text-dark">Name der Kolping-Villa</span>
                    </div>
                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" id="inputHouse_address" name="House_address" placeholder="Adresse der Kolping-Villa" value="<?=$house['house_address']?>" required <?php if ($user['perm_edit_kj'] != 1) print('disabled');?>>
                        <label for="inputHouse_address" class="text-dark">Adresse der Kolping-Villa</span>
                    </div>
            </div>
            <div class="d-flex justify-content-center mt-3">
                <input type="number" value="<?=$kolpingjugend['kolpingjugend_id']?>" name="kj_id" style="display: none;" required>
                <button type="submit" name="action" value="save" class="mx-2 btn btn-success"><i class="bi bi-sd-card text-light"></i></button>
                <button class="mx-2 btn btn-danger" onclick="window.location.href = '/index.php';"><i class="bi bi-x-circle text-light"></i></button>
            </div>
        </div>
    </form>
</div>
<?php 
$disheadercheck = false;
require_once("templates/footer.php"); 
?>