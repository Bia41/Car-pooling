<!DOCTYPE html>
<html>
	<head>
		<title> Ride - Cria&ccedil;&atilde;o de boleia </title>
		<link rel="stylesheet" type="text/css" href="layout.css" />
	</head>
	<body>
		<h1> <a href = 'index.php' class=title>RIDE</a> </h1>
		<?php
			require_once 'connection.php';
			if(isset($_REQUEST['criador']) and isset($_REQUEST['data']) and isset($_REQUEST['data_termino']) and isset($_REQUEST['frequencia'])){
				$data_termino = date('c',strtotime($_REQUEST['data_termino']));
				$data = date('c',strtotime($_REQUEST['data']));
				$connection = pg_connect("host=$host port=$port user=$user password=$password dbname=$dbname") or die(pg_last_error());
				pg_query("start transaction");
				$sql="delete from boleiaUnica where nick='{$_REQUEST['criador']}' and data_hora='$data';";
				$result = pg_query($sql) or die(pg_last_error());
				$sql = "insert into BoleiaFrequente values('{$_REQUEST['criador']}', '$data', '$data_termino', '{$_REQUEST['frequencia']}');";
				$result = pg_query($sql) or die('<p class=erro>ERRO: Informa&ccedil;&atilde;o inv&aacute;lida</p>');
				pg_query("commit");
				pg_close($connection);
			
				echo("<p> Frequ&ecirc;ncia da boleia definida com sucesso! </p>");
			}
			else{
				$data = date('c',strtotime($_REQUEST['data']));
				echo('<p> Boleia criada com sucesso!</p> <br/>
					<p><span> Deseja que a sua boleia seja peri&oacute;dica? </span></p>
					<form action = "boleiacriada.php" method = "post">
					<input type = "hidden" name="criador" value="'.$_REQUEST['criador'].'">
					<input type = "hidden" name="data" value='.$data.'>
					<p> Data T&eacute;rmino: <input type="text" name="data_termino"/> (MM/DD/AAAA HH:MM)</p>
					<p> Frequ&ecirc;ncia: <input type="text" name="frequencia"/> </p>
					<p> <input type="submit" value="OK"/> </p>
					</form>');
			}
		?>
	</body>
</html>
