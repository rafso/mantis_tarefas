<?
//Caso a data nao seja passada atrav�s da URL ent�o ele pega a data atual do sistema
if (empty($_GET['data'])) {
   $dia = date("d");
   $mes = date("m");
   $ano = date("Y");
}else{
   $data = explode('/',$_GET['data']);
   $dia = $data[0];
   $mes = $data[1];
   $ano = $data[2];
}

//Caso o m�s seja janeiro (1) entao o m�s anterior ser� dezembro (12), al�m de fazer o decr�scimo de um ano
if($mes==1){
	$mes_ant = 12;
	$ano_ant = $ano - 1;
}else{
	$mes_ant = $mes - 1;
	$ano_ant = $ano;
}

//Caso o m�s seja dezembro (12) entao o pr�ximo ser� janeiro (1), al�m de fazer o acr�scimo de um ano
if($mes==12){
	$mes_prox = 1;
	$ano_prox = $ano + 1;
}else{
	$mes_prox = $mes+1;
	$ano_prox = $ano;
}  

//Dados da data atual
$hoje = date("d");
$mesAtual = date("m");
$anoAtual = date("Y");

//Faz um switch para mostrar o m�s em portugu�s!
switch($mes){
   case "01" : $mesext = "Janeiro";         break;
   case "02" : $mesext = "Fevereiro";       break;
   case "03" : $mesext = "Mar�o";           break;
   case "04" : $mesext = "Abril";           break;
   case "05" : $mesext = "Maio";            break;
   case "06" : $mesext = "Junho";           break;
   case "07" : $mesext = "Julho";           break;
   case "08" : $mesext = "Agosto";          break;
   case "09" : $mesext = "Setembro";        break;
   case "10" : $mesext = "Outubro";         break;
   case "11" : $mesext = "Novembro";        break;
   case "12" : $mesext = "Dezembro";        break;
}

//Primeiro dia do m�s, vari�vel usada para calcular o primeiro dia do m�s no formato semanal (domingo....)!
$primeiroDiaNum = mktime(0,0,0,$mes,1,$ano) ;

//Primeiro dia no formato semanal
$primeiroDiaLet = date('D', $primeiroDiaNum) ;

//Switch usado para calcular as colunas em branco antes do primeiro dia do m�s, 
//usado na montagem da tabela do calend�rio
switch($primeiroDiaLet){
   case "Sun": $blank = 0; break;
   case "Mon": $blank = 1; break;
   case "Tue": $blank = 2; break;
   case "Wed": $blank = 3; break;
   case "Thu": $blank = 4; break;
   case "Fri": $blank = 5; break;
   case "Sat": $blank = 6; break;
}

//C�lculo de quantos tidas o m�s possui
$diasDoMes = cal_days_in_month(0,$mes,$ano);?>

<!-- Montagem da tabela de calend�rio, adicionando o link para o m�s anterior e o link para o pr�cimo m�s
bem como escrevendo o m�s por extenso! -->
<table border="1" align="center" width="154" border="0" cellpadding="0" cellspacing="0">

<tr align='center'>
	<td width="22"><a href="?data=<?echo $dia."/".$mes_ant."/".$ano_ant?>" class='cal'><<</a></td>
	<td colspan=5><font size="1"><strong><?echo $mesext." / ".$ano; ?></strong></font></td>
	<td width="22"><a href="?data=<?echo $dia."/".$mes_prox."/".$ano_prox?>" class='cal'>>></a></td>
</tr>

<tr align='center' height="15">
	<td colspan=7></td>
</tr>

<tr align="center">
	<td width=22>D</td><td width=22>S</td><td width=22>T</td><td width=22>Q</td>
	<td width=22>Q</td><td width=22>S</td><td width=22>S</td>
</tr>

<tr align='center' height="5">
	<td colspan=7></td>
</tr>

<?
//Vari�vel usada para quebrar a tabela em semanas (7 dias)
$contDias = 1;

echo "<tr align='center'>";

//Caso blnak maior que 0 ent�o acrescenta uma coluna na tabela
if ($blank > 0){
	for ($x=0; $x < $blank; $x++){
    	echo "<td></td>";
    	$contDias++;
	}	
}

//Loop de todos os dias do m�s
for ($y=1; $y <= $diasDoMes; $y++){
	//If usado para real�ar o dia atual e tamb�m a data caso seja clicado em algum dia qualquer
	if($y == $hoje){
		echo "<td><strong><a href=?data=".$y."/".$mes."/".$ano." class='cal'>".$y."</a></strong></td>";
	}else{
		if($y == $dia){
			echo "<td bgcolor='#cccccc'><a href=?data=".$y."/".$mes."/".$ano." class='cal'>".$y."</a></td>";
		}else{
			echo "<td><a href=?data=".$y."/".$mes."/".$ano." class='cal'>".$y."</a></td>";
		}		
	}
	
	//Caso a vari�vel seja igual a 7 ent�o cria-se uma nova linha na tabela e o contador volta a 1
	$contDias++;
	if ($contDias > 7){
		echo "</tr><tr align='center' height=5><td colspan=7></td></tr><tr align='center'>";
		$contDias = 1;
	}
}

//Caso o m�s termine antes do sab�do completa a tabela com campos em branco
while ($contDias > 1 && $contDias <=7){
   echo "<td></td>";
   $contDias++;
}
echo "</tr>
<tr align='center' height=15><td colspan=7></td></tr>
<tr align='center'>
	<td colspan=7>Hoje � : ".$hoje."/".$mesAtual."/".$anoAtual."</td>
</tr>
</table>";
?>
