<!DOCTYPE html>
<html>
	<head>
		<title> Ride </title>
		<link rel="stylesheet" type="text/css" href="layout.css" />
	</head>
	<body>
		<h1> <a href = 'index.php' class=title>RIDE</a> </h1>
		<h3> <a href = "criar.php">Crie j&aacute;</a> a sua boleia ou procure uma abaixo </h3>
		<form action = "index.php" method = "get">
			<p> <label>Origem:</label> <input type="text" name="origem"/> </p>
			<p> <label>Destino:</label> <input type="text" name="destino"/> </p>
			<p> <input type="submit" value="Procurar Boleia"/> </p>
		</form>
		
		<?php
			require_once 'connection.php';
			if(isset($_REQUEST['origem']) and isset($_REQUEST['destino'])){

				$connection = pg_connect("host=$host port=$port user=$user password=$password dbname=$dbname") or die (pg_last_error());
				$sql = "select * from boleia";
				$origem = $_REQUEST['origem'];
				$destino = $_REQUEST['destino'];
				if($origem !== '' or $destino !== '') $sql = $sql.' where ';
				if($origem !== '') $sql = $sql."nome_origem = '".$origem."'";
				if($origem !== '' and $destino !== '') $sql = $sql.' and ';
				if($destino !== '') $sql = $sql."nome_destino = '".$destino."'";
				$sql = $sql.';';
				$result = pg_query($sql) or die(pg_last_error());
				if(pg_num_rows($result) == 0) 
					echo("<p> N&atilde;o foram encontradas boleias </p>");
				else{
					echo("<table>"); 
					echo("<tr class=firstline> <td class=period> </td> <td> Origem </td> <td> Destino </td> <td> Data </td> <td> Pre&ccedil;o </td> <td> Condutor </td> <td> Passageiros </td> </tr>");
					while($row = pg_fetch_assoc($result)) {
						$sql = "select count (*) as num_passageiros from Inscricaop where nick_organizador='{$row['nick']}' and data_hora='{$row['data_hora']}'";
						$res = pg_query($sql) or die(pg_last_error());
						$count = pg_fetch_assoc($res);
						
						$full = false;
						if($row['matricula'] != NULL) {
							$sql = "select * from viatura where matricula='{$row['matricula']}';";
							$res = pg_query($sql) or die(pg_last_error());
							$viatura = pg_fetch_assoc($res);
							if($viatura['maxocupantes'] == ($count['num_passageiros'] + 1))
								$full = true;
						}
						
						$freq = false;
						$sql = "select * from BoleiaFrequente where nick='{$row['nick']}' and data_hora='{$row['data_hora']}';";
						$res = pg_query($sql) or die(pg_last_error());
						if(pg_num_rows($res) != 0)
							$freq = true;
										
						echo("<tr>");
						if($freq)
							echo("<td class=period> P </td>");
						else echo("<td class=period> </td>");
						echo("<td>{$row['nome_origem']}</td>"); 
						echo("<td>{$row['nome_destino']}</td>"); 
						echo("<td>{$row['data_hora']}</td>"); 
						echo("<td>{$row['custo_passageiro']} &#8364</td>"); 
						echo("<td>{$row['nick_condutor']}</td>");
						echo("<td>{$count['num_passageiros']}</td>");
						if($full)
							echo("<td> Completa </td>");
						else
							echo("<td> <a href = 'inscrever.php?criador={$row['nick']}&data={$row['data_hora']}'>Inscrever</a> </td>");
						echo("</tr>");
					} 
					echo("</table>");
				}
				pg_close($connection);
			}
		?>
		<br>
		<p> Nunca utilizou o Ride? <a href = "registar.php">Registe-se agora.</a> </p>
	</body>
</html>