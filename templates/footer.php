<?php 
$crdate = "2023";
?>
</body>
<?php if (!isMobile()): ?>
    <footer class="container-fluid cbg2 footer py-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-2 text-start">
                    <ul class="px-0">
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/" class="link ctext ps-2">Start</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/login.php" class="link ctext ps-2">Anmelden</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="https://jugend.kolping-dvrs.de/" class="link ctext ps-2">Hauptwebseite</a>
                        </li>
                    </ul>
                </div>
                <div class="col-2 text-start">
                    <ul class="px-0">
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/disclaimer.php" class="link ctext ps-2">Disclaimer</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/impressum.php" class="link ctext ps-2">Impressum</a> 
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/datenschutz.php" class="link ctext ps-2">Datenschutz</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row justify-content-end align-items-center">
            <div class="col-4 text-center ctext light"><a href="https://jugend.kolping-dvrs.de/" class="text-center ctext light">&copy; <?=$crdate?> Kolpingjugend Diözesanverband Rottenburg-Stuttgart</a></div>
            <div class="col-4 d-flex justify-content-end">
                <input onchange="toggleStyle()" class="styleswitcher" type="checkbox" name="switch" id="style_switch" <?php if (check_style() == "dark"): print("checked"); endif; ?> >
                <label class="styleswitcherlabel" for="style_switch"></label>
            </div>
        </div>
    </footer>
<?php else: ?>
    <footer class="container-fluid cbg2 footer py-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-6 text-start ps-2">
                    <ul class="px-0">
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/" class="link ctext ps-2">Start</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/login.php" class="link ctext ps-2">Anmelden</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="https://jugend.kolping-dvrs.de/" class="link ctext ps-2">Hauptwebseite</a>
                        </li>
                    </ul>
                </div>
                <div class="col-6 text-start ps-2">
                    <ul class="px-0">
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/disclaimer.php" class="link ctext ps-2">Disclaimer</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/impressum.php" class="link ctext ps-2">Impressum</a> 
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="/datenschutz.php" class="link ctext ps-2">Datenschutz</a>
                        </li>
                    </ul>
                </div>
                <div class="row justify-content-between align-items-center">
                    <div class="col-4 ctext text-start light ps-0"><a href="https://jugend.kolping-dvrs.de/" class="ctext light">&copy; <?=$crdate?> Kolpingjugend Diözesanverband Rottenburg-Stuttgart</a></div>
                    <div class="col-4 d-flex justify-content-end">
                        <input onchange="toggleStyle()" class="styleswitcher" type="checkbox" name="switch" id="style_switch" <?php if (check_style() != "light"): print("checked"); endif; ?> >
                        <label class="styleswitcherlabel" for="style_switch"></label>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<?php endif; ?>

<script src="/js/custom.js"></script>

</html>