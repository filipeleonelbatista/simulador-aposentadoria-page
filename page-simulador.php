<?php

/*

Template Name: Simulador

*/

?>


<?php

ob_start();


//Prevenir erro de array
$grafRegra1 = array('ano' => 1);
$grafRegra2 = array('ano' => 1);
$grafRegra3 = array('ano' => 1);

$erro = '';

if (isset($_POST) && !empty($_POST)) {

	//var_dump($_POST);
	//die();

	//Recuperando campos do POST
	if (!empty($_POST['genero'])) {
		$genero = $_POST['genero'];
	} else {
		$genero = 'homem';
	}
	if (!empty($_POST['idade'])) {
		$idade = $_POST['idade'];
	} else {
		$idade = '';
	}
	if (!empty($_POST['contrib'])) {
		$contrib = $_POST['contrib'];
	} else {
		$contrib = '';
	}
	if (!empty($_POST['email'])) {
		$email = $_POST['email'];
	} else {
		$email = '';
	}
	if (!empty($_POST['profissao'])) {
		$profissao = $_POST['profissao'];
	} else {
		$profissao = '';
	}
	if (!empty($_POST['telefone'])) {
		$telefone = $_POST['telefone'];
	} else {
		$telefone = '';
	}
	if (!empty($_POST['nome'])) {
		$nome = $_POST['nome'];
	} else {
		$nome = '';
	}


	//Valida Email informado

	if (!validaEmail($email)) {
		$erro = true;
		$msgerro = 'Email inválido. Por favor, tente novamente.';
		goto saida;
	} else {
		$erro = false;
		$msgerro = '';
	}

	$html = '<!doctype html>';
	$html .= '<html lang="pt-br">';
	$html .= '	<head>';
	$html .= '		<meta charset="utf-8">';
	$html .= '	</head>	';
	$html .= '<body>';
	$html .= '<p>Nome : ' . $nome . '</p>';
	$html .= '<p>Profissão : ' . $profissao . '</p>';
	$html .= '<p>Email : ' . $email . '</p>';
	$html .= '<p>Gênero : ' . $genero . '</p>';
	$html .= '<p>Idade : ' . $idade . '</p>';
	$html .= '<p>Telefone : ' . $telefone . '</p>';
	$html .= '<p>Contribuição : ' . $contrib . '</p>';
	$html .= '</body>';
	$html .= '</html>';

	$message = $html;
	
	 $to = get_theme_mod("email_simulador");
	 $subject = "Simulador Aposentadoria - Uso no site ";
	 $headers = array('Content-Type: text/html; charset=UTF-8','From: Formulário do simulador do escritorio <'. get_theme_mod('email_simulador_host') .'>');

	 $sent = wp_mail($to, $subject, $message, $headers);

	/*********************    Calculo da Aposentadoria - Regra 1 - Regra dos Pontos      *******************/

	$AnoReferencia = 2019;
	$AnoAtual = date('Y');
	$AnoDif = $AnoAtual - $AnoReferencia;
	$PontosIniHomem = 96;
	$PontosIniMulher = 86;
	$PontosIniVoce = $idade + $contrib;

	$TotPontos = $genero == 'homem' ? 105 : 100;

	$TotAnos = round(($TotPontos - ($idade + $contrib - (2 * $AnoDif))) / 2, 0);

	$AnoApoRegra1 = $AnoReferencia + $TotAnos;

	$IdadeApoRegra1 = $idade - $AnoDif + $TotAnos;

	$ContribApoRegra1 = $contrib - $AnoDif + $TotAnos;

	//Gerar Array para o Gráfico
	$grafRegra1 = array();

	for ($x = 0; $x <= $TotAnos + 2; $x++) {
		$pontosH = $PontosIniHomem + $x >= 105 ? 105 : $PontosIniHomem + $x;
		$pontosM = $PontosIniMulher + $x >= 100 ? 100 : $PontosIniMulher + $x;
		$pontosV = $PontosIniVoce + ($x * 2) >= $TotPontos ? $TotPontos : $PontosIniVoce + ($x * 2);
		array_push($grafRegra1, ["ano" => $AnoReferencia + $x,  "homem" => $pontosH, "mulher" => $pontosM, "voce" => $pontosV]);
	}


	$Regra1Mensagem = 'Você vai alcançar os pontos necessários em ' . $AnoApoRegra1 . ' com ' . $IdadeApoRegra1 . ' ano(s) de idade e ' . $ContribApoRegra1 . ' ano(s) de contribuição.';

	/*
        echo '<pre>';
        var_dump($grafRegra1);
        echo '</pre>';
    	*/



	/*********************    Calculo da Aposentadoria - Regra 2 - Idade Progressiva      *******************/

	$AnosIniHomem = 61;
	$AnosIniMulher = 56;
	$AnosIniVoce = $idade - $AnoDif;

	$AnosIniContrib = $contrib - $AnoDif;


	if ($genero == 'homem') {
		if ($contrib >= 35) {
			$IdadeApoRegra2 = 65;
		} else {
			$IdadeApoRegra2 = 35 - $AnosIniContrib + $AnosIniVoce;
			$IdadeApoRegra2 = $IdadeApoRegra2 < 65 ? 65 : $IdadeApoRegra2;
		}
	} else {
		if ($contrib >= 30) {
			$IdadeApoRegra2 = 62;
		} else {
			$IdadeApoRegra2 = 30 - $AnosIniContrib + $AnosIniVoce;
			$IdadeApoRegra2 = $IdadeApoRegra2 < 62 ? 62 : $IdadeApoRegra2;
		}
	}


	$ContribApoRegra2 = $IdadeApoRegra2 - $AnosIniVoce + $AnosIniContrib;
	$AnoApoRegra2 = $AnoReferencia + $IdadeApoRegra2 - $AnosIniVoce;
	$DifAnoRegra2 = $IdadeApoRegra2 - $AnosIniVoce;

	//Gerar Array para o Gráfico
	$grafRegra2 = array();

	array_push($grafRegra2, ["ano" => $AnoReferencia,  "homem" => $AnosIniHomem, "mulher" => $AnosIniMulher, "voce" => $AnosIniVoce]);

	$Regra2 = 0.5; //por ano

	for ($x = 1; $x <= $DifAnoRegra2; $x++) {
		$AnosH = $AnosIniHomem + $Regra2 >= 65 ? 65 : $AnosIniHomem + $Regra2;
		$AnosM = $AnosIniMulher + $Regra2 >= 62 ? 62 : $AnosIniMulher + $Regra2;
		$AnosV = $AnosIniVoce + $x >= $IdadeApoRegra2 ? $IdadeApoRegra2 : $AnosIniVoce + $x;
		array_push($grafRegra2, ["ano" => $AnoReferencia + $x,  "homem" => $AnosH, "mulher" => $AnosM, "voce" => $AnosV]);

		$Regra2 = $Regra2 + 0.5;
	}

	$Regra2Mensagem = 'Você vai alcançar a idade necessária em ' . $AnoApoRegra2 . ' com ' . $IdadeApoRegra2 . ' ano(s) de idade e ' . $ContribApoRegra2 . ' ano(s) de contribuição.';


	/*
        echo '<pre>';
        var_dump($grafRegra2);
        echo '</pre>';
        */


	/*********************    Calculo da Aposentadoria - Regra 3 - Proporcional    *******************/

	$AnosIniVoce = $idade - $AnoDif;
	$ContribIniVoce = $contrib - $AnoDif;

	$TempoFaltaContrib = ($genero == 'homem' ? 30 : 25) - $AnosIniContrib;

	$TempoAdicionalContrib = $TempoFaltaContrib * 0.4;

	$ContribParaCalculo = $AnosIniContrib - $TempoAdicionalContrib;

	$ContribApoRegra3 = round($TempoFaltaContrib + $TempoAdicionalContrib + $ContribParaCalculo);

	$IdadeApoRegra3 = round($AnosIniVoce + $TempoFaltaContrib + $TempoAdicionalContrib);

	$AnoApoRegra3 =  round($AnoReferencia + $TempoFaltaContrib + $TempoAdicionalContrib, 0);

	$EntraRegra3 = false;

	if ($genero == 'homem') {
		if ($AnosIniVoce >= 53) {
			$EntraRegra3 = true;
		} else {
			$EntraRegra3 = false;
			goto naoaplica;
		}
	} else {
		if ($AnosIniVoce >= 48) {
			$EntraRegra3 = true;
		} else {
			$EntraRegra3 = false;
			goto naoaplica;
		}
	}

	//Gerar Array para o Gráfico
	$grafRegra3 = array();

	array_push($grafRegra3, ["ano" => $AnoReferencia,  "voce" => $AnosIniVoce]);

	for ($x = 1; $x <= round($TempoFaltaContrib + $TempoAdicionalContrib, 0); $x++) {
		$AnosV = $AnosIniVoce + $x >= $IdadeApoRegra3 ? $IdadeApoRegra3 : $AnosIniVoce + $x;
		array_push($grafRegra3, ["ano" => $AnoReferencia + $x,  "voce" => $AnosV]);
	}


	//var_dump(get_defined_vars());
	//die();


	naoaplica:

	if ($EntraRegra3) {
		$Regra3Mensagem = 'Você vai alcançar a idade necessária em ' . $AnoApoRegra3 . ' com ' . $IdadeApoRegra3 . ' ano(s) de idade e ' . $ContribApoRegra3 . ' ano(s) de contribuição.';
	} else {
		$Regra3Mensagem = 'Você não se enquadra nessa regra';
	}



	$arrayVantagem = array();
	array_push($arrayVantagem, ["ano" => $AnoApoRegra1, "regra" => 1]);
	array_push($arrayVantagem, ["ano" => $AnoApoRegra2, "regra" => 2]);
	if ($EntraRegra3) {
		array_push($arrayVantagem, ["ano" => $AnoApoRegra3, "regra" => 3]);
	}


	$Vantagem = min($arrayVantagem);

	saida:
}


