<?php 
ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "Verbandsspiel Kolpingjugend DVRS - Startseite";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;

?>

<div class="container-fluid px-0 py-3">
    <?php if (!isMobile()): ?>
    <div class="container-xxl" style="min-height: 73.3vh;">
        <div class="row ctext">
            <h1 class="display-4 text-center">Verbandsspiel der Kolpingjugend DVRS</h1>
            <span class="text-center text-size-larger">
                Weitere Informationen zum Verbandsspiel findest du <a href="https://jugend.kolping-dvrs.de/verbandsspiel" class="text-size-large link">hier</a>.<br>
                <a href="https://jugend.kolping-dvrs.de/" class="text-size-large link">Hier</a> gehts zur Webseite der Kolpingjugend Diözesanverband Rottenburg-Stuttgart<br>
            </span>
        </div>
        <div class="row gx-5 pt-3">
        </div>
    </div>
    <?php else: ?>
    <div class="container" style="min-height: 80vh;">
        <div class="row ctext">
            <h1 class="display-4 text-center">Verbandsspiel der Kolpingjugend DVRS</h1>
            <span class="text-center text-size-larger">
                Weitere Informationen zum Verbandsspiel findest du <a href="https://jugend.kolping-dvrs.de/verbandsspiel" class="text-size-large link">hier</a>.<br>
                <a href="https://jugend.kolping-dvrs.de/" class="text-size-large link">Hier</a> gehts zur Webseite der Kolpingjugend Diözesanverband Rottenburg-Stuttgart<br>
            </span>
        </div>
        <div class="row gx-5 pt-3 justify-content-center">
            <div class="col-11">
                
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>





<?php require_once("templates/footer.php"); ?>