<?php
require_once("php/functions.php");
setlocale (LC_ALL, 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de', 'ge', 'de_DE.ISO_8859-1', 'German_Germany');
session_start();
if ($disheadercheck != true) {
    $user = check_user();
}
$isadmin = false;
if (isset($user) && $user != null) {
    $stmt = $pdo->prepare("SELECT * FROM kolpingjugend WHERE kolpingjugend_id = ?");
    $stmt->bindValue(1, $user['kolpingjugend_id']);
    $stmt->execute();
    if ($stmt->rowCount() != 1) {
        errorPage('Du scheinst keiner Kolpingjugend zugeordnet zu sein, melde dich bitte bei <a href="mailto:admin@kj-dvrs.de" class="link">admin@kj-dvrs.de</a>');
    }
    $kolpingjugend = $stmt->fetch();
    if ($user['perm_admin'] == 1) {
        $isadmin = true;
    }
}
require_once("templates/imports.php");
?>

<header class="sticky-top">
    <nav class="navbar navbar-expand-lg cbg ctext">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src="/images/Kolpingjugend_light.svg" class="navbar-icon_light" alt="Navbar Logo">
                <img src="/images/Kolpingjugend_dark.svg" class="navbar-icon_dark" alt="Navbar Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse cbg" tabindex="-1" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-lg-0">
                    <?php if(isset($user) && $user != null):?>
                        <li class="nav-item text-size-x-large mx-1">
                            <a class="nav-link clink" aria-current="page" href="/overview.php">Alle Häuser</a>
                        </li>
                        <li class="nav-item text-size-x-large mx-1">
                            <a class="nav-link clink" href="/kolpingjugend.php?id=<?=$user['kolpingjugend_id']?>">Das Haus von <?=$kolpingjugend['kolpingjugend_name']?></a>
                        </li>
                    <?php endif;?>
                    <?php if($isadmin == true):?>
                        <li class="nav-item text-size-x-large mx-1">
                            <a class="nav-link clink" href="/admin/admin.php">Administration</a>
                        </li>
                    <?php endif;?>
                </ul>
                <ul class="navbar-nav mb-lg-0">
                    <?php if(isset($user) && $user != null):?>
                        <li class="nav-item text-size-x-large mx-1">
                            <a class="nav-link clink" href="/settings.php">Einstellungen</a>
                        </li>
                    <?php endif;?>
                    <?php if(!isset($user) || $user == null):?>
                        <li class="nav-item text-size-x-large mx-1">
                            <a class="nav-link clink" href="/login.php">Anmelden</a>
                        </li>
                    <?php else:?>
                        <li class="nav-item text-size-x-large mx-1">
                            <a class="nav-link clink clink" href="/logout.php">Abmelden</a>
                        </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="header-line text-end">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-at-fill" viewBox="0 0 16 16">
            <path d="M2 2A2 2 0 0 0 .05 3.555L8 8.414l7.95-4.859A2 2 0 0 0 14 2H2Zm-2 9.8V4.698l5.803 3.546L0 11.801Zm6.761-2.97-6.57 4.026A2 2 0 0 0 2 14h6.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586l-1.239-.757ZM16 9.671V4.697l-5.803 3.546.338.208A4.482 4.482 0 0 1 12.5 8c1.414 0 2.675.652 3.5 1.671Z"/>
            <path d="M15.834 12.244c0 1.168-.577 2.025-1.587 2.025-.503 0-1.002-.228-1.12-.648h-.043c-.118.416-.543.643-1.015.643-.77 0-1.259-.542-1.259-1.434v-.529c0-.844.481-1.4 1.26-1.4.585 0 .87.333.953.63h.03v-.568h.905v2.19c0 .272.18.42.411.42.315 0 .639-.415.639-1.39v-.118c0-1.277-.95-2.326-2.484-2.326h-.04c-1.582 0-2.64 1.067-2.64 2.724v.157c0 1.867 1.237 2.654 2.57 2.654h.045c.507 0 .935-.07 1.18-.18v.731c-.219.1-.643.175-1.237.175h-.044C10.438 16 9 14.82 9 12.646v-.214C9 10.36 10.421 9 12.485 9h.035c2.12 0 3.314 1.43 3.314 3.034v.21Zm-4.04.21v.227c0 .586.227.8.581.8.31 0 .564-.17.564-.743v-.367c0-.516-.275-.708-.572-.708-.346 0-.573.245-.573.791Z"/>
        </svg>
        <a href="mailto:admin@kj-dvrs.de" class="ctext pe-1">admin@kj-dvrs.de</a>
    </div>
</header>



<body>
<div class="modal fade" id="cookieModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cookieModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content cbg">
            <div class="modal-header cbg">
                <h4 class="modal-title ctext fw-bold" id="cookieModalLabel">Mhhh Lecker &#x1F36A;!</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ctext cbg fw-normal">
                <div class="px-2">
                    <p>Wir nutzen Cookies auf unserer Webseite.<br>
                    Alle Cookies die wir verwenden sind für die Funktion der Webseite nötig. <br>
                    Die Cookies werden nicht ausgewertet.
                    </p>
                </div>
            </div>
            <div class="modal-footer ctext cbg fw-bold">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick='setCookie("acceptCookies", "false", 365)'>Ablehnen</button>
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick='setCookie("acceptCookies", "true", 365)'>Akzeptieren</button>
            </div>
        </div>
    </div>
</div>


<!-- comment the following to disable under KJonstruction -->
<div class="modal fade" id="underKJonstructionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="underKJonstructionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content cbg">
            <div class="modal-header cbg">
                <h4 class="modal-title ctext fw-bold" id="underKJonstructionModalLabel">Under KJonstruction!</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick='triggerCookie()'></button>
            </div>
            <div class="modal-body ctext cbg fw-normal">
                <div class="px-2">
                    <p class="text-center">Wir arbeiten aktuell noch an dieser Webseite. <i class="bi bi-cone-striped"></i><br>
                    Nicht wundern wenn noch nicht alles funktioniert.<br>
                    Feedback trotzdem gerne an:<br>
                    <a href="mailto:entwicklung@kj-dvrs.de" class="link">entwicklung@kj-dvrs.de</a>
                    </p>
                </div>
            </div>
            <div class="modal-footer ctext cbg fw-bold justify-content-center">
                <button type="button" class="btn btn-kolping" data-bs-dismiss="modal" onclick='setCookie("acceptKJonstruction", "true", 1); triggerCookie()'>Okay</button>
            </div>
        </div>
    </div>
</div>

<?php if (!check_kjonstruction_cookie()): ?>
    <script type="text/javascript">
        function triggerKJ() {
            const underKJonstruction = new bootstrap.Modal('#underKJonstructionModal');
            const underKJonstructionToggle = document.getElementById('underKJonstructionModal');
            underKJonstruction.show(underKJonstructionToggle);
        }
        setTimeout(triggerKJ, 2000);
    </script>
<?php endif; ?>

<?php if (check_kjonstruction_cookie() && !check_cookie()): ?>
    <script type="text/javascript">
        function triggerCookie() {
            const myModal = new bootstrap.Modal('#cookieModal');
            const modalToggle = document.getElementById('cookieModal');
            myModal.show(modalToggle);
        }

        setTimeout(triggerCookie, 2000);
    </script>
<?php elseif (!check_cookie()): ?>
    <script type="text/javascript">
        function triggerCookie() {
            const myModal = new bootstrap.Modal('#cookieModal');
            const modalToggle = document.getElementById('cookieModal');
            myModal.show(modalToggle);
        }
    </script>

<?php else: ?>
    <script type="text/javascript">
        function triggerCookie() {}
    </script>
<?php endif; ?>