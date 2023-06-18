<?php 
ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "Verbandsspiel Kolpingjugend DVRS - Startseite";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
?>

<div class="container-fluid py-3">
    <div class="container" style="min-height: 75vh;">
        <div class="ctext">
            <h1 class="display-4 text-center mb-0">Under KJonstruction - Bau dir deine Kolpingvilla!</h1>
            <h1 class="display-6 text-center">Verbandsspiel 2023/24</h1>
            <div class="text-size-x-larger">
                <span class="text-size-large">Träumst du von einer eigenen Villa mit Kinosaal, Partykeller und Co.? Dann hast du jetzt die einmalige Möglichkeit, deine Kolpingvilla zu bauen!<br><br></span>
                <h2>Wie funktioniert's?</h2>
                <ul class="dots">
                    <li class="orange text-size-large"><span class="text-size-large">Zum Start bekommt ihr den Umriss eurer Kolpingvilla als Poster zugeschickt.</span></li>
                    <li class="orange text-size-large"><span class="text-size-large">Alle 3 Monate bekommt ihr ein Aufgabenpaket mit verschiedenen Räumen. Hinter jedem Raum verbirgt sich eine Aufgabe.</span></li>
                    <li class="orange text-size-large"><span class="text-size-large">Aus diesen Räumen könnt ihr eure Lieblingszimmer und -anbauten auswählen und die jeweilige Aufgabe meistern.</span></li>
                    <li class="orange text-size-large"><span class="text-size-large">Aus den unterschiedlichsten Räumen kannst du so mit deiner Kolpinggruppe deine Traumvilla zusammenstellen!</span></li>
                    <li class="orange text-size-large"><span class="text-size-large">Wenn ihr die Challenges & Aufgaben zum jeweiligen Zimmer gemeistert habt,<br>dann bekommt deine Gruppe eine ausgestattete Version des Raumes in bunt zugesendet.</span></li>
                    <li class="orange text-size-large"><span class="text-size-large">Je mehr Räume & Aufgaben ihr also schafft, desto bunter wird eure Kolpingvilla!</span></li>
                </ul>
                <h2>Wer kann mitmachen?</h2>
                <ul class="dots">
                    <li class="orange text-size-large"><span class="text-size-large">Alle Kolpinggruppen - egal welchen Alters!</span></li>
                </ul>
                <h2>Wann geht's los?</h2>
                <ul class="dots">
                    <li class="orange text-size-large"><span class="text-size-large">Das Kick-off-Event findet am <bold>8. Oktober 2023 um 18 Uhr online</bold> statt.</span></li>
                    <li class="orange text-size-large"><span class="text-size-large">Die Siegerehrung findet auf dem Jugendfestival 2024 statt! </span></li>
                </ul>
                <h2>Was kann man gewinnen?</h2>
                <ul class="dots">
                    <li class="orange text-size-large"><span class="text-size-large">Je mehr Räume ihr schafft, desto höher ist auch der Preis!</span></li>
                    <li class="orange text-size-large"><span class="text-size-large">Es lohnt sich schon ab dem 3. Raum mitzubauen.</span></li>
                    <li class="orange text-size-large"><span class="text-size-large">Die höchste Gewinnsumme beträgt sagenhafte 200 €, die ihr zum Beispiel für einen Ausflug einsacken könnt.<br>Also nichts wie anmelden und losbauen!</span></li>
                </ul>
                <span class="text-size-larger">Weitere Infos und Anmeldung findest du <a href="https://jugend.kolping-dvrs.de/verbandsspiel/" class="link text-size-larger">hier</a></span>
            </div>
        </div>
    </div>
</div>





<?php require_once("templates/footer.php"); ?>