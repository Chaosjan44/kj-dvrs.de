<?php 
ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "Verbandsspiel Kolpingjugend DVRS - Startseite";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;

?>

<div class="container-fluid px-0 pt-0 pb-3">
    <?php if (!isMobile()): ?>
    <div class="container-xxl">
        <div class="row ctext">
            <h1 class="display-4 text-center">Verbandsspiel der Kolpingjugend DVRS</h1>
            <span class="text-center text-size-larger">
                w
        </span>
        </div>
        <div class="row gx-5 pt-3">

        </div>
    </div>
    <?php else: ?>
    <div class="container">
        <div class="row ctext">
            <h1 class="display-4 text-center">Verbandsspiel der Kolpingjugend DVRS</h1>
            <span class="text-center text-size-larger">
                w
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