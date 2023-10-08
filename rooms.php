<?php
chdir ($_SERVER['DOCUMENT_ROOT']);
require_once("php/functions.php");
ob_start();
require_once("templates/header.php");
$buffer=ob_get_contents();
ob_end_clean();

$title = "Verbandsspiel Kolpingjugend DVRS - Räume";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
if (!isset($user) || $user != true) {
    print("<script>location.href='/login.php'</script>");
    exit;
}
if (!isset($_GET['kj_id'])) {
    print("<script>location.href='/login.php'</script>");
    exit;
}

if ($_GET['kj_id'] != $user['kolpingjugend_id']) {
    print("<script>location.href='/index.php'</script>");
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM kolpingjugend WHERE kolpingjugend_id = ?");
$stmt->bindValue(1, $_GET['kj_id']);
$stmt->execute();
if ($stmt->rowCount() != 1) {
    error_log("Fehler beim Abfragen der Datenbank - Fehler: 1 ".$_GET['kj_id']);
    exit;
}
$kj = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM houses WHERE kolpingjugend_id = ?");
$stmt->bindValue(1, $_GET['kj_id']);
$stmt->execute();
if ($stmt->rowCount() != 1) {
    error_log("Fehler beim Abfragen der Datenbank - Fehler: 2 ".$_GET['kj_id']);
    exit;
}
$kj_house = $stmt->fetch();


$stmt = $pdo->prepare("SELECT * FROM rooms WHERE house_id = ?");
$stmt->bindValue(1, $kj_house['house_id']);
$stmt->execute();
if ($stmt->rowCount() < 16) {
    error_log("Fehler beim Abfragen der Datenbank - Fehler: 3.1");
    exit;
}
$kj_rooms = $stmt->fetchAll();
$kj_rooms_aufg1 = array_slice($kj_rooms, 0, 6);
#$kj_rooms_aufg2 = array_slice($kj_rooms, 6, 5);
#$kj_rooms_aufg3 = array_slice($kj_rooms, 11, 5);

$rooms_done = 0;
foreach ($kj_rooms as $room) {
    if ($room['room_done'] == 1) {
        $rooms_done += 1;
    }
}

if (isset($_GET['room_id'])) {
    doRoom($_GET['kj_id'], $_GET['room_id'], $pdo);
    exit;
}


if(isset($_POST['action'])) {
    if ($_POST['action'] == 'save') {
        if ($user['perm_edit_kj'] != 1) {
            error('Unzureichende Berechtigungen!');
        }
        $stmt = $pdo->prepare('UPDATE rooms SET text = ?, room_solved = 1 WHERE room_id = ?');
        $stmt->bindValue(1, $_POST['textinput']);
        $stmt->bindValue(2, $_POST['room_id'], PDO::PARAM_INT);
        $result = $stmt->execute();
        if (!$result) {
            error('Datenbank Fehler!', pdo_debugStrParams($stmt));
        }
        $stmt = $pdo->prepare('SELECT * FROM solution_pics where room_id = ?');
        $stmt->bindValue(1, $_POST['room_id'], PDO::PARAM_INT);
        $result = $stmt->execute();
        if (!$result) {
            error('Datenbank Fehler!', pdo_debugStrParams($stmt));
        }
        $imgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // DelImgs
        for ($x = 0; $x < count($imgs); $x++) {
            $var = 'delImage-'.$x;
            if (isset($_POST[$var])) {
                #del
                $stmt = $pdo->prepare('SELECT solution_pic_path, solution_pic_id FROM solution_pics where solution_pic_id = ? and room_id = ?');
                $stmt->bindValue(1, $_POST[$var], PDO::PARAM_INT);
                $stmt->bindValue(2, $_POST['room_id'], PDO::PARAM_INT);
                $result = $stmt->execute();
                if (!$result) {
                    error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                }   
                $delImgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                delRoomImgs($delImgs);            
            }
        }
        for ($x = 0; $x < count($imgs); $x++) {
            $imgOwner = 'imgOwner-'.$x;
            $imgAlt = 'imgAlt-'.$x;
            $id = 'room_image_id-'.$x;
            $stmt = $pdo->prepare('UPDATE solution_pics SET `owner` = ?, alt = ? where solution_pic_id = ? and room_id = ?');
            $stmt->bindValue(1, $_POST[$imgOwner]);
            $stmt->bindValue(2, $_POST[$imgAlt]);
            $stmt->bindValue(3, $_POST[$id], PDO::PARAM_INT);
            $stmt->bindValue(4, $_POST['room_id'], PDO::PARAM_INT);
            $result = $stmt->execute();
            if (!$result) {
                error('Datenbank Fehler!', pdo_debugStrParams($stmt));
            }            
        }
        // File Upload
        if (!empty($_FILES["file"]["name"][0])){
            $allowTypes = array('jpg','png','jpeg','gif','JPG','PNG','JPEG','GIF');
            $fileCount = count($_FILES['file']['name']);
            // für jedes Bild
            for($i = 0; $i < $fileCount; $i++){
                // Bild wird zum Abspeichern mit einer Einmaligen ID + Uhrsprungsame versehen
                $fileName = uniqid('image_') . '_' . basename($_FILES["file"]["name"][$i]);
                $fileName = str_replace(str_split(' '), '_', $fileName);
                // $fileName = $fileName.str_replace(" ", "_", $fileName);
                $targetFilePath = "images/solutions/" . $fileName;
                if(in_array(pathinfo($targetFilePath,PATHINFO_EXTENSION), $allowTypes)){
                    // Hochladen der Bilder
                    if(move_uploaded_file($_FILES["file"]["tmp_name"][$i], $targetFilePath)){
                        // Einpflegen der Bilder in die Datenbank
                        $hash = md5_file("images/solutions/" . $fileName);
                        $imgAlt = 'imgAlt-'.$hash;
                        if (isset($_POST[$imgAlt])) {
                            $imgAlt = $_POST[$imgAlt];
                        } else {
                            $imgAlt = $_POST['room_id'];
                        }
                        $imgOwner = 'imgOwner-'.$hash;
                        if (isset($_POST[$imgOwner])) {
                            $imgOwner = $_POST[$imgOwner];
                        } else {
                            $imgOwner = $_POST['room_id'];
                        }
                        $stmt = $pdo->prepare("INSERT into solution_pics (room_id, solution_pic_path, alt, owner) VALUES ( ? , ? , ? , ? )");
                        $stmt->bindValue(1, $_POST['room_id']);
                        $stmt->bindValue(2, "/images/solutions/" . $fileName);
                        $stmt->bindValue(3, $imgAlt);
                        $stmt->bindValue(4, $imgOwner);
                        $result = $stmt->execute();
                        if (!$result) {
                            error_log(print_r($stmt, true));
                            error('Datenbank Fehler!', pdo_debugStrParams($stmt));
                        } else {
                            convertToWEBP($targetFilePath);
                        }
                        if (!$stmt) {
                            error("Hochladen Fehlgeschlagen");
                        } 
                    } else {
                        error("Hochladen Fehlgeschlagen (3)");
                    }
                } else {
                    error('Wir unterstützen nur JPG, JPEG, PNG & GIF Dateien.');
                }
            }
        }
        print("<script>location.href='rooms.php?kj_id=".$_POST['kj_id']."'</script>");
        exit;
    }

    // Wenn action "mod" ist
    if($_POST['action'] == 'mod') {
        // Zeigt die Error Seite wenn der User keine Berechtigungen hat
        if ($user['perm_edit_kj'] != 1) {
            error('Unzureichende Berechtigungen!');
        }
        // Wenn action "set_done" ist
        if($_POST['todo'] == 'abgeben') {
            doRoom($_POST['kj_id'], $_POST['room_id'], $pdo);
        }
    }

    // Wenn action "del" ist
    if($_POST['action'] == 'del') {
        // Zeigt die Error Seite wenn der User keine Berechtigungen hat
        if ($user['perm_edit_kj'] != 1) {
            error('Unzureichende Berechtigungen!');
        }
        // Wenn action "set_done" ist
        if($_POST['todo'] == 'del') {
            $stmt = $pdo->prepare('SELECT * FROM solution_pics where room_id = ?');
            $stmt->bindValue(1, $_POST['room_id'], PDO::PARAM_INT);
            $result = $stmt->execute();
            if (!$result) {
                error('Datenbank Fehler!', pdo_debugStrParams($stmt));
            }
            $imgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            delRoomImgs($imgs);
            $stmt = $pdo->prepare('UPDATE rooms SET text = "text", room_solved = 0, room_done = 0 WHERE room_id = ?');
            $stmt->bindValue(1, $_POST['room_id'], PDO::PARAM_INT);
            $result = $stmt->execute();
            if (!$result) {
                error('Datenbank Fehler!', pdo_debugStrParams($stmt));
            }
            print("<script>location.href='rooms.php?kj_id=".$_POST['kj_id']."'</script>");
            exit;
        }
    }
}


function doRoom($kj_id, $room_id, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->bindValue(1, $room_id);
    $stmt->execute();
    if ($stmt->rowCount() != 1) {
        error_log("Fehler beim Abfragen der Datenbank - Fehler: 1.2");
        exit;
    }
    $kj_room = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT * FROM solution_pics WHERE room_id = ?");
    $stmt->bindValue(1, $room_id);
    $result = $stmt->execute();
    if (!$result) {
        error_log("Fehler beim Abfragen der Datenbank - Fehler: 1.3");
        exit;
    }
    $room_pics = $stmt->fetchAll();
    
    if ($kj_room['room_done'] == 0): ?>
    <script src='/js/md5.js'></script>
    <div class="container-xxl p-0" style="min-height: 80vh;">
        <div class="row row-cols-1 m-3 cbg2 rounded">
            <form action="rooms.php?kj_id=<?=$kj_id?>" method="post" enctype="multipart/form-data">
                <?php if (!isMobile()):?>
                    <div class="col p-2 pt-3 rounded justify-content-between d-flex">
                            <h3><?=$kj_room['room_name']?></h3>
                            <div class="justify-content-end d-flex">
                                <input type="number" value="<?=$kj_id?>" name="kj_id" style="display: none;" required>
                                <input type="number" value="<?=$kj_room['room_id']?>" name="room_id" style="display: none;" required>
                                <button type="submit" class="btn btn-success ctext me-2" name="action" value="save" onclick="blocker = false;"><i class="bi bi-floppy text-light"></i></button>
                                <button type="button" class="btn btn-danger ctext ms-2" onclick="blocker = false; window.location.href = 'rooms.php?kj_id=<?=$kj_id?>';"><i class="bi bi-x-circle text-light"></i></button>
                            </div>
                    </div>
                <?php else: ?>
                    <div class="col p-2 pt-3 rounded d-flex justify-content-between">
                        <h3><?=$kj_room['room_name']?></h3>
                        <div class="col-4 d-flex justify-content-end align-content-center">
                            <input type="number" value="<?=$kj_id?>" name="kj_id" style="display: none;" required>
                            <input type="number" value="<?=$kj_room['room_id']?>" name="room_id" style="display: none;" required>
                            <button type="submit" class="btn btn-success ctext me-1" name="action" value="save" onclick="blocker = false;"><i class="bi bi-floppy text-light"></i></button>
                            <button type="button" class="btn btn-danger ctext ms-1" onclick="blocker = false; window.location.href = 'rooms.php?kj_id=<?=$kj_id?>';"><i class="bi bi-x-circle text-light"></i></button>
                        </div>
                        
                    </div>
                <?php endif; ?>
                <div class="col p-2 rounded">
                    <textarea class="form-control cbg ctext" name="textinput" id="textinput" rows="10" placeholder="Text"><?=$kj_room["text"]?></textarea>
                </div>
                <div class="col p-2 rounded d-flex">
                    <div class="input-group cbg ctext">
                        <input type="file" class="form-control" id="PicUpload" name="file[]" accept="image/png, image/gif, image/jpeg" multiple onchange="showPreview(event);">
                        <label class="input-group-text" for="PicUpload">Bilder Hochladen</label>
                    </div>
                </div>
                <div class="col p-2 rounded">
                    <h2>Diese Bilder werden Hochgeladen:</h2>
                    <div class="row row-cols-<?php if (!isMobile()) print("4"); else print("1"); ?> row-cols-md-4 g-4 py-2" id="preview">
                    </div>
                    <h2>Diese Bilder sind bereits Hochgeladen:</h2>
                    <div class="row row-cols-<?php if (!isMobile()) print("4"); else print("1"); ?> row-cols-md-4 g-4 py-2">
                        <?php for ($x = 0; $x < count($room_pics); $x++) :?>
                            <div class="col">
                                <div class="card cbg">
                                    <img src="<?=$room_pics[$x]['solution_pic_path']?>" class="card-img-top img-fluid rounded" alt="<?=$room_pics[$x]['alt']?>">
                                    <div class="card-body">
                                    <input type="number" value="<?=$room_pics[$x]['solution_pic_id']?>" name="<?='room_image_id-'.$x?>" style="display: none;" required>
                                        <div class="input-group pb-2">
                                            <span class="input-group-text" id="basic-addon1">Quelle</span>
                                            <input type="text" class="form-control" placeholder="Quelle" value="<?=$room_pics[$x]['owner']?>" name="<?='imgOwner-'.$x?>">
                                        </div>
                                        <div class="input-group py-2">
                                            <span class="input-group-text" id="basic-addon1">Text</span>
                                            <input type="text" class="form-control" placeholder="Text" value="<?=$room_pics[$x]['alt']?>" name="<?='imgAlt-'.$x?>">
                                        </div>
                                        <div class="input-group py-2 d-flex justify-content-center">
                                            <span class="input-group-text" for="inputVisible">Löschen?</span>
                                            <div class="input-group-text">
                                                <input type="checkbox" class="form-check-input checkbox-kolping" value="<?=$room_pics[$x]['solution_pic_id']?>" name="<?='delImage-'.$x?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor;?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
    endif;
    require_once("templates/footer.php");
    exit;
} 




















    // show room Overview
    ?>
    <div class="container p-3">
        <div style="min-height: 75vh;">
            <div class="d-flex justify-content-between">
                <div class="col">
                    <h1><?=$kj_house['house_name']?></h1>
                </div>
            </div>
            <p>Räume Erledigt: <?php print($rooms_done);?></p>
            <div class="row row-cols-1 gy-4">
                <?php foreach ($kj_rooms_aufg1 as $room): ?>
                    <div class="col">
                        <div class="card cbg2">
                            <div class="card-body p-3">
                                <h4 class="ctext card-title"><?=$room['room_name']?></h4>
                                <div class="card-text">
                                    <div class="row" id="<?=$room['room_id']?>">
                                        <div class="col-6 justify-content-start">
                                            <?php if ($room['room_done'] == 1) print('<button class="btn btn-success ctext m-0">Erledigt</button>');
                                            else if ($room['room_solved'] == 1) print('<button class="btn btn-kolping ctext m-0">Eingereicht</button>');
                                            else print('<button class="btn btn-secondary ctext m-0">Offen</button>');?>
                                        </div>
                                        <form action="rooms.php?kj_id=<?=$kj['kolpingjugend_id']?>" method="post" class="col-6 d-flex justify-content-end">
                                            <div class="">
                                                <input type="number" value="<?=$kj['kolpingjugend_id']?>" name="kj_id" style="display: none;" required>
                                                <input type="number" value="<?=$room['room_id']?>" name="room_id" style="display: none;" required>
                                                <?php if ($room['room_solved'] == 1 && $room['room_done'] == 0) print('<input type="text" value="abgeben" name="todo" style="display: none;" required><button type="submit" name="action" value="mod" class="btn btn-kolping ctext">Einreichung Editieren</button>');
                                                    else if ($room['room_done'] == 0) print('<input type="text" value="abgeben" name="todo" style="display: none;" required><button type="submit" name="action" value="mod" class="btn btn-success ctext">Aufgabe abgeben</button>');
                                                    else print('<input type="text" value="del" name="todo" style="display: none;" required><button type="submit" name="action" value="del" class="btn btn-danger ctext">Einreichung zurückziehen</button>');
                                                ?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach?>
                <!-- <?php #foreach ($kj_rooms_aufg2 as $room): ?>
                    <div class="col">
                        <div class="card cbg2">
                            <div class="card-body p-3">
                                <h4 class="ctext card-title"><?=$room['room_name']?></h4>
                                <div class="card-text">
                                    <div class="row" id="<?=$room['room_id']?>">
                                        <div class="col-6 justify-content-start">
                                            <?php if ($room['room_done'] == 1) print('<button class="btn btn-success ctext m-0">Erledigt</button>');
                                            else if ($room['room_solved'] == 1) print('<button class="btn btn-kolping ctext m-0">Eingereicht</button>');
                                            else print('<button class="btn btn-secondary ctext m-0">Offen</button>');?>
                                        </div>
                                        <form action="rooms.php?kj_id=<?=$kj['kolpingjugend_id']?>" method="post" class="col-6 d-flex justify-content-end">
                                            <div class="">
                                                <input type="number" value="<?=$kj['kolpingjugend_id']?>" name="kj_id" style="display: none;" required>
                                                <input type="number" value="<?=$room['room_id']?>" name="room_id" style="display: none;" required>
                                                <?php if ($room['room_solved'] == 1 && $room['room_done'] == 0) print('<input type="text" value="abgeben" name="todo" style="display: none;" required><button type="submit" name="action" value="mod" class="btn btn-kolping ctext">Einreichung Editieren</button>');
                                                    else if ($room['room_done'] == 0) print('<input type="text" value="abgeben" name="todo" style="display: none;" required><button type="submit" name="action" value="mod" class="btn btn-success ctext">Aufgabe abgeben</button>');
                                                    else print('<input type="text" value="del" name="todo" style="display: none;" required><button type="submit" name="action" value="del" class="btn btn-danger ctext">Einreichung zurückziehen</button>');                                                ?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php #endforeach?> -->
                <!-- <?php #foreach ($kj_rooms_aufg3 as $room): ?>
                    <div class="col">
                        <div class="card cbg2">
                            <div class="card-body p-3">
                                <h4 class="ctext card-title"><?=$room['room_name']?></h4>
                                <div class="card-text">
                                    <div class="row" id="<?=$room['room_id']?>">
                                        <div class="col-6 justify-content-start">
                                            <?php if ($room['room_done'] == 1) print('<button class="btn btn-success ctext m-0">Erledigt</button>');
                                            else if ($room['room_solved'] == 1) print('<button class="btn btn-kolping ctext m-0">Eingereicht</button>');
                                            else print('<button class="btn btn-secondary ctext m-0">Offen</button>');?>
                                        </div>
                                        <form action="rooms.php?kj_id=<?=$kj['kolpingjugend_id']?>" method="post" class="col-6 d-flex justify-content-end">
                                            <div class="">
                                                <input type="number" value="<?=$kj['kolpingjugend_id']?>" name="kj_id" style="display: none;" required>
                                                <input type="number" value="<?=$room['room_id']?>" name="room_id" style="display: none;" required>
                                                <?php if ($room['room_solved'] == 1 && $room['room_done'] == 0) print('<input type="text" value="abgeben" name="todo" style="display: none;" required><button type="submit" name="action" value="mod" class="btn btn-kolping ctext">Einreichung Editieren</button>');
                                                    else if ($room['room_done'] == 0) print('<input type="text" value="abgeben" name="todo" style="display: none;" required><button type="submit" name="action" value="mod" class="btn btn-success ctext">Aufgabe abgeben</button>');
                                                    else print('<input type="text" value="del" name="todo" style="display: none;" required><button type="submit" name="action" value="del" class="btn btn-danger ctext">Einreichung zurückziehen</button>');                                                ?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php #endforeach?> -->
            </div>
            <div class="justify-content-center d-flex mt-4">
                <button class="btn btn-danger m-0" aria-label="Abbrechen" onclick="window.location.href = '/kolpingjugend.php?id=<?=$_GET['kj_id']?>';"><i class="bi bi-x-circle text-light"></i></button>
            </div>
        </div>
    </div>

    <?php
    require_once("templates/footer.php");
    exit;
?>