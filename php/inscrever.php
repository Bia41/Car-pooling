<!DOCTYPE html>
<html>
	<head>
		<title> Ride - Inscri&ccedil;&atilde;o </title>
		<link rel="stylesheet" type="text/css" href="layout.css" />
	</head>
	<body>
		<h1> <a href = 'index.php' class=title>RIDE</a> </h1>

		<?php
			require_once 'connection.php';
			$connection = pg_connect("host=$host port=$port user=$user password=$password dbname=$dbname") or die(pg_last_error());
			if(isset($_REQUEST['nick']) and isset($_REQUEST['tipo']) and isset($_REQUEST['criador']) and isset($_REQUEST['data'])){
				$data = date('c',strtotime($_REQUEST['data']));
				pg_query("start transaction");
				$sql = "select * from Inscricaop where nick_organizador='{$_REQUEST['criador']}' and data_hora='$data' and nick_passageiro='{$_REQUEST['nick']}'";
				$result = pg_query($sql) or die('<p class=erro> ERRO: Imposs&iacute;vel inscrever </p>');
				if(pg_num_rows($result) != 0){
					die('<p class=erro>ERRO: J&aacute; est&aacute; inscrito! </p>');
				}
				
				$sql = "select * from Boleia where nick='{$_REQUEST['criador']}' and data_hora='$data' and nick_condutor='{$_REQUEST['nick']}'";
				$result = pg_query($sql) or die('<p class=erro> ERRO: Imposs&iacute;vel inscrever </p>');
				if(pg_num_rows($result) != 0){
					die('<p class=erro>ERRO: J&aacute; est&aacute; inscrito!</p>');
				}
				
				$tipo = $_REQUEST['tipo'];
				if($tipo == 'passageiro'){ 
					$sql = "insert into Inscricaop values('{$_REQUEST['nick']}', '{$_REQUEST['criador']}', '$data');";
					$result = pg_query($sql) or die('<p class=erro>ERRO: Imposs&iacute;vel inscrever</p>');
					
					echo("<p> Inscri&ccedil;&atilde;o bem sucedida! </p>");
					
					$sql = "select saldo from Utente where nick='{$_REQUEST['nick']}'";
					$result = pg_query($sql) or die(pg_last_error());
					$saldo = pg_fetch_assoc($result);
					echo("Saldo corrente: {$saldo['saldo']} &#8364");
				}
				
				if($tipo == 'condutor'){
					if(isset($_REQUEST['viatura'])){
						$sql = "update Boleia set nick_condutor='{$_REQUEST['nick']}', matricula='{$_REQUEST['viatura']}' where nick='{$_REQUEST['criador']}' and data_hora='$data';";
						$result = pg_query($sql) or die(pg_last_error());
						echo("<p> Inscri&ccedil;&atilde;o bem sucedida </p>");
					}
					
					else{
						$sql = "select count (*) as num_passageiros from Inscricaop where nick_organizador='{$_REQUEST['criador']}' and data_hora='$data'";
						$res = pg_query($sql) or die(pg_last_error());
						$count = pg_fetch_assoc($res);

						$sql = "select * from Viatura where nick='{$_REQUEST['nick']}' and maxocupantes > {$count['num_passageiros']};";
						$result = pg_query($sql) or die(pg_last_error());
						
						if(pg_num_rows($result) == 0){
							echo('<p class=erro>N&atilde;o tem nenhuma viatura com as caracter&iacute;sticas necess&aacute;rias!</p>');
						}
						else{
							echo('<p> Escolha a sua viatura </p>
							<form action = "inscrever.php" method = "get">
							<input type = "hidden" name="criador" value="'.$_REQUEST['criador'].'">
							<input type = "hidden" name="data" value='.$data.'>
							<input type = "hidden" name="nick" value="'.$_REQUEST['nick'].'">
							<input type = "hidden" name="tipo" value="'.$_REQUEST['tipo'].'">
							<select name="viatura">');
							while($row = pg_fetch_assoc($result)) {
								echo('<option value="'.$row['matricula'].'">'.$row['matricula'].'</option>');
							}
							echo('<p> <input type="submit" value="Escolher"/> </p>
								</form>');
						}
					}
				}
				pg_query("commit");
			}
			else{
				$data = date('c',strtotime($_REQUEST['data']));
				pg_query("start transaction");
				$sql = "select * from Boleia where nick='{$_REQUEST['criador']}' and data_hora='$data'";
				$result = pg_query($sql) or die(pg_last_error());
				if(pg_num_rows($result) == 0){
					die('Boleia inv&aacute;lida');
				}
				$boleia = pg_fetch_assoc($result);
				
				$sql = "select * from BoleiaFrequente where nick='{$_REQUEST['criador']}' and data_hora='$data';";
				$res = pg_query($sql) or die(pg_last_error());
				if(pg_num_rows($res) != 0) {
					$freq = pg_fetch_assoc($res);
					echo('<p><span>Aten&ccedil;&atilde;o, esta boleia &eacute; peri&oacute;dica.</span></p>
						<p><span> Frequ&ecirc;ncia:</span> '.$freq['tipo'].'</p>
						<p><span> T&eacute;rmino:</span> '.$freq['data_termino'].'</p><br>');
				}
				
				$sql = "select * from Inscricaop where nick_organizador='{$_REQUEST['criador']}' and data_hora='$data';";
				$res = pg_query($sql) or die(pg_last_error());
				if(pg_num_rows($res) != 0) {
					echo('<p><span>Passageiros:</span> ');
					while($row = pg_fetch_assoc($res)) {
						echo($row['nick_passageiro'].' ');
					}
					echo('</p><br>');
				}
				echo('<form action = "inscrever.php" method = "get">
					<input type = "hidden" name="criador" value="'.$_REQUEST['criador'].'">
					<input type = "hidden" name="data" value='.$data.'>
					<p> Nick: <input type="text" name="nick"/> </p>
					<p><input type="radio" name="tipo" value="passageiro">Passageiro');
					
				if($boleia['nick_condutor'] == NULL)
					echo('<input type="radio" name="tipo" value="condutor">Condutor</p>');
					
				echo('<p> <input type="submit" value="Inscrever"/> </p>
				</form>');
				pg_query("commit");
			}
			pg_close($connection);
		?>
	</body>
</html>
