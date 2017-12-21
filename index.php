<?php
	error_reporting(0);
	// Ruhum
	// 21.12.2017
?>
<!DOCTYPE html>
<html>
<head>
	<title>SQL Backup</title>
	<link rel="stylesheet" type="text/css" href="Style.css" />
</head>
<body>

<?php
	
	$Host = 'SQL_HOST_REPLACE';
	$SQLUser = 'SQL_USER_NAME_REPLACE';
	$SQLPass = 'SQL_PASSWORD_REPLACE';
	$SQLName = 'SQL_NAME_REPLACE';
	$BackupTable = '*'; // Example: * or wp_options, wp_options, wp_posts

	$Connect = mysql_connect($Host, $SQLUser, $SQLPass);
	mysql_select_db($SQLName, $Connect);
	
	mysql_query("SET NAMES 'UTF8'");

	if($BackupTable == '*'){

		$Tablolar = array();
		$result = mysql_query('SHOW TABLES');
		while($Satirlar = mysql_fetch_row($result)){

			$Tablolar[] = $Satirlar[0];

		}

	}else{

		$Tablolar = is_array($BackupTable) ? $BackupTable : explode(',',$BackupTable);

	}
	

	$Sonuclar = '';
	foreach($Tablolar as $Tablo){

		$result = mysql_query('SELECT * FROM '.$Tablo);
		$num_fields = mysql_num_fields($result);
		
		echo 'Tables: '.$Tablo.'<br />';

		$Sonuclar.= 'DROP TABLE '.$Tablo.';';
		$Satirlar2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$Tablo));
		$Sonuclar.= "\n\n".$Satirlar2[1].";\n\n";
		$Say = 0;

		for ($i = 0; $i < $num_fields; $i++){

			while($Satirlar = mysql_fetch_row($result)){

				$Sonuclar.= 'INSERT INTO '.$Tablo.' VALUES(';
				for($j=0; $j < $num_fields; $j++){

					$Satirlar[$j] = addslashes($Satirlar[$j]);
					$Satirlar[$j] = str_replace("\n","\\n",$Satirlar[$j]);

					if (isset($Satirlar[$j])) { 

						$Sonuclar.= '"'.$Satirlar[$j].'"' ; 

					}else{ 

						$Sonuclar.= '""'; 

					}

					if ($j < ($num_fields-1)) { 

						$Sonuclar.= ','; 

					}
				}

				$Sonuclar.= ");\n";
				$Say++;

			}
		}

		$Sonuclar.="\n\n\n";
		echo 'Total Data in Table: '.number_format($Say).'<br /><br />';

	}
	
	$DosyaAdi = date('d-m-Y-H-i').'.sql'; // SQL Backup File Name
	
	$Handle = fopen($DosyaAdi,'w+');
	fwrite($Handle,$Sonuclar);
	fclose($Handle);

	echo 'Download Backup File: <a href="'.$DosyaAdi.'">Download</a><br /> ';
	echo 'Backup Size: '.filesize($DosyaAdi).' Byte<br />';

?>

</body>
</html>