<?php 
ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "Verbandsspiel Kolpingjugend DVRS - 404 Seite nicht gefunden";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
?>

<div class="container-fluid py-3">
    <div style="min-height: 75vh;">
        <h2 class="display-4 text-kolping-orange text-center">404 - Seite nicht gefunden!</h2>
        <div class="row justify-content-center <?php if(isMobile()) print("row-cols-1");?>" style="max-height: 50vh;">
            <div class="py-2 col d-flex justify-content-<?php if(!isMobile()) print("end"); else print("center");?>">
                <img src="/images/Schnuffi_Rainbow.png" alt="" class="" style="max-height: 50vh; max-width: 90%;">
            </div>
            <div class="py-2 col <?php if(!isMobile()) print("d-flex align-items-center"); else print("text-center");?>">
                <span class="ctext text-size-larger">
                    Du scheinst dich ein wenig verirrt zu haben.<br>
                    Hier kommst du wieder auf die <a href="/" class="link text-size-larger">Startseite</a>.<br>
                </span>
            </div>
        </div>
    </div>
</div>


<?php
include_once("templates/footer.php");
?>