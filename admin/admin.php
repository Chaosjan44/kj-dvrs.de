<?php 
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
ob_start();
require_once("templates/header.php");
$buffer=ob_get_contents();
ob_end_clean();

$title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Adminbreich";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
if (!isset($user) || $user['perm_admin'] != 1) {
    print("<script>location.href='/login.php'</script>");
    exit;
}
?>


<div class="container py-3">
    <div style="min-height: 72vh;">
        <div class="card cbg2 my-3 py-3 px-3">
            <div class="card-body text-center">
                <h1 class="card-title display-3 text-center mb-4 text-kolping-orange">Administrations Bereich</h1>
                <?php if (!isMobile()): ?>
                    <div class="card-text">
                        <button class="btn btn-kolping mx-1 my-2" type="button" onclick="window.location.href = '/admin/kolpingjugenden.php';">Kolpingjugenden</button>
                        <button class="btn btn-kolping mx-1 my-2" type="button" onclick="window.location.href = '/admin/user.php';">Anwender*innen</button>
                        <button class="btn btn-kolping mx-1 my-2" type="button" onclick="window.location.href = '/admin/houses.php';">Häuser</button>
                        <button class="btn btn-kolping mx-1 my-2" type="button" onclick="window.location.href = '/admin/room_templates.php';">Raum Vorlagen</button>
                    </div>
                <?php else: ?>
                    <div class="card-text my-1">
                    </div>
                        <button class="btn btn-kolping mx-1" type="button" onclick="window.location.href = '/admin/kolpingjugenden.php';">Kolpingjugenden</button>
                        <button class="btn btn-kolping mx-1" type="button" onclick="window.location.href = '/admin/user.php';">Anwender*innen</button>
                    <div class="card-text my-1">
                        <button class="btn btn-kolping mx-1" type="button" onclick="window.location.href = '/admin/houses.php';">Häuser</button>
                        <button class="btn btn-kolping mx-1" type="button" onclick="window.location.href = '/admin/room_templates.php';">Raum Vorlagen</button>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>

<?php require_once("templates/footer.php"); ?>