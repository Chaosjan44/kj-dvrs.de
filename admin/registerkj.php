<?php 
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");

ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Kolpingjugend anlegen";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;

if (!isset($user)) {
    print("<script>location.href='/login.php'</script>");
    exit;
}
if ($user['perm_admin'] != 1) {
    errorPage('Unzureichende Berechtigungen!');
}

$error_msg = "";
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'register') {
        if(isset($_POST['kj_name']) && !empty($_POST['kj_name']) && isset($_POST['kj_ort']) && !empty($_POST['kj_ort'])) {
            $kj_name = $_POST['kj_name'];
            $kj_ort = $_POST['kj_ort'];

            $stmt = $pdo->prepare("INSERT INTO kolpingjugend SET kolpingjugend_name = ?, kolpingjugend_ort = ?");
            $stmt->bindValue(1, $kj_name);
            $stmt->bindValue(2, $kj_ort);
            $result = $stmt->execute();
            if (!$result) {
                error_log("Error while registering kj");
                exit;
            }
            $error_msg = "<span class='text-success'>Kolpingjugend erfolgreich angelegt. :)<br><br></span>";
            echo("<script>location.href='kolpingjugenden.php'</script>");
        } else {
            $error_msg = "<span class='text-danger'>Es müssen alle Felder ausgefüllt werden!<br><br></span>";
        }
    } 
}

?>
<div class="container py-3">
	<div class="row justify-content-center" style="min-height: 73.3vh;">
		<div class="col">
			<div class="card cbg2">
                <div class="card-body">
                    <h3 class="card-title display-3 text-center mb-4 text-kolping-orange">Kolpingjugend anlegen</h3>
                    <div class="card-text">
                        <?=$error_msg?>
                        <form action="registerkj.php" method="post">
                            <div class="form-floating mb-3">
                                <input id="kjName" type="text" name="kj_name" placeholder="Kolpingjugend Name" autofocus class="form-control border-0 ps-4 text-dark fw-bold" required>
                                <label for="kjName" class="text-dark fw-bold">Name der Kolpingjugend</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="kjOrt" type="text" name="kj_ort" placeholder="Kolpingjugend Ort" class="form-control border-0 ps-4 text-dark fw-bold" required>
                                <label for="kjOrt" class="text-dark fw-bold">Ort der Kolpingjugend</label>
                            </div>
                            <div class="col text-center">
                                <button type="submit" name="action" value="register" class="btn btn-kolping btn-floating">Kolpingjugend anlegen</button>
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