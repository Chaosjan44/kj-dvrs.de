<?php 
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");

ob_start();
require_once("templates/header.php"); 
$buffer=ob_get_contents();
ob_end_clean();

$title = "ADMIN - Verbandsspiel Kolpingjugend DVRS - Kolpingjugend anlegen";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;

if (!isset($user)) {
    print("<script>location.href='/login.php'</script>");
    exit;
}
if ($user['perm_admin'] != 1) {
    errorPage('Unzureichende Berechtigungen!');
}

$error_msg = "";
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'register') {
        if(isset($_POST['kj_name']) && !empty($_POST['kj_name']) && isset($_POST['kj_ort']) && !empty($_POST['kj_ort'])) {
            $kj_name = $_POST['kj_name'];
            $kj_ort = $_POST['kj_ort'];

            $stmt = $pdo->prepare("INSERT INTO kolpingjugend SET kolpingjugend_name = ?, kolpingjugend_ort = ?");
            $stmt->bindValue(1, $kj_name);
            $stmt->bindValue(2, $kj_ort);
            $result = $stmt->execute();
            if (!$result) {
                error_log("Error #1 while registering kj");
                exit;
            }
            $error_msg = "<span class='text-success'>Kolpingjugend erfolgreich angelegt. :)<br><br></span>";
            echo("<script>location.href='kolpingjugenden.php'</script>");

            $stmt = $pdo->prepare("SELECT kolpingjugend_id FROM kolpingjugend WHERE kolpingjugend_name = ? AND kolpingjugend_ort = ?");
            $stmt->bindValue(1, $kj_name);
            $stmt->bindValue(2, $kj_ort);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while registering kj");
                exit;
            }
            $kjID_tmp = $stmt->fetch();
            $kj_id = $kjID_tmp['kolpingjugend_id'];
            $stmt = $pdo->prepare("INSERT INTO houses SET kolpingjugend_id = ?, house_name = ?, house_address = ?");
            $stmt->bindValue(1, $kj_id);
            $stmt->bindValue(2, $kj_name);
            $stmt->bindValue(3, 'Streng Geheim');
            $result1 = $stmt->execute();
            if (!$result1) {
                error_log("Error #3 while registering kj");
                exit;
            }
            $stmt = $pdo->prepare("SELECT house_id FROM houses WHERE kolpingjugend_id = ?");
            $stmt->bindValue(1, $kj_id);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #4 while registering kj");
                exit;
            }
            $houseid = $stmt->fetch();
            $random = generateRandomString(16);
            // Create room 1
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum1'.$random);
            $result2 = $stmt->execute();
            if (!$result2) {
                error_log("Error #1 while creating room 1");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum1'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 1");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Kreativer Raum');
            $stmt->bindValue(2, $roomid['room_id']);
            $result3 = $stmt->execute();
            if (!$result3) {
                error_log("Error #3 while creating room 1");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result4 = $stmt->execute();
            if (!$result4) {
                error_log("Error #4 while creating room 1");
                exit;
            }
            // Create room 2
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum2'.$random);
            $result5 = $stmt->execute();
            if (!$result5) {
                error_log("Error #1 while creating room 2");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum2'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 2");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Partykeller');
            $stmt->bindValue(2, $roomid['room_id']);
            $result6 = $stmt->execute();
            if (!$result6) {
                error_log("Error #3 while creating room 2");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result7 = $stmt->execute();
            if (!$result7) {
                error_log("Error #4 while creating room 2");
                exit;
            }
            // Create room 3
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum3'.$random);
            $result8 = $stmt->execute();
            if (!$result8) {
                error_log("Error #1 while creating room 3");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum3'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 3");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Wohnzimmer & Heimkino');
            $stmt->bindValue(2, $roomid['room_id']);
            $result9 = $stmt->execute();
            if (!$result9) {
                error_log("Error #3 while creating room 3");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result7 = $stmt->execute();
            if (!$result7) {
                error_log("Error #4 while creating room 3");
                exit;
            }
            // Create room 4
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum4'.$random);
            $result10 = $stmt->execute();
            if (!$result10) {
                error_log("Error #1 while creating room 4");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum4'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 4");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Essküche');
            $stmt->bindValue(2, $roomid['room_id']);
            $result11 = $stmt->execute();
            if (!$result11) {
                error_log("Error #3 while creating room 4");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result12 = $stmt->execute();
            if (!$result12) {
                error_log("Error #4 while creating room 4");
                exit;
            }
            // Create room 5
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum5'.$random);
            $result13 = $stmt->execute();
            if (!$result13) {
                error_log("Error #1 while creating room 5");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum5'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 5");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Schlafzimmer');
            $stmt->bindValue(2, $roomid['room_id']);
            $result14 = $stmt->execute();
            if (!$result14) {
                error_log("Error #3 while creating room 5");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result15 = $stmt->execute();
            if (!$result15) {
                error_log("Error #4 while creating room 5");
                exit;
            }
            // Create room 6
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum6'.$random);
            $result16 = $stmt->execute();
            if (!$result16) {
                error_log("Error #1 while creating room 6");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum6'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 6");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Atelier');
            $stmt->bindValue(2, $roomid['room_id']);
            $result17 = $stmt->execute();
            if (!$result17) {
                error_log("Error #3 while creating room 6");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result18 = $stmt->execute();
            if (!$result18) {
                error_log("Error #4 while creating room 6");
                exit;
            }
            // Create room 7
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum7'.$random);
            $result19 = $stmt->execute();
            if (!$result19) {
                error_log("Error #1 while creating room 7");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum7'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 7");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Garten');
            $stmt->bindValue(2, $roomid['room_id']);
            $result20 = $stmt->execute();
            if (!$result20) {
                error_log("Error #3 while creating room 7");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result21 = $stmt->execute();
            if (!$result21) {
                error_log("Error #4 while creating room 7");
                exit;
            }
            // Create room 8
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum8'.$random);
            $result22 = $stmt->execute();
            if (!$result22) {
                error_log("Error #1 while creating room 8");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum8'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 8");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Werkstatt');
            $stmt->bindValue(2, $roomid['room_id']);
            $result23 = $stmt->execute();
            if (!$result23) {
                error_log("Error #3 while creating room 8");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result24 = $stmt->execute();
            if (!$result24) {
                error_log("Error #4 while creating room 8");
                exit;
            }
            // Create room 9
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum9'.$random);
            $result25 = $stmt->execute();
            if (!$result25) {
                error_log("Error #1 while creating room 9");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum9'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 9");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Fitnessraum');
            $stmt->bindValue(2, $roomid['room_id']);
            $result26 = $stmt->execute();
            if (!$result26) {
                error_log("Error #3 while creating room 9");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result27 = $stmt->execute();
            if (!$result27) {
                error_log("Error #4 while creating room 9");
                exit;
            }
            // Create room 10
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum10'.$random);
            $result28 = $stmt->execute();
            if (!$result28) {
                error_log("Error #1 while creating room 10");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum10'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 10");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Spielzimmer');
            $stmt->bindValue(2, $roomid['room_id']);
            $result29 = $stmt->execute();
            if (!$result29) {
                error_log("Error #3 while creating room 10");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result30 = $stmt->execute();
            if (!$result30) {
                error_log("Error #4 while creating room 10");
                exit;
            }
            // Create room 11
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum11'.$random);
            $result31 = $stmt->execute();
            if (!$result31) {
                error_log("Error #1 while creating room 11");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum11'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 11");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Musikzimmer');
            $stmt->bindValue(2, $roomid['room_id']);
            $result32 = $stmt->execute();
            if (!$result32) {
                error_log("Error #3 while creating room 11");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result33 = $stmt->execute();
            if (!$result33) {
                error_log("Error #4 while creating room 11");
                exit;
            }
            // Create room 12
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum12'.$random);
            $result34 = $stmt->execute();
            if (!$result34) {
                error_log("Error #1 while creating room 12");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum12'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 12");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Garderobe');
            $stmt->bindValue(2, $roomid['room_id']);
            $result35 = $stmt->execute();
            if (!$result35) {
                error_log("Error #3 while creating room 12");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result36 = $stmt->execute();
            if (!$result36) {
                error_log("Error #4 while creating room 12");
                exit;
            }
            // Create room 13
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum13'.$random);
            $result37 = $stmt->execute();
            if (!$result37) {
                error_log("Error #1 while creating room 13");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum13'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 13");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Boulderwand');
            $stmt->bindValue(2, $roomid['room_id']);
            $result38 = $stmt->execute();
            if (!$result38) {
                error_log("Error #3 while creating room 13");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result39 = $stmt->execute();
            if (!$result39) {
                error_log("Error #4 while creating room 13");
                exit;
            }
            // Create room 14
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum14'.$random);
            $result40 = $stmt->execute();
            if (!$result40) {
                error_log("Error #1 while creating room 14");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum14'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 14");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Bad');
            $stmt->bindValue(2, $roomid['room_id']);
            $result41 = $stmt->execute();
            if (!$result41) {
                error_log("Error #3 while creating room 14");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result42 = $stmt->execute();
            if (!$result42) {
                error_log("Error #4 while creating room 14");
                exit;
            }
            // Create room 15
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum15'.$random);
            $result43 = $stmt->execute();
            if (!$result43) {
                error_log("Error #1 while creating room 15");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum15'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 15");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Arbeitszimmer');
            $stmt->bindValue(2, $roomid['room_id']);
            $result44 = $stmt->execute();
            if (!$result44) {
                error_log("Error #3 while creating room 15");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result45 = $stmt->execute();
            if (!$result45) {
                error_log("Error #4 while creating room 15");
                exit;
            }
            // Create room 16
            $stmt = $pdo->prepare("INSERT INTO rooms SET room_name = ?, room_done = 0");
            $stmt->bindValue(1, 'Raum16'.$random);
            $result46 = $stmt->execute();
            if (!$result46) {
                error_log("Error #1 while creating room 16");
                exit;
            }
            $stmt = $pdo->prepare("SELECT room_id FROM rooms where room_name = ?");
            $stmt->bindValue(1, 'Raum16'.$random);
            $stmt->execute();
            if ($stmt->rowCount() != 1) {
                error_log("Error #2 while creating room 16");
                exit;
            }
            $roomid = $stmt->fetch();
            $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
            $stmt->bindValue(1, 'Dachkapelle');
            $stmt->bindValue(2, $roomid['room_id']);
            $result47 = $stmt->execute();
            if (!$result47) {
                error_log("Error #3 while creating room 16");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO house_room SET house_id = ?, room_id = ?");
            $stmt->bindValue(1, $houseid['house_id']);
            $stmt->bindValue(2, $roomid['room_id']);
            $result48 = $stmt->execute();
            if (!$result48) {
                error_log("Error #4 while creating room 16");
                exit;
            }
            $error_msg = "<span class='text-success'>Kolpingjugend erfolgreich angelegt. :)<br><br></span>";
        } else {
            $error_msg = "<span class='text-danger'>Es müssen alle Felder ausgefüllt werden!<br><br></span>";
        }
    } 
}

?>
<div class="container py-3">
	<div class="row justify-content-center" style="min-height: 75vh;">
		<div class="col">
			<div class="card cbg2">
                <div class="card-body">
                    <h3 class="card-title display-3 text-center mb-4 text-kolping-orange">Kolpingjugend anlegen</h3>
                    <div class="card-text">
                        <?=$error_msg?>
                        <form action="registerkj.php" method="post">
                            <div class="form-floating mb-3">
                                <input id="kjName" type="text" name="kj_name" placeholder="Kolpingjugend Name" autofocus class="form-control border-0 ps-4 text-dark fw-bold" required>
                                <label for="kjName" class="text-dark fw-bold">Name der Kolpingjugend</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="kjOrt" type="text" name="kj_ort" placeholder="Kolpingjugend Ort" class="form-control border-0 ps-4 text-dark fw-bold" required>
                                <label for="kjOrt" class="text-dark fw-bold">Ort der Kolpingjugend</label>
                            </div>
                            <div class="col text-center">
                                <button type="submit" name="action" value="register" class="btn btn-kolping btn-floating">Kolpingjugend anlegen</button>
                            </div>
                        </form>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
<?php
include_once("templates/footer.php");
?>