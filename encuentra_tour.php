<?php
include ("admin/config.php");
include ("admin/functions.php");

$tip_tab="tour";

// $_SESSION['URL'] = "http://".$SERVER_NAME.$PHP_SELF."?".$QUERY_STRING; 
session_start();
if(isset($_SESSION['carro'])){$carro=$_SESSION['carro'];}else{ $carro=false; }

$id=0;


//Configuracion
$dato_config = "SELECT * FROM site WHERE id_site='1'";
$dato_config = mysql_db_query($dbname, $dato_config); 
if ($row = mysql_fetch_array($dato_config)){ 

	$url = $row["url_site"];
	$mail = $row["mail_site"];
	$auto = $row["auto_site"];
	$copy = $row["copy_site"];

}

$opcion=$_GET["opcion"];
$region=$_GET["region"];
$pais=$_GET["pais"];
$ciudad=$_GET["ciudad"];
$interior=$_GET["interior"];
$duracion=$_GET["duracion"];
$desde=$_GET["desde"];
$hasta=$_GET["hasta"];

//Opcion
if ($opcion!="Ninguna") {
	$dato_key_opc = "SELECT tit_pos_pagina FROM pagina WHERE id_pagina='$opcion'";
	$dato_key_opc = mysql_db_query($dbname, $dato_key_opc); 
	if ($row = mysql_fetch_array($dato_key_opc)){ $key_opc = $row["tit_pos_pagina"]; }
}

// Opcion IDs
if ($opcion =="Ninguna") {

	$dato_opcion = "SELECT id_opcion FROM opcion";
	$dato_opcion = mysql_db_query($dbname, $dato_opcion); 
	while ($row = mysql_fetch_array($dato_opcion)){ 
		$id_opcion = $row["id_opcion"];
		$ids_opc = $ids_opc."'".$id_opcion."',";
	}
	$ids_opc = substr($ids_opc, 0, -1);
	
} else {

	$dato_opcion = "SELECT id_opcion FROM opcion WHERE id_pagina='$opcion'";
	$dato_opcion = mysql_db_query($dbname, $dato_opcion); 
	if ($row = mysql_fetch_array($dato_opcion)){ 
		$id_opcion = $row["id_opcion"];
		$ids_opc = $id_opcion;
	}
}


// Destino
if (($region =="Ninguna") AND ($pais =="Ninguna") AND ($ciudad =="Ninguna") AND ($interior =="Ninguna")) { 

	$dato_destino = "SELECT id_destino FROM destino";
	$dato_destino = mysql_db_query($dbname, $dato_destino); 
	while ($row = mysql_fetch_array($dato_destino)){ 
		$id_destino = $row["id_destino"];
		$ids_dest = $ids_dest."'".$id_destino."',";
	}
	$ids_dest = substr($ids_dest, 0, -1);

} else {

	if ($region !="Ninguna") {
		$ids_dest = "";//Inicializa la cadena
		$destino=$region; 
	}
	if ($pais !="Ninguna") {
		$ids_dest = "";//Inicializa la cadena 
		$destino=$pais; 
	}
	if ($ciudad !="Ninguna") { 
		$ids_dest = "";//Inicializa la cadena
		$destino=$ciudad; 
	}
	if ($interior !="Ninguna") { 
		$ids_dest = "";//Inicializa la cadena
		$destino=$interior; 
	}


	$ids_pag_dest="'".$destino."',".pagInternas($dbname,$destino);
	$ids_pag_dest = substr($ids_pag_dest, 0, -1);

	$dato_destino = "SELECT id_destino FROM destino WHERE id_pagina IN ($ids_pag_dest)";
	$dato_destino = mysql_db_query($dbname, $dato_destino); 
	while ($row = mysql_fetch_array($dato_destino)){ 
		$id_destino = $row["id_destino"];
		$ids_dest = $ids_dest."'".$id_destino."',";
	}
	$ids_dest = substr($ids_dest, 0, -1);	
}


//Region
if ($region!="Ninguna") {
	$dato_key_reg = "SELECT tit_pos_pagina FROM pagina WHERE id_pagina='$region'";
	$dato_key_reg = mysql_db_query($dbname, $dato_key_reg); 
	if ($row = mysql_fetch_array($dato_key_reg)){ $key_reg = $row["tit_pos_pagina"]; }
}

//Pais
if ($pais!="Ninguna") {
	$dato_key_pais = "SELECT tit_pos_pagina FROM pagina WHERE id_pagina='$pais'";
	$dato_key_pais = mysql_db_query($dbname, $dato_key_pais); 
	if ($row = mysql_fetch_array($dato_key_pais)){ $key_pais = $row["tit_pos_pagina"]; }
}

//Ciudad
if ($ciudad!="Ninguna") {
	$dato_key_ciu = "SELECT tit_pos_pagina FROM pagina WHERE id_pagina='$ciudad'";
	$dato_key_ciu = mysql_db_query($dbname, $dato_key_ciu); 
	if ($row = mysql_fetch_array($dato_key_ciu)){ $key_ciu = $row["tit_pos_pagina"]; }
}

//Interior
if ($interior!="Ninguna") {
	$dato_key_int = "SELECT tit_pos_pagina FROM pagina WHERE id_pagina='$interior'";
	$dato_key_int = mysql_db_query($dbname, $dato_key_int); 
	if ($row = mysql_fetch_array($dato_key_int)){ $key_int = $row["tit_pos_pagina"]; }
}

//Duracion
$min_dia = "SELECT MIN(dur_dia_tour) FROM tour";
$min_dia = mysql_db_query($dbname, $min_dia);
$min_dia = mysql_result($min_dia, 0);

$max_dia = "SELECT MAx(dur_dia_tour) FROM tour";
$max_dia = mysql_db_query($dbname, $max_dia);
$max_dia = mysql_result($max_dia, 0);

switch ($duracion) {
	case ($duracion=="Ninguna"):
		$min_duracion=$min_dia;
		if (empty($min_duracion)) {
			$min_duracion=0;
		}
		$max_duracion=$max_dia;
		break;

	case ($duracion==1):
		$min_duracion=1;
		$max_duracion=1;
		break;

	case ($duracion==5):
		$min_duracion=2;
		$max_duracion=$duracion;
		break;
	
	default:
		$min_duracion=$duracion-4;
		$max_duracion=$duracion;

		$ult=$max_dia-$duracion;
		$mod_ult=$duracion%5;

		if (($ult<5) AND ($mod_ult!=0)) {
			$min_duracion=$max_duracion-($mod_ult-1);
		}
		break;
}

//Precio
$min_desde = "SELECT MIN(val_tprecio) FROM tprecio";
$min_desde = mysql_db_query($dbname, $min_desde);
$min_desde = mysql_result($min_desde, 0);

$max_hasta = "SELECT MAx(val_tprecio) FROM tprecio";
$max_hasta = mysql_db_query($dbname, $max_hasta);
$max_hasta = mysql_result($max_hasta, 0);

if ($desde=="Ninguna") { $desde=$min_desde;}
if ($hasta=="Ninguna") { $hasta=$max_hasta;}

//total de Tours
	$total_tour = "SELECT COUNT(DISTINCT tour.id_tour) FROM 
								(((tour LEFT JOIN tprecio ON tour.id_tour = tprecio.id_tour)
								LEFT JOIN tour_opcion ON tour.id_tour = tour_opcion.id_tour)
								LEFT JOIN tour_destino ON tour.id_tour = tour_destino.id_tour)
								WHERE 
								(tprecio.val_tprecio BETWEEN ".$desde." AND ".$hasta.")
								AND
								(tour.dur_dia_tour BETWEEN ".$min_duracion." AND ".$max_duracion.")
								AND
								(tour_opcion.id_opcion IN (".$ids_opc."))
								AND
                				(tour_destino.id_destino IN (".$ids_dest."))";

	$total_tour = mysql_db_query($dbname, $total_tour);
	$total_tour = mysql_result($total_tour, 0);

//Pagina
$dato_pagina = "SELECT * FROM pagina WHERE id_pagina='$id'";
$dato_pagina = mysql_db_query($dbname, $dato_pagina); 
if ($row = mysql_fetch_array($dato_pagina)){ 

	$id_idioma = $row["id_idioma"];
}


	$des = "Sitiotours.com Encuentra tus Tours ubicados en el Destino: ".$key_reg." - ".$key_pais." - ".$key_ciu." - ".$key_int.", que duran de ".$min_duracion." a ".$max_duracion." días, tienen un Precio desde ".$desde." USD a ".$hasta." USD; donde te podras divertir en la Opcion Turistica de ".$key_opc." según tu interes";
	$key = "tour,".$key_opc.",".$key_reg.",".$key_pais.",".$key_ciu.", ".$key_int;
	$tit = "Encuentra tu Tour";

	if ($total_tour=="0") {
		$tit_pos = "No se Ubico tu Tour";
		$tit_com = $total_tour." Tours de tu preferencia";
		$des_pag = "Has Otra Selección";
	} else{
		
		$tit_pos = "Encontramos tu Tour";
		$tit_com = $total_tour." Tours de tu preferencia";
		$des_pag = "En Sitiotours.com Encontramos ".$total_tour." de tus Tours ubicados con el Destino ".$key_reg." - ".$key_pais." - ".$key_ciu." - ".$key_int.", que tienen una duración de ".$min_duracion." a ".$max_duracion." días, con un Precio desde ".$desde." USD a ".$hasta." USD; donde te podras divertir en la Opcion Turistica de ".$key_opc." según tu interes";
	}


//idioma
$dato_pagina = "SELECT * FROM idioma WHERE id_idioma='$id_idioma'";
$dato_pagina = mysql_db_query($dbname, $dato_pagina); 
if ($row = mysql_fetch_array($dato_pagina)){ $abre = $row["abre_idioma"];}

?>
<!DOCTYPE html>
<html lang="<?=$abre?>">
	<head>
		<base href="" />
		<meta charset="utf-8">
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="author" content="<?=$auto?>" />
		<meta name="copyright" content="<?=$copy?>" />
		<!-- Descripción: entre 155 a 160 caracteres -->
		<meta name="description" content="<?=$des?>" />
		<meta name="distribution" content="Global" />
		<meta name="google-site-verification" content="" /> <!-- -------- -->
		<!-- Keywords: entre 300 caracteres -->
		<meta name="keywords" content="<?=$key?>,sitiotours" />
		<meta name="MSSmartTagsPreventParsing" content="TRUE" />
		<meta name="owner" content="<?=$auto?>" />
		<meta name="rating" content="General" />
		<meta name="reply-to" content="<?=$mail?>" />
		<meta name="revisit-After" content="1 days" />
		<meta name="robots" content="all" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="img/ico-sitiotours.png" rel="shortcut icon">
		
		<script src="js/script.js"></script>
		<script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>

		<!-- Titulo de la pagina: entre 60 a 70 caracteres -->		
		<title><?=$tit?> :: Sitiotours.com </title>
	</head>

	<body>
		<?php
			//General
			$dato_general = "SELECT * FROM general WHERE id_pagina='$id'";
			$dato_general = mysql_db_query($dbname, $dato_general); 
			if ($row = mysql_fetch_array($dato_general)){ 
				$id_general = $row["id_general"];
				$des_general = $row["des_general"];
			}
		?>

				<?php
					include ("header.php");
				?>

		<section class="container marketing top-margen">
			<div class="row">
				<?php
					include ("filtro.php");
				?>

				<article class="span8" itemscope itemtype="http://schema.org/ImageObject">
					<figure>
						<img src="image/<?=$url?>" alt="<?=$url?> - <?=$url?>" border="0" width="200" heigth="114" itemprop="contentURL">
					</figure>
					<meta itemprop="datePublished" content="2014-07-06">
					<h1 itemprop="name"><span class="icon-tour ico-car"></span> <?=$tit_pos?></h1>
					<h2><?=$tit_com?></h2>
					<p itemprop="description"><?=$des_pag?></p><br>
					<p class="text-center">
					</p>
				</article>

				<?php

					// maximo por pagina
					$limit = 10;

					// pagina pedida
					$pag = (int) $_GET["pag"];
					if ($pag < 1)
					{
					   $pag = 1;
					}
					$offset = ($pag-1) * $limit;

					$sql = "SELECT SQL_CALC_FOUND_ROWS 
											DISTINCT
											tour.id_tour,
											tour.id_pagina,
											tour.dur_dia_tour,
											tour.dur_noc_tour,
											tour.id_agencia


											FROM 
											(((tour LEFT JOIN tprecio ON tour.id_tour = tprecio.id_tour)
											LEFT JOIN tour_opcion ON tour.id_tour = tour_opcion.id_tour)
											LEFT JOIN tour_destino ON tour.id_tour = tour_destino.id_tour)

											WHERE 
											(tprecio.val_tprecio BETWEEN ".$desde." AND ".$hasta.")
											AND
											(tour.dur_dia_tour BETWEEN ".$min_duracion." AND ".$max_duracion.")
											AND
											(tour_opcion.id_opcion IN (".$ids_opc."))
											AND
			                				(tour_destino.id_destino IN (".$ids_dest."))

											LIMIT $offset, $limit";

					$sqlTotal = "SELECT FOUND_ROWS() as total";

					$rs = mysql_query($sql);
					$rsTotal = mysql_query($sqlTotal);

					$rowTotal = mysql_fetch_assoc($rsTotal);
					// Total de registros sin limit
					$total = $rowTotal["total"];

					while ($row = mysql_fetch_assoc($rs)){ 

						$id_tour = $row["id_tour"];
						$id_pag_tour = $row["id_pagina"];
						$dur_dia_tour = $row["dur_dia_tour"];
						$dur_noc_tour = $row["dur_noc_tour"];
						$id_agencia = $row["id_agencia"];

						$dato_pag_tour = "SELECT * FROM pagina WHERE id_pagina=$id_pag_tour";
						$dato_pag_tour = mysql_db_query($dbname, $dato_pag_tour); 
						if ($row = mysql_fetch_array($dato_pag_tour)){ 
							$tit_pos_pag_tour = $row["tit_pos_pagina"];
							$des_pag_tour = $row["des_pagina"];
							$url_pag_tour = $row["url_pagina"];
						}


						$dato_pag_tour = "SELECT 
											DISTINCT pagina.id_pagina, pagina.tit_pos_pagina, pagina.url_pagina
											FROM

											pagina INNER JOIN agencia ON pagina.id_pagina = agencia.id_pagina
											WHERE id_agencia=$id_agencia";

						$dato_pag_tour = mysql_db_query($dbname, $dato_pag_tour); 
						if ($row = mysql_fetch_array($dato_pag_tour)){ 
							$tit_pos_pag_agen = $row["tit_pos_pagina"];
							$url_pag_agen = $row["url_pagina"];
						}
						?>

						<article class="span4" itemscope itemtype="http://schema.org/ImageObject">
							<a href="/tours/<?=$url_pag_tour?>">
								<h3 itemprop="name"><?=$tit_pos_pag_tour?> (<?=$dur_dia_tour?> D / <?=$dur_noc_tour?> N)</h3>
								
								<figure>
									<img src="image/<?=$logo_int?>" alt="<?=$alt_logo_int?> - <?=$des_logo_int?>" border="0" width="111" heigth="111" class="img-circle pull-left" itemprop="contentURL">
								</figure>
								<meta itemprop="datePublished" content="2014-07-06">
							</a>
							<a href="/agencias-de-viaje/<?=$url_pag_agen?>"><h4><?=$tit_pos_pag_agen?></h4></a>
							
							<p itemprop="description"><?=$des_pag_tour?></p>
							<a  href="/tour/<?=$url_pag_tour?>" class="btn btn-sitio span3 btn-filtro">
								<h3><i class="icon-favorito pull-left"></i></h3> <strong>Agregalo a tus Favoritos</strong>
							</a>
							
						</article>
						<?php
					}
				?>

					<br>
					<br>
					<div class="pagination span12 text-center">
						<ul>
				<?php


					$totalPag = ceil($total/$limit);
					$links = array();

					if ($totalPag<=5){

						for( $i=1; $i<=$totalPag ; $i++)
						{
							if ($pag==$i) { $on="class='active'"; } else { $on=""; }
							$n=$i;

							$links[] = "<li ".$on."><a href=\"?opcion=$opcion&region=$region&pais=$pais&ciudad=$ciudad&interior=$interior&duracion=$duracion&desde=$desde&hasta=$hasta&pag=$i\">$n</a></li>"; 
						}

					}else{

						
						if ($pag < 5){ 

							$ini=1; 
							$fin=5;

						} else{ 

							$ini=$pag-2;
							$fin=$pag+2;
						}		

						if ($fin >= $totalPag) { $fin=$totalPag; } 

						for( $i=$ini; $i<=$fin ; $i++)
						{	
							$n=$i; 
							if ($pag==$i) { $on="class='active'"; } else { $on=""; }
							if ($ini==$i && $i!=1) { $n="«"; } 
							if ($fin==$i && $i!=$totalPag) { $n="»"; }

							$links[] = "<li ".$on."><a href=\"?opcion=$opcion&region=$region&pais=$pais&ciudad=$ciudad&interior=$interior&duracion=$duracion&desde=$desde&hasta=$hasta&pag=$i\">$n</a></li>"; 
						}

					}

					echo implode(" ", $links);
				?>
		  				</ul>
					</div>

				<br>
				


			</div>
		</section>		

				<?php
					include ("footer.php");
				?>


	</body>
</html>