function validaEmail($email)
{
	// Check the formatting is correct
	if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		return FALSE;
	}
	// Next check the domain is real.
	$domain = explode("@", $email, 2);
	return checkdnsrr($domain[1]); // returns TRUE/FALSE;
}


?>

<?php get_header(); ?>

<head>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">

	<?php
	echo get_theme_mod('is_bootstrap')
		? ''
		: '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">'
	?>

	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>



	<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/sa_assets/css/hover.css">
	<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/sa_assets/css/custom.css">
	<script src="<?php bloginfo('template_url'); ?>/sa_assets/js/custom.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/sa_assets/js/mascara.js"></script>



	<style type="text/css">
		.btn-custom {
			font-weight: bold;
			font-size: 150%;
			background-color: <?php echo get_theme_mod('primary_color'); ?>;
			color: #fff !important;
			width: 90%;
			margin-top: 10px;
			min-width: auto !important;
		}

		.botao-zap {
			padding: 0 0 2rem;
		}

		.especi {
			background: #25d366;
			padding: 26px 38px;
			color: #fff;
			font-size: 22px;
			text-align: center;
			margin: 0 auto;
			border-radius: 2em;
			border: none;
			font-weight: bold;
		}

		.especi:hover {
			background: #25d366 !important;
		}

		.btn-custom2:after {
			font-family: "Font Awesome 5 Free";
			content: "\f104";
			float: right;
			color: #fff;
			transform: rotate(180deg);
		}

		.headsimulador {
			background-color: <?php echo get_theme_mod('primary_color'); ?> !important;
		}

		.bodysimulador {
			border: 1px solid <?php echo get_theme_mod('primary_color'); ?> !important;
		}

		.form-genero .form-genero-homem,
		.form-genero .form-genero-mulher {
			border: 1px solid <?php echo get_theme_mod('primary_color'); ?> !important;
		}

		.form-genero .ativo {
			background-color: <?php echo get_theme_mod('primary_color'); ?> !important;
		}

		.idade,
		.email {
			border: 1px solid <?php echo get_theme_mod('primary_color'); ?> !important;
		}

		h4 {
			color: <?php echo get_theme_mod('secondary_color'); ?> !important;
			font-size: 18px !important;
		}

		label {
			font-weight: 700 !important;
		}

		.btn-custom2 {
			background-color: <?php echo get_theme_mod('primary_color'); ?> !important;
		}

		.headgraf {
			display: flex;
		}

		.headgraf1 {
			background-color: <?php echo get_theme_mod('primary_color'); ?> !important;
		}

		.headgraf2 {
			border: 2px solid <?php echo get_theme_mod('primary_color'); ?> !important;
		}
	</style>

	<?php wp_head(); ?>


</head>

<div class="body-content" style="padding: 15px;">

	<!-- <div class="starter-template">
		<?php //the_content() ?>
	</div> -->

	<div class="starter-template">
		<h1 class="headsimulador" style="text-transform: uppercase;">Simule sua aposentadoria</h1>

		<form action="" method="post" name="formsimulador">

			<div class="bodysimulador">


				<div class="row">
					<div class="col-md-6">
						<input type="hidden" name="genero" class="genero">
						<div class="form-genero">
							<h4>Informe o sexo</h4>

							<div class="form-genero-mulher genero-click <?php echo $genero == 'mulher' ? 'ativo' : '' ?>" data-genero="mulher">
								<label>Feminino</label>
							</div>

							<div class="form-genero-homem genero-click <?php echo $genero == 'homem' ? 'ativo' : '' ?>" data-genero="homem">
								<label>Masculino</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-inputs">
							<h4>Informe a idade <i class="fa fa-question-circle" title="Use ex.: 30.5 para informar o mês" style="color: <?php echo get_theme_mod('primary_color'); ?>;"></i></h4>
							<input type="text" id="idade" name="idade" placeholder="Ex.: 30.5" class="form-control idade" value="<?php echo esc_attr(isset($_POST['idade']) ? $_POST['idade'] : ''); ?>" pattern="[0-9]{1,3}\.?([0-9])?" title="Informe a idade" required>
						</div>
					</div>

				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-inputs">
							<h4>Informe o tempo de contribuição <i class="fa fa-question-circle" title="Use ex.: 20.5 para informar o mês" style="color: <?php echo get_theme_mod('primary_color'); ?>;"></i></h4>
							<input type="text" id="contrib" name="contrib" placeholder="Ex.: 20.5" class="form-control idade" value="<?php echo esc_attr(isset($_POST['contrib']) ? $_POST['contrib'] : ''); ?>" pattern="[0-9]{1,3}\.?([0-9])?" title="Informe o tempo de contribuição" required>
						</div>
						<div class="form-inputs">
							<h4>Seu Nome <i class="fa fa-question-circle" title=" " style="color: <?php echo get_theme_mod('primary_color'); ?>;"></i></h4>
							<input type="text" id="nome" name="nome" placeholder="" class="form-control idade" value="<?php echo esc_attr(isset($_POST['nome']) ? $nome : ''); ?>" title="Seu Nome" required>
						</div>

						<div class="form-inputs" style="padding: 15px 0;">
							<input style="background-color: initial;
														cursor: default;
														appearance: auto;
														box-sizing: border-box;
														margin: 3px 3px 3px 4px;
														padding: initial;
														border: initial;
														height: 25px;
														width: 25px;
														" type="checkbox" class=" wow bounceInRight" id="form_Aceite" name="form_Aceite" required />
							<label for="form_Aceite" style="text-align: justify;">De acordo com as Leis 12.965/2014 e 13.709/2018, que regulam o uso da Internet e o tratamento de dados pessoais no Brasil, autorizo o escritório <?php echo get_theme_mod("escritorio_simulador"); ?> a enviar notificações por e-mail ou outros meios e concordo com sua Política de Privacidade.
							</label>
						</div>

					</div>
					<div class="col-md-6">
						<div class="form-inputs">
							<h4>Telefone <i class="fa fa-question-circle" title=" " style="color: <?php echo get_theme_mod('primary_color'); ?>;"></i></h4>
							<input type="text" id="telefone" name="telefone" placeholder="" onkeyup="mascara('(##) #####-####',this,event,true)" maxlength="15" class="form-control idade" value="<?php echo esc_attr(isset($_POST['telefone']) ? $_POST['telefone'] : ''); ?>" title="Telefone" required>
						</div>
						<div class="form-inputs">
							<h4>Profissão <i class="fa fa-question-circle" title=" " style="color: <?php echo get_theme_mod('primary_color'); ?>;"></i></h4>
							<input type="text" id="profissao" name="profissao" placeholder="" class="form-control idade" value="<?php echo esc_attr(isset($_POST['profissao']) ? $_POST['profissao'] : ''); ?>" title="Sua Profissão" required>
						</div>

						<div class="form-inputs emailbox align-left">
							<h4>Seu E-mail</h4>
							<input type="text" name="email" class="form-control email" required>
						</div>
					</div>

				</div>

				<div class="row">
					<br>
					<div class="col-md-12 btn-calc">
						<!--<button class="btn btn-custom hvr-bounce-to-right" onclick="calcular()">Simular</button>-->
						<?php echo $erro ? '<p style="font-weight : bold; font-size: 16px; color: red;">' . $msgerro . '</p>' : '';   ?>
						<button class="btn btn-custom2 hvr-grow" id="btsimula" type="submit">Simular</button>
					</div>
				</div>

			</div>

			<div id="enviar" style="display : none;">

				<!--div class="row caixa1">
						<div class="col-md-12 caixa2">
							
							<p class="caixatit1">Preencha abaixo para saber o resultado.</p>
							<p class="caixatit2">Não se preocupe, é apenas para nosso banco de dados.</p>
							
							<div class="form-inputs emailbox">
								<p>Digite seu E-mail</p>
								<input type="text" name="email" class="form-control email" required>
							</div>
							
							<div class="row">
								<div class="col-md-12 btn-calc">
									<button class="btn btn-custom2 hvr-grow" type="submit">Enviar</button>
								</div>
							</div>
							
						</div>
						
					</div-->
			</div>

		</form>

		<?php if (!empty($email) && !$erro) { ?>
			<p>&nbsp;</p>
			<div style="outline : 3px solid <?php echo $Vantagem['regra'] == 1 ? '#41c19d' :  get_theme_mod("primary_color"); ?> ;">
				<div class="headgraf">
					<div class="col-md-2 headgraf1">REGRA 1</div>
					<div class="col-md-<?php echo $Vantagem['regra'] == 1 ? '8' :  '10'; ?> headgraf2">REGRA DOS PONTOS</div>
					<?php echo $Vantagem['regra'] == 1 ? '<div class="col-md-2 headgraf3">+ VANTAJOSA PARA VOCÊ</div>' :  ''; ?>
				</div>
				<div class="panelgraf">
					<div class="bodygraf">
						<figure class="highcharts-figure">
							<div id="grafigoRegra1" style="width: 100%; height: 400px; margin: 0 auto; overflow: auto"></div>
						</figure>
						<script>
							Highcharts.chart('grafigoRegra1', {
								chart: {
									type: 'line',
								},
								title: {
									text: ''
								},
								subtitle: {
									text: ''
								},
								xAxis: {
									categories: [
										<?php
										foreach ($grafRegra1 as &$value) {
											echo $value['ano'] . ',';
										}
										?>
									]
								},
								yAxis: {
									title: {
										text: 'Pontos'
									}
								},
								plotOptions: {
									line: {
										dataLabels: {
											enabled: true
										},
										enableMouseTracking: false
									}
								},
								series: [{
									name: 'Homem',
									data: [
										<?php
										foreach ($grafRegra1 as &$value) {
											echo $value['homem'] . ',';
										}
										?>
									]
								}, {
									name: 'Mulher',
									data: [
										<?php
										foreach ($grafRegra1 as &$value) {
											echo $value['mulher'] . ',';
										}
										?>
									]
								}, {
									name: 'Você',
									data: [
										<?php
										foreach ($grafRegra1 as &$value) {
											echo $value['voce'] . ',';
										}
										?>
									]
								}]
							});
						</script>
					</div>
					<p class="footgraf"><?php echo $Regra1Mensagem; ?></p>

				</div>
			</div>

			<p>&nbsp;</p>

			<div style="outline : 3px solid <?php echo $Vantagem['regra'] == 2 ? '#41c19d' : get_theme_mod("primary_color"); ?> ;">
				<div class="headgraf">
					<div class="col-md-2 headgraf1">REGRA 2</div>
					<div class="col-md-<?php echo $Vantagem['regra'] == 2 ? '8' :  '10'; ?> headgraf2">IDADE PROGRESSIVA</div>
					<?php echo $Vantagem['regra'] == 2 ? '<div class="col-md-2 headgraf3">+ VANTAJOSA PARA VOCÊ</div>' :  ''; ?>
				</div>
				<div class="panelgraf">
					<div class="bodygraf">
						<figure class="highcharts-figure">
							<div id="grafigoRegra2" style="width: 100%; height: 400px; margin: 0 auto; overflow: auto"></div>
						</figure>
						<script>
							Highcharts.chart('grafigoRegra2', {
								chart: {
									type: 'line',
								},
								title: {
									text: ''
								},
								subtitle: {
									text: ''
								},
								xAxis: {
									categories: [
										<?php
										foreach ($grafRegra2 as &$value) {
											echo $value['ano'] . ',';
										}
										?>
									]
								},
								yAxis: {
									title: {
										text: 'Anos'
									}
								},
								plotOptions: {
									line: {
										dataLabels: {
											enabled: true
										},
										enableMouseTracking: false
									}
								},
								series: [{
									name: 'Homem',
									data: [
										<?php
										foreach ($grafRegra2 as &$value) {
											echo $value['homem'] . ',';
										}
										?>
									]
								}, {
									name: 'Mulher',
									data: [
										<?php
										foreach ($grafRegra2 as &$value) {
											echo $value['mulher'] . ',';
										}
										?>
									]
								}, {
									name: 'Você',
									data: [
										<?php
										foreach ($grafRegra2 as &$value) {
											echo $value['voce'] . ',';
										}
										?>
									]
								}]
							});
						</script>
					</div>
					<p class="footgraf"><?php echo $Regra2Mensagem; ?></p>

				</div>
			</div>

			<p>&nbsp;</p>

			<div style="outline : 3px solid <?php echo $Vantagem['regra'] == 3 ? '#41c19d' : get_theme_mod("primary_color"); ?> ;">
				<div class="headgraf">
					<div class="col-md-2 headgraf1">REGRA 3</div>
					<div class="col-md-<?php echo $Vantagem['regra'] == 3 ? '8' :  '10'; ?> headgraf2">PROPORCIONAL</div>
					<?php echo $Vantagem['regra'] == 3 ? '<div class="col-md-2 headgraf3">+ VANTAJOSA PARA VOCÊ</div>' :  ''; ?>
				</div>

				<div class="botao-zap">

					Falar com um Especialista

				</div>
				<div class="panelgraf">
					<div class="bodygraf">
						<?php if ($EntraRegra3) { ?>
							<figure class="highcharts-figure">
								<div id="grafigoRegra3" style="width: 100%; height: 400px; margin: 0 auto; overflow: auto"></div>
							</figure>
							<script>
								Highcharts.chart('grafigoRegra3', {
									chart: {
										type: 'line',
									},
									title: {
										text: ''
									},
									subtitle: {
										text: ''
									},
									xAxis: {
										categories: [
											<?php
											foreach ($grafRegra3 as &$value) {
												echo $value['ano'] . ',';
											}
											?>
										]
									},
									yAxis: {
										title: {
											text: 'Anos'
										}
									},
									plotOptions: {
										line: {
											dataLabels: {
												enabled: true
											},
											enableMouseTracking: false
										}
									},
									series: [{
										name: 'Você',
										data: [
											<?php
											foreach ($grafRegra3 as &$value) {
												echo $value['voce'] . ',';
											}
											?>
										]
									}]
								});
							</script>

						<?php } ?>
					</div>
					<p class="footgraf"><?php echo $Regra3Mensagem; ?></p>

				</div>
			</div>

<!-- 			<div class="col-md-12" style="text-align: center;">
				<a id="zap-float" target="_blank" class="especi btn btn-primary btn-style  hvr-grow" href="https://wa.me/<?php echo get_theme_mod("whatsapp_simulador") ?>?text=Ola%20gostaria%20de%20um%20atendimento.">
					<div class="botao-zap">
					Falar com um Especialista <i class="fa-brands fa-whatsapp"></i>	
					</div>
				</a>
			</div> -->
		<?php }  ?>



		<!--h3>Saiba mais sobre as regras:</h3>
			
			
			<button id="btn-regra1" class="accordion collapsed" data-toggle="collapse" data-target="#cont1" aria-expanded="false" aria-controls="cont1"><strong>Regra 1 - Regra dos Pontos</strong></button>
			<div class="conteudo collapse" id="cont1" >
				<p>
					<ul>
						<li>Não há idade mínima.</li>
						<li>Tempo mínimo de contribuição de 30 anos para as mulheres e 35 anos para os homens.</li>
						<li>Total resultante da soma da idade e do tempo de contribuição deve ser de 86 pontos para as mulheres e de 96 pontos para os homens.</li>
						<li>Carência de 180 contribuições mensais.</li>
						<li>A aplicação do fator previdenciário para o cálculo desse benefício é opcional.</li>
					</ul>
				</p>
			</div>
			
			<button id="btn-regra2" class="accordion collapsed" data-toggle="collapse" data-target="#cont2" aria-expanded="false" aria-controls="cont2"><strong>Regra 2 - Idade Progressiva</strong></button>
			<div class="conteudo collapse" id="cont2" >
				<p>
					<ul>
						<li>Não há idade mínima.</li>
						<li>Tempo mínimo de contribuição de 30 anos para as mulheres e 35 anos para os homens.</li>
						<li>Carência de 180 contribuições mensais.</li>
						<li>A aplicação do fator previdenciário para o cálculo desse benefício é obrigatória.</li>
					</ul>
					
				</p>
			</div>
			
			<button id="btn-regra3" class="accordion collapsed" data-toggle="collapse" data-target="#cont3" aria-expanded="false" aria-controls="cont3"><strong>Regra 3 - Proporcional</strong></button>
			<div class="conteudo collapse" id="cont3" >
				<p>
					<ul>
						<li>Segurado com idade mínima de 48 anos (mulher) e 53 anos (homem).</li>
						<li>
							Tempo total de contribuição
							<ul>
								<li>25 anos de contribuição + o tempo adicional (mulher)</li>
								<li>30 anos de contribuição + o tempo adicional (homem)</li>
							</ul>
						</li>
						<li>Carência de 180 contribuições mensais.</li>
						<li>Aplicação obrigatória do fator previdenciário.</li>
						<li>Atenção! A aposentadoria proporcional foi extinta pela Emenda Constitucional 20/98. Porém, tendo em vista as regras de transição estabelecidas pela EC 20, os segurados filiados ao RGPS até 16/12/98 (somente estes) ainda têm direito à aposentadoria com proventos proporcionais ao tempo de contribuição.</li>
						<li>Um período adicional de contribuição equivalente a 40% do tempo que, em 16 de dezembro de 1998, vigência da Emenda Constitucional nº 20, de 15 de dezembro de 1998, faltava para atingir o tempo 25 anos de contribuição, se mulher, e de 30 anos de contribuição, se homem. Exemplo: um homem que tinha 20 anos de contribuição nessa data, precisava de 10 para se aposentar pela proporcional. Logo, para se aposentar pela proporcional hoje, deverá comprovar 34 anos (30 anos + 40% de 10 anos).</li>
					</ul>
				</p>
			</div-->

	</div>

</div>



<?php get_footer(); ?>