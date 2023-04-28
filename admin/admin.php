<?php 
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
require_once("templates/header.php");
if (!isset($user)) {
    print("<script>location.href='/login.php'</script>");
    exit;
} 
?>

<?php require_once("templates/footer.php"); ?>