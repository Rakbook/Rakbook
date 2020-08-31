<?php

session_start();

if(isset($_POST['wyslanoformularz'])
	&&isset($_POST['username'])
	&&isset($_POST['password'])
	&&isset($_POST['repeatpassword'])
	&&isset($_POST['nrdziennik']))
{
	$usernametaken=false;
	$usernametoshort=false;
	$nrwdziennikutaken=false;
	$nrwdziennikunotvalid=false;
	$passtoshort=false;
	$passdontmatch=false;
	$registrationsuccess=true;
	
	
	require_once("dbcredentials.php");

	$conn = new MySQLi($dbserver, $dbuser, $dbpassword, $dbname);

	if($conn->connect_errno)
	{
		die("Nie udało się połączyć z bazą danych");
		die("Coś się zepsuło");
	}
	
	
	if(!$conn->set_charset("utf8"))
	{
		die("Nie udało się ustawić kodowania na utf-8");
		die("Coś się zepsuło");
	}
	
	$query="SELECT id FROM users WHERE user_name=?";
	if(!$stmt=$conn->prepare($query))
	{
		die("Nie udało się przygotować zapytania");
		die("Coś się zepsuło");
	}
	
	
	if(!$stmt->bind_param("s", $_POST['username']))
	{
		die("Nie udało się dołączyć parametru");
		die("Coś się zepsuło");
	}
	
	if(!$stmt->execute())
	{
		die("Nie udało się wykonać zapytania");
		die("Coś się zepsuło");
	}
	
	if(!($result=$stmt->get_result()))
	{
		die("Nie udało się pozyskać wyniku zapytania");
		die("Coś się zepsuło");
	}
	
	if($result->num_rows>0)
	{
		$usernametaken=true;
		$registrationsuccess=false;
	}
	
	$stmt->close();
	
	
	$minusernamelength=3;
	$maxusernamelength=10;
	
	if(strlen($_POST['username'])<$minusernamelength||strlen($_POST['username'])>$maxusernamelength)
	{
		$usernametoshort=true;
		$registrationsuccess=false;
	}
	
	
	$dzienniknum=intval($_POST['nrdziennik']);
	$maxdzienniknum=33;
	if($dzienniknum<1||$dzienniknum>$maxdzienniknum)
	{
		if($dzienniknum!=41)
		{
			$nrwdziennikunotvalid=true;
			$registrationsuccess=false;
		}
	}
	
	
	$query="SELECT id FROM users WHERE user_nrwdzienniku=?";
	if(!$stmt=$conn->prepare($query))
	{
		die("Nie udało się przygotować zapytania");
		die("Coś się zepsuło");
	}
	
	
	if(!$stmt->bind_param("i", $_POST['nrdziennik']))
	{
		die("Nie udało się dołączyć parametru");
		die("Coś się zepsuło");
	}
	
	if(!$stmt->execute())
	{
		die("Nie udało się wykonać zapytania");
		die("Coś się zepsuło");
	}
	
	if(!($result=$stmt->get_result()))
	{
		die("Nie udało się pozyskać wyniku zapytania");
		die("Coś się zepsuło");
	}
	
	if($result->num_rows>0)
	{
		$nrwdziennikutaken=true;
		$registrationsuccess=false;
	}
	
	$stmt->close();
	
	$minpasslength=8;
	
	if(strlen($_POST['password'])<$minpasslength)
	{
		$passtoshort=true;
		$registrationsuccess=false;
	}
	
	if($_POST['password']!=$_POST['repeatpassword'])
	{
		$passdontmatch=true;
		$registrationsuccess=false;
	}
	
	
	
	if($registrationsuccess)
	{
		$passhash=password_hash($_POST['password'], PASSWORD_DEFAULT);
		
		
		$query="INSERT INTO users (user_name, user_pass, user_nrwdzienniku) VALUES(?, ?, ?)";
	if(!$stmt=$conn->prepare($query))
	{
		die("Nie udało się przygotować zapytania");
		die("Coś się zepsuło");
	}
	
	
	if(!$stmt->bind_param("ssi", $_POST['username'], $passhash, $dzienniknum))
	{
		die("Nie udało się dołączyć parametru");
		die("Coś się zepsuło");
	}
	
		
		
	if(!$stmt->execute())
	{
		//die($stmt->error);
		die("Nie udało się wykonać zapytania");
		die("Coś się zepsuło");
	}
	$to = getenv('NEW_USER_EMAILS');
	$topic = "Nowy użytkownik!";
	$headers = 'From: donotreply@rakbook.pl';
	$content = "Nowy użytkownik chce dołączyć do Rakbooka! Jego nazwa: ".$_POST['username'].". Jego numer w dzienniku: ".$_POST['nrdziennik']; 
	mail($to, $topic, $content, $headers);
	$stmt->close();
	$conn->close();
	
		$_SESSION['registrationsuccess']=true;
		header('Location: registrationsuccess.php');
		die();
		
	}
	
	
	
	$conn->close();
	
}


?>


<!DOCTYPE html>
<html>
<head>
  <?php require('standardHead.php'); ?>
</head>
<body>
  <div class="Container">

    <div class="Content">
      <div class="Headline">Rakbook Rejestracja</div>
        <form action="" method="post" autocomplete="off">
        Login: &nbsp;<input type="text"  name="username" value="<?php if(isset($_POST['username'])) echo($_POST['username']); ?>"/><br>
            <?php 	if(isset($usernametaken)&&$usernametaken){ echo(" Ta nazwa użytkownika jest zajęta "); }	
					if(isset($usernametoshort)&&$usernametoshort){ echo(" Nazwa musi zawierać od ".$minusernamelength." do ".$maxusernamelength." znaków"); }
					?><br>
        Nr w dzienniku: &nbsp;<input type="number" name="nrdziennik" value="<?php if(isset($dzienniknum)) echo($dzienniknum); ?>"/><br>
            <?php 	if(isset($nrwdziennikutaken)&&$nrwdziennikutaken){ echo(" Ten numer w dzienniku jest zajęty "); }
					if(isset($nrwdziennikunotvalid)&&$nrwdziennikunotvalid){ echo(" Numer w dzienniku musi być liczbą w zakresie od 1 do ".$maxdzienniknum); }
					?><br>
        Hasło: &nbsp;<input type="password" name="password"/><br>
        <?php 	if(isset($passtoshort)&&$passtoshort){ echo(" Hasło musi mieć co najmniej ".$minpasslength." znaków"); } ?><br>
        Powtórz hasło: &nbsp;<input type="password" name="repeatpassword"/><br>
        <?php 	if(isset($passdontmatch)&&$passdontmatch){ echo(" Hasła są różne"); } ?><br>
        <input class="loginbutton" type="submit" name="wyslanoformularz" value="Zarejestruj się"/>
        </form>
    </div>
  </div>
</body>
</html>