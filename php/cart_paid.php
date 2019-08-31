<?php
/*-----------------------
Autor: Armando Bolaños Dircio
Fecha: 28-08-2017
----------------------------*/
# conectare la base de datos
//$ConexionBD=@mysqli_connect('localhost', 'root', '', 'proyectomontecarlo');
include '../connections/conexion.php';
if(!$ConexionBD){
	die("imposible conectarse: ".mysqli_error($ConexionBD));
}
if (@mysqli_connect_errno()) {
	die("Connect failed: ".mysqli_connect_errno()." : ". mysqli_connect_error());
}
$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
if($action == 'ajax'){
	include '../php/pagination.php'; //incluir el archivo de paginación
	//las variables de paginación
	$page = (isset($_REQUEST['page']) && (isset($_REQUEST['idCliente'])) && !empty($_REQUEST['page']))?$_REQUEST['page']:1;
	$idCliente = $_REQUEST['idCliente'];

	$per_page = 10; //la cantidad de registros que desea mostrar
	$adjacents  = 4; //brecha entre páginas después de varios adyacentes
	$offset = ($page - 1) * $per_page;
	//Cuenta el número total de filas de la tabla*/
	$count_query   = mysqli_query($ConexionBD,"SELECT count(*) AS numrows FROM venta 
	WHERE idCliente='$idCliente' AND aux = '2' ");
	if ($row= mysqli_fetch_array($count_query)){$numrows = $row['numrows'];}
	$total_pages = ceil($numrows/$per_page);
	$reload = 'cliente_historial_pagados.php';
	//consulta principal para recuperar los datos

	//CAMBIAR DE PENDIENTE A PAGADO
	$cart_paid = "
		SELECT idVenta, idCliente, totalArticulos, totalVenta,
		fechaDeRegistro, aux
		FROM venta
		WHERE idCliente = '$idCliente' 
		
		AND aux = '2' ORDER BY fechaDeRegistro LIMIT $offset,$per_page
		";

	//$query = mysqli_query($ConexionBD,"SELECT * FROM countries  order by countryName LIMIT $offset,$per_page");
	$query   = mysqli_query($ConexionBD, $cart_paid);
	$i = 1;

	if ($numrows>0){
?>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Número de compra</th>
			<th>Clave de compra</th>
			<th>Articulos por Marca</th>
			<th>Total</th>
			<th>Fecha de compra</th>
			<th>Estado</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while($row = mysqli_fetch_array($query)){
		?>
		<tr>
			<td align="center"><?php echo $i; ?></td>
			<td>
				<a href="cliente_historial_pagados_2.php?idVenta=<?php echo $row['idVenta'];?>">
					<?php echo $row['idVenta'];?>
				</a>
			</td>
			<td align="center"><?php echo $row['totalArticulos'];?></td>
			<td><?php echo $row['totalVenta'];?></td>
			<td><?php echo $row['fechaDeRegistro'];?></td>
			<td>Pagado</td>
		</tr>
		<?php
			$i++;
		}
		?>
	</tbody>
</table>
<div class="table-pagination pull-right">
	<?php echo paginate($reload, $page, $total_pages, $adjacents);?>
</div>

<?php

	} else {
?>
<div class="alert alert-warning alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4>Aviso!!!</h4> No hay datos para mostrar
</div>
<?php
	}
}
?>
