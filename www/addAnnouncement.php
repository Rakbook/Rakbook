<?php

session_start();
if(!isset($_SESSION['loggedin'])&&$_SESSION['loggedin']!=true)
{
	header("Location: index.php");
	die();
}

include("requestuserdata.php");
require_once('userinfo.php');

# CHECK IF THE USER HAS JUST POSTED, CHECK THE INPUT, AND ADD TO THE DATABASE
if(isset($_POST['ogloszenietitle'])&&isset($_POST['ogloszenietekst']))
{
		$title=$_POST['ogloszenietitle'];
		$content=$_POST['ogloszenietekst'];

		if(strlen($title)<1||strlen($content)<1)
		{
				$_SESSION['ogloszenieadderror']='Musisz wypełnić tytuł i treść';
				# clean $_POST variables
				header('Location: addAnnouncement.php');
				die();
		}else
		{

				$color='blue';
				if(isset($_POST['colorSelector']))
				{
						$color=htmlentities($_POST['colorSelector']);
				}
				$pinned=0;
				if(isset($_POST['pin']))
				{
						$pinned=1;
				}
				$expires=NULL;
				if(isset($_POST['expires'])&&strlen($_POST['expires'])>0)
				{
						$expires=$_POST['expires'];
				}
				$query = "INSERT INTO ogloszenia (title, text, pinned, autor, colorclass, expirydate) VALUES (?, ?, ?, ?, ?, ?)";
				easyQuery($query, "ssiiss", $title, $content, $pinned, $_SESSION['userid'], $color, $expires);

				# redirect to announcments.php after success
				header('Location: announcements.php');
		}
}

?>

<!DOCTYPE html>
<html>
<head>
  <?php require('standardHead.php'); ?>
	<link rel="stylesheet" type="text/css" href="divColors.css">
	<link rel="stylesheet" type="text/css" href="announcements.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="addAnnouncement.js"></script>
</head>
<body>
  <div class="container">
    <?php require('topbar.php') ?>
    <div class="content">
      <div class="headline" style="margin-bottom: 10px;">Dodaj Nowe Ogłoszenie</div>
			<div style="padding: 10px;">

    	<?php

    	if(isset($_SESSION['ogloszenieadderror'])){
      	echo $_SESSION['ogloszenieadderror'];
        unset($_SESSION['ogloszenieadderror']);
    	}

    	?>

    <form action="addAnnouncement.php" method="post">

        <div class="announcementOptinosBar">
        	<div>
            <select name="colorSelector" class="select">
            	<option value="blue">Niebieski</option>
              <option value="red">Czerwony</option>
              <option value="green">Zielony</option>
              <option value="yellow">Żółty</option>
            </select>
          </div>
          <div style="display: flex; align-items: center;">
          	<span style="color:#131218;">Przypiąć?</span><input name="pin" type="checkbox" class="checkbox"></input>
          </div>
				</div>
        <p style="margin: 0; margin-top: 10px;">Zostaw pustą datę jeśli nie chcesz jej określać</p>
        <div class="announcementsField">
        	<div id="announcementPreview" class="announcement blue">
          	<input name="ogloszenietitle" type="text" class="announcementInput" style="text-align: center;" placeholder="Wprowadź tutuł"></input>
            <p class="announcmentTitle" style="font-size: 16px; display: flex; align-items: center; justify-content: center;">Trwa do: &nbsp; <input name="expires" type="date" class="dateInput"></p>
            <div class="announcementContent" style="overflow: hidden;"><textarea name="ogloszenietekst" class="announcementContentInput" placeholder="Tutaj treść..."></textarea></div>
            <div class="announcementInfo"> <div>Opublikowano: <?php echo date('d.m.Y'); ?></div></div>
          </div>
				</div>
        <input type="submit" class="addAnnouncementButton" value="Dodaj ogłoszenie"></input>
      </div>
    </form>
    </div>
  </div>
</body>
</html>
