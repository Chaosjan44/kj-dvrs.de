<?php 
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");

ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Anwender*in anlegen";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;

if (!isset($user) || $user != true) {
    print("<script>location.href='/login.php'</script>");
}
if ($user['perm_admin'] != 1) {
    error('Unzureichende Berechtigungen!');
}

$stmt = $pdo->prepare('SELECT * FROM kolpingjugend ORDER BY kolpingjugend_name');
$result = $stmt->execute();
if (!$result) {
    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
}
$kolpingjugenden = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error_msg = "";
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'register') {
        if(isset($_POST['user']) && !empty($_POST['user']) && isset($_POST['passwort']) && !empty($_POST['passwort']) && isset($_POST['passwort2']) && !empty($_POST['passwort2']) && isset($_POST['vorname']) && !empty($_POST['vorname']) && isset($_POST['nachname']) && !empty($_POST['nachname'])) {
            if ($_POST['passwort'] == $_POST['passwort2']) {

                $username = $_POST['user'];
                $passwort = password_hash($_POST['passwort'], PASSWORD_DEFAULT);
                $vorname = trim($_POST['vorname']);
                $nachname = trim($_POST['nachname']);
                $email = trim($_POST['email']);
                $kj_id = $_POST['kj_id'];

                $stmt = $pdo->prepare("INSERT INTO users SET login = ?, password = ?, nachname = ?, vorname = ?, email = ?, kolpingjugend_id = ?, perm_login = ?, perm_admin = ?, perm_edit_kj = ?");
                $stmt->bindValue(1, $username);
                $stmt->bindValue(2, $passwort);
                $stmt->bindValue(3, $nachname);
                $stmt->bindValue(4, $vorname);
                $stmt->bindValue(5, $email);
                $stmt->bindValue(6, $kj_id);
                $stmt->bindValue(7, (isset($_POST['perm_login']) ? "1" : "0"), PDO::PARAM_INT);
                $stmt->bindValue(8, (isset($_POST['perm_admin']) ? "1" : "0"), PDO::PARAM_INT);
                $stmt->bindValue(9, (isset($_POST['perm_edit_kj']) ? "1" : "0"), PDO::PARAM_INT);
                $result = $stmt->execute();
                if (!$result) {
                    error_log("Error while registering user");
                    exit;
                }
                $error_msg = "<span class='text-success'>User erfolgreich angelegt. :)<br><br></span>";
                echo("<script>location.href='user.php'</script>");
            } else {
                $error_msg = "<span class='text-danger'>Die angegebenen Passwörter stimmen nicht überein.<br><br></span>";
            }
        } else {
            $error_msg = "<span class='text-danger'>Es müssen alle Felder ausgefüllt werden!<br><br></span>";
        }
    }
}

?>
<div class="container py-3">
	<div class="row justify-content-center" style="min-height: 75vh;">
		<div class="col">
			<div class="card cbg2">
                <div class="card-body">
                    <h3 class="card-title display-3 text-center mb-4 text-kolping-orange">Registrieren</h3>
                    <div class="card-text">
                        <?=$error_msg?>
                        <form action="register.php" method="post">
                            <div class="form-floating mb-3">
                                <input id="inputUser" type="text" name="user" placeholder="User" autofocus class="form-control border-0 ps-4 text-dark" required>
                                <label for="inputUser" class="text-dark">Anmeldename</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="inputEmail" type="text" name="email" placeholder="E-Mail" autofocus class="form-control border-0 ps-4 text-dark" required>
                                <label for="inputEmail" class="text-dark">E-Mail</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="inputPassword" type="password" name="passwort" placeholder="Passwort" class="form-control border-0 ps-4 text-dark" required>
                                <label for="inputPassword" class="text-dark">Passwort</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="inputPassword2" type="password" name="passwort2" placeholder="Passwort wiederholen" class="form-control border-0 ps-4 text-dark" required>
                                <label for="inputPassword2" class="text-dark">Passwort wiederholen</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="inputVorname" type="text" name="vorname" placeholder="Vorname" class="form-control border-0 ps-4 text-dark" required>
                                <label for="inputVorname" class="text-dark">Vorname</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="inputNachname" type="text" name="nachname" placeholder="Nachname" class="form-control border-0 ps-4 text-dark" required>
                                <label for="inputNachname" class="text-dark">Nachname</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="kj_id" name="kj_id">
                                    <option class="text-dark" selected>Bitte auswählen</option>
                                    <?php foreach ($kolpingjugenden as $kolpingjugend):?>
                                        <option class="text-dark" value="<?=$kolpingjugend['kolpingjugend_id']?>"><?=$kolpingjugend['kolpingjugend_name']?></option>
                                    <?php endforeach;?>
                                </select>
                                <label for="kj_id" class="text-dark">Kolpingjugend</label>
                            </div>
                            <div class="col mb-3">
                                <div class="input-group justify-content-center">
                                    <label for="perm_login" class="input-group-text">Anmelde Berechtigungen?</label>
                                    <div class="input-group-text">
                                        <input value="perm_login" id="perm_login" type="checkbox" name="perm_login" value="0" class="form-check-input checkbox-kolping my-0">
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-3">
                                <div class="input-group justify-content-center">
                                    <label for="perm_admin" class="input-group-text">Admin Berechtigungen?</label>
                                    <div class="input-group-text">
                                        <input value="perm_admin" id="perm_admin" type="checkbox" name="perm_admin" value="0" class="form-check-input checkbox-kolping my-0">
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-3">
                                <div class="input-group justify-content-center">
                                    <label for="perm_edit_kj" class="input-group-text">Kolpingjugend Admin?</label>
                                    <div class="input-group-text">
                                        <input value="perm_edit_kj" id="perm_edit_kj" type="checkbox" name="perm_edit_kj" value="0" class="form-check-input checkbox-kolping my-0">
                                    </div>
                                </div>
                            </div>
                            <div class="col text-center">
                                <button type="submit" name="action" value="register" class="btn btn-kolping btn-floating">Registrieren</button>
                            </div>
                        </form>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
<?php
include_once("templates/footer.php");
?>