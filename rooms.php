<?php
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
ob_start();
require_once("templates/header.php");
$buffer=ob_get_contents();
ob_end_clean();

$title = "Verbandsspiel Kolpingjugend DVRS - Räume";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;

if (!isset($user)) {
    print("<script>location.href='/login.php'</script>");
    exit;
}
if (!isset($_GET['kj_id'])) {
    print("<script>location.href='/login.php'</script>");
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM kolpingjugend WHERE kolpingjugend_id = ?");
$stmt->bindValue(1, $_GET['kj_id']);
$stmt->execute();
if ($stmt->rowCount() != 1) {
    error_log("Fehler beim Abfragen der Datenbank - Fehler: 1 ".$_GET['kj_id']);
    exit;
}
$kj = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM houses WHERE kolpingjugend_id = ?");
$stmt->bindValue(1, $_GET['kj_id']);
$stmt->execute();
if ($stmt->rowCount() != 1) {
    error_log("Fehler beim Abfragen der Datenbank - Fehler: 2 ".$_GET['kj_id']);
    exit;
}
$kj_house = $stmt->fetch();

if (!isset($_GET['s'])) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE house_id = ?");
    $stmt->bindValue(1, $kj_house['house_id']);
    $stmt->execute();
    if ($stmt->rowCount() < 16) {
        error_log("Fehler beim Abfragen der Datenbank - Fehler: 3.1");
        exit;
    }
    $kj_rooms = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->bindValue(1, $_GET['room_id']);
    $stmt->execute();
    if ($stmt->rowCount() != 1) {
        error_log("Fehler beim Abfragen der Datenbank - Fehler: 3.2");
        exit;
    }
    $kj_room = $stmt->fetch();
}
if (isset($_GET['s'])) $s_set = 'true'; else $s_set = 'false';


?>



<?php
require_once("templates/footer.php");

?>