<!DOCTYPE html>
<html>
	<head>
		<title> Ride - Escolher Viatura </title>
		<link rel="stylesheet" type="text/css" href="layout.css" />
	</head>
	<body>
		<h1> <a href = 'index.php' class=title>RIDE</a> </h1>
		
		<?php
			require_once 'connection.php';
			$connection = pg_connect("host=$host port=$port user=$user password=$password dbname=$dbname") or die (pg_last_error());
			
			if(isset($_REQUEST['nick']) and isset($_REQUEST['origem']) and isset($_REQUEST['destino']) and isset($_REQUEST['custo']) and isset($_REQUEST['data']) and isset($_REQUEST['viatura'])){
				$data = date('c',strtotime($_REQUEST['data']));
				pg_query("start transaction");
				$sql="insert into boleia values('{$_REQUEST['nick']}', '{$_REQUEST['nick']}', '$data', {$_REQUEST['custo']}, '{$_REQUEST['origem']}','{$_REQUEST['destino']}', '{$_REQUEST['viatura']}');";
				$result = pg_query($sql) or die('<p class=erro>ERRO: Informa&ccedil;&atilde;o inv&aacute;lida</p>');
				
				$sql="insert into boleiaUnica values('{$_REQUEST['nick']}', '$data');";
				$result = pg_query($sql) or die(pg_last_error());
				pg_query("commit");
				echo("<script> location.href='boleiacriada.php?criador={$_REQUEST['nick']}&data={$_REQUEST['data']}' </script>");
			}
			else{
				$data = date('c', $_REQUEST['data']);
				pg_query("start transaction");
				$sql = "select * from Viatura where nick='{$_REQUEST['nick']}';";
				$result = pg_query($sql) or die(pg_last_error());
				
				if(pg_num_rows($result) == 0){
					echo('<p class=erro>N&atilde;o tem nenhuma viatura</p>');
				}
				else{
					echo('<p> Escolha a sua viatura </p>
						<form action = "escolherviatura.php" method = "get">
						<input type = "hidden" name="nick" value="'.$_REQUEST['nick'].'">
						<input type = "hidden" name="origem" value="'.$_REQUEST['origem'].'">
						<input type = "hidden" name="destino" value="'.$_REQUEST['destino'].'">
						<input type = "hidden" name="custo" value="'.$_REQUEST['custo'].'">
						<input type = "hidden" name="data" value='.$_REQUEST['data'].'>

						<select name="viatura">');
					while($row = pg_fetch_assoc($result)) {
						echo('<option value="'.$row['matricula'].'">'.$row['matricula'].'</option>');
					}
					echo('<p> <input type="submit" value="Escolher"/> </p>
						</form>');
				}
			}
			pg_query("commit");
			pg_close($connection);	
		?>
	</body>
</html>
