<?php chdir ($_SERVER['DOCUMENT_ROOT']); ?>
<div class="container-fluid" style="height: 80vh;">
    <div class="position-absolute top-50 start-50 translate-middle">
        <h1 class="text-danger text-center">Fehler</h1>
        <p class="ctext text-center"><?php echo $error_msg;?></p>
        <div class="d-flex justify-content-center">
            <?php if (check_user() != null): ?>
            <button type="button" class="btn btn-kolping mx-2" onclick='location.href="/logout.php"'>Abmelden</button>
            <?php endif; ?>
            <button type="button" class="btn btn-kolping mx-2" onclick='location.href="/index.php"'>Zurück zur Hauptseite</button>
        </div>
    </div>
</div>
<script src="/js/custom.js"></script>