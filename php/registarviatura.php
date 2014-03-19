<!DOCTYPE html>
<html>
	<head>
		<title> Ride - Registar Viatura </title>
		<link rel="stylesheet" type="text/css" href="layout.css" />
	</head>
	<body>
		<h1> <a href = 'index.php' class=title>RIDE</a> </h1>
		<?php
			require_once 'connection.php';
			if(isset($_REQUEST['matricula']) and isset($_REQUEST['marca']) and isset($_REQUEST['modelo']) and isset($_REQUEST['lugares'])){
				$connection = pg_connect("host=$host port=$port user=$user password=$password dbname=$dbname") or die(pg_last_error());
				pg_query("start transaction");
				$sql = "insert into Viatura values('{$_REQUEST['matricula']}', '{$_REQUEST['marca']}', '{$_REQUEST['modelo']}', '{$_REQUEST['lugares']}', '{$_REQUEST['nick']}');";
				$result = pg_query($sql) or die('<p class=erro>ERRO: Informa&ccedil;&atilde;o inv&aacute;lida</p>');
				
				$sql = "insert into Condutor select '{$_REQUEST['nick']}' where not exists (select nick from Condutor where nick='{$_REQUEST['nick']}');";
				$result = pg_query($sql) or die(pg_last_error());
				pg_query("commit");
				pg_close($connection);
				
				echo("<p> Viatura registada com sucesso! </p>");
				
				echo("<a href = 'registarviatura.php?nick={$_REQUEST['nick']}'>Adicione outra viatura &agrave; sua conta</a>");
			}
			else{
				echo('<p> Dados da sua viatura </p>
					<form action = "registarviatura.php" method = "post">
					<input type = "hidden" name="nick" value="'.$_REQUEST['nick'].'">
					<p> <label>Matricula:</label> <input type="text" name="matricula"/> </p>
					<p> <label>Marca:</label> <input type="text" name="marca"/> </p>
					<p> <label>Modelo:</label> <input type="text" name="modelo"/> </p>
					<p> <label>Lugares:</label> <input type="text" name="lugares"/> </p>
					<p> <input type="submit" value="Registar"/> </p>
					</form>');
			}
		?>

	</body>
</html>
