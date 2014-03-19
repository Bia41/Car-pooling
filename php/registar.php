<!DOCTYPE html>
<html>
	<head>
		<title> Ride - Registo </title>
		<link rel="stylesheet" type="text/css" href="layout.css" />
	</head>
	<body>
		<h1> <a href = 'index.php' class=title>RIDE</a> </h1>
		<?php
			require_once 'connection.php';
			if(isset($_REQUEST['nome']) and isset($_REQUEST['nick']) and isset($_REQUEST['numero']) and isset($_REQUEST['saldo'])){

				$connection = pg_connect("host=$host port=$port user=$user password=$password dbname=$dbname") or die(pg_last_error());
				
				if(!(isset($_REQUEST['funcionario']) or isset($_REQUEST['docente']) or isset($_REQUEST['aluno']))) {die('<p class=erro>ERRO: Informa&ccedil;&atilde;o inv&aacute;lida</p>');}
				
				if(isset($_REQUEST['aluno']) and (!isset($_REQUEST['curso']) or $_REQUEST['curso'] == '')) {die('<p class=erro>ERRO: Falta preencher o campo Curso</p>');}
				
				pg_query("start transaction");
				$sql = "insert into Utente values('{$_REQUEST['nick']}', '{$_REQUEST['nome']}', '{$_REQUEST['numero']}', '{$_REQUEST['saldo']}');";
				$result = pg_query($sql) or die('<p class=erro>ERRO: Informa&ccedil;&atilde;o inv&aacute;lida</p>');
				$sql = "insert into Passageiro values('{$_REQUEST['nick']}');";
				$result = pg_query($sql) or die(pg_last_error());
								
				if(isset($_REQUEST['aluno'])){
					$sql = "insert into Aluno values('{$_REQUEST['curso']}', '{$_REQUEST['nick']}');";
					$result = pg_query($sql) or die(pg_last_error());
				}
				
				if(isset($_REQUEST['funcionario'])){
					$sql = "insert into Funcionario values('{$_REQUEST['nick']}');";
					$result = pg_query($sql) or die(pg_last_error());
				}
				
				if(isset($_REQUEST['docente'])){
					$sql = "insert into Docente values('{$_REQUEST['nick']}');";
					$result = pg_query($sql) or die(pg_last_error());
				}
				pg_query("commit");
				pg_close($connection);
				

				echo("<p> Conta criada com sucesso! </p>");
				
				echo("<a href = 'registarviatura.php?nick={$_REQUEST['nick']}'>Adicione uma viatura &agrave; sua conta</a>");
			}
			else{
				echo('<form action = "registar.php" method = "get">
					<p> <label>Nome:</label> <input type="text" name="nome"/> </p>
					<p> <label>Nick:</label> <input type="text" name="nick"/> </p>
					<p> <label>N&uacute;mero:</label> <input type="text" name="numero"/> </p>
					<p> <label>Saldo:</label> <input type="text" name="saldo"/> </p>
					<p> <label>Aluno</label><input type="checkbox" name="aluno"> Curso: <input type="text" name="curso"/></p> 
					<p> <label>Funcion&aacute;rio</label><input type="checkbox" name="funcionario"></p> 
					<p> <label>Docente</label><input type="checkbox" name="docente"></p> 
					<p> <input type="submit" value="Registar"/> </p>
					</form>');
			}
		?>

	</body>
</html>
