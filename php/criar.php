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
			if(isset($_REQUEST['nick']) and isset($_REQUEST['trajeto']) and isset($_REQUEST['custo']) and isset($_REQUEST['data']) and isset($_REQUEST['tipo'])){
				$connection = pg_connect("host=$host port=$port user=$user password=$password dbname=$dbname") or die (pg_last_error());
				$nick = $_REQUEST['nick'];
				$data_hora = date('c',strtotime($_REQUEST['data']));
				$custo_passageiro = $_REQUEST['custo'];
				$tipo = $_REQUEST['tipo'];
				
				list($nome_origem, $nome_destino) = split('---', $_REQUEST['trajeto']);
				
				if($tipo == 'condutor'){
					echo("<script> location.href='escolherviatura.php?nick=$nick&origem=$nome_origem&destino=$nome_destino&custo=$custo_passageiro&data={$_REQUEST['data']}' </script>");
				}
				else{
					pg_query("start transaction");
					$sql="insert into boleia values('$nick', NULL, '$data_hora', $custo_passageiro, '$nome_origem','$nome_destino', NULL);";	
					$result = pg_query($sql) or die('<p class=erro>ERRO: Informa&ccedil;&atilde;o inv&aacute;lida</p>');
					
					$sql="insert into boleiaUnica values('$nick', '$data_hora');";
					$result = pg_query($sql) or die(pg_last_error());
					
					$sql = "insert into Inscricaop values('$nick', '$nick', '$data_hora');";
					$result = pg_query($sql) or die(pg_last_error());
					pg_query("commit");
					echo("<script> location.href='boleiacriada.php?criador=$nick&data={$_REQUEST['data']}' </script>");
				}
				pg_close($connection);
			}
		?>
		<form action = "criar.php" method = "get">
			<p> <label>Nick:</label> <input type="text" name="nick"/> <br/></p>
			
				<p><label>Trajeto:</label> <select name="trajeto">
					<option value='' disabled selected>Origem - Destino </option>
				<?php
				require_once 'connection.php';
				$connection = pg_connect("host=$host port=$port user=$user password=$password dbname=$dbname") or die (pg_last_error());
				pg_query("start transaction");
				$sql="select * from trajeto;";	
				$result = pg_query($sql) or die(pg_last_error());
				while($row = pg_fetch_assoc($result)) {
					echo('<option value="'.$row['nome_origem'].'---'.$row['nome_destino'].'">'.$row['nome_origem'].' - '.$row['nome_destino'].'</option>');
				}
				pg_query("commit");
				pg_close($connection);
				?>
			</select>
			</p>
			<p> <label>Pre&ccedil;o:</label> <input type="text" name="custo"/> </p>
			<p> <label>Data:</label> <input type="text" name="data"/> (MM-DD-AAAA HH:MM)</p>
			<p><input type="radio" name="tipo" value="passageiro">Passageiro
			<input type="radio" name="tipo" value="condutor">Condutor</p>
			<p> <input type="submit" value="Criar"/> </p>
		</form>
		<p> Nunca utilizou o Ride? <a href = "registar.php">Registe-se agora.</a> </p>
	</body>
</html>
