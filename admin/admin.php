<?php 
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
$disheadercheck = true;
$user = check_user();
ob_start();
require_once("templates/header.php");
$buffer=ob_get_contents();
ob_end_clean();

$title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Adminbreich";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
if (!isset($user) || $user != true) {
    print("<script>location.href='/login.php'</script>");
} else if ($user['perm_admin'] != 1) {
    error('Unzureichende Berechtigungen!');
}
?>


<div class="container py-3">
    <div style="min-height: 75vh;">
        <div class="card cbg2 px-3">
            <div class="card-body text-center">
                <h1 class="card-title display-3 text-center mb-4 text-kolping-orange">Administrations Bereich</h1>
                <div class="card-text">
                    <button class="btn btn-kolping m-1" type="button" onclick="window.location.href = '/admin/kolpingjugenden.php';">Kolpingjugenden</button>
                    <button class="btn btn-kolping m-1" type="button" onclick="window.location.href = '/admin/user.php';">Anwender*innen</button>
                    <button class="btn btn-kolping m-1" type="button" onclick="window.location.href = '/admin/houses.php';">Häuser</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $disheadercheck = false;
require_once("templates/footer.php"); ?>