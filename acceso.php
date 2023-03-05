<?php
class claseAcceso
{
	public function mostrar($sesionId)
	{
		$accion = encriptar("validar");
		$dato = encriptar(encriptar("tablero") . "&" . encriptar(urlAcceso) . "&" . encriptar(urlInicio) . "&" . encriptar(secured_encrypt($sesionId)));

		$presentar = <<<HEREDOCS
				<!DOCTYPE html>
					<html lang="es">
						<head>
							<meta charset="ISO-8859-1">
							<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
							<meta name="description" content="">
							<meta name="author" content="">
							<title>Inicio de Sesión</title>
							<link rel="stylesheet" type="text/css" href="css/style-login.css">
							<!--assets-->
							<link rel="stylesheet" href="css/fontawesome/css/all.min.css">
							<link rel="stylesheet" href="css/base.min.css">
							<link rel="stylesheet" href="css/custom.css">						
						</head>
						<body>
							<div class="contenedor">
								<section class="forma" >
									<label>Inicio de Sesión</label>
										<form id="forma-login" action="">
										<input type="hidden" name="accion" value="$accion">
										<input type="hidden" name="parametro" value="$dato">
										<input type="text" name="usuario" id="usuario" placeholder="Usuario" autofocus>
										<input type="password" name="clave" id="clave" placeholder="Clave">
										<input type="button" id="acceso" value="Acceso">
									</form>
								</sectionv>
							</div>	
							<script src="js/funciones.js"></script>
							<script src="js/acceso.js"></script>					
						</body>
					</html>
				HEREDOCS;
		return $presentar;
	}
	public function validar()
	{
		try {
			$user = $_REQUEST["usuario"];
			$password = $_REQUEST["clave"];

			if (trim($user) == "" || trim($password) == "") {
				$presentar = "[ERROR] Los datos estan vacios.";
				return $presentar;
			}

			$user = desencriptar($user);
			$password = desencriptar($password);

			$parametro_sql = array();
			$parametro_sql["s_user"] = $user;
			$parametro_sql["s_password"] = $password;
			$parametro["db_procedimiento"] = "sp_getValidaLogin";
			$parametro["parametro_sql"] = $parametro_sql;
			$mensaje = "";

			$rows = readCall($parametro, $mensaje, false);

			if ($mensaje == "[OK]") {
				$presentar = $rows[0]["mensaje"];
				$_SESSION['usuario']=encriptar($user);
			} else {
				$presentar = "[ERROR] Al ejecutar validacion en la base de datos.";
				$presentar = $mensaje;
			}
		} catch (Throwable $e) {
			$presentar = "[ERROR] " . $e->getMessage();
		}

		return $presentar;
	}
}
