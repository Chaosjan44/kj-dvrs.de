<?php 
ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "Verbandsspiel Kolpingjugend DVRS - Impressum";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
?>
<div class="container py-3">
    <div style="min-height: 75vh;">
        <h1 class="display-3 text-center text-kolping-orange">Impressum</h1>
        <h5 class="mb-1">Angaben gemäß § 5 Telemediengesetz (TMG):</h5>
        <p class="mb-3">
            Jan Schniebs<br>
            c/o IP-Management #12901<br>
            Ludwig-Erhard-Str. 18<br>
            20459 Hamburg<br>
            E-Mail: <a href="mailto:jan@schniebs.com" class="link">jan@schniebs.com</a><br>
            Telefon: +49 7181 9379852
        </p>
        <h5 class="mb-1">Inhaltlich verantwortlich i.S.v. §55 Abs. 2 RStV:</h5>
        <p class="mb-3">
            Jan Schniebs<br>
            c/o IP-Management #12901<br>
            Ludwig-Erhard-Str. 18<br>
            20459 Hamburg<br>
            E-Mail: <a href="mailto:jan@schniebs.com" class="link">jan@schniebs.com</a><br>
            Telefon: +49 7181 9379852<br>
            <br>
            Nähere Hinweise zu unserem Datenschutz finden Sie unter diesem <a href="/datenschutz.php" class="link">Link</a>.
        </p>
    </div>
</div>
<?php require_once("templates/footer.php"); ?>