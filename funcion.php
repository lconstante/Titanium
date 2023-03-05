<?php
include_once("configuracion.php");
function cadenaAleatoria($longitud)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randstring = '';
	for ($i = 0; $i < $longitud; $i++) {
		$randstring = $characters[rand(0, strlen($characters))];
	}
	return $randstring;
}

function iniciarSesion()
{
	session_start();
	return session_id();
}
function cargarSesion()
{
	$id = $_COOKIE["PHPSESSID"];
	$sesion = array ([
		"name" => "$id",
		"cookie_lifetime" => "1800",
		"cookie_path" => "/",
		"cookie_domain" => miDominio,
		"cookie_secure" => "0",
		"cookie_httponly" => "1",
		"cache_limiter" => "nocache",
		"use_trans_sid" => "0",
		"use_cookies" => "1",
		"lazy_write" => "1",
		"use_strict_mode" => "1",
		]);

	session_start($sesion);

	if (isset($_REQUEST["id"]) && !isset($_SERVER["id"]))
	{
		$_SESSION["id"]=$_REQUEST["id"];
	}
	
	if (isset($_SESSION["id"])) 
	{
		return true;
	} else {
		return false;
	}
}
function secured_encrypt($data)
{
	$first_key = base64_decode(primeraClave);
	$second_key = base64_decode(segundaClave);

	$method = "aes-256-cbc";
	$iv_length = openssl_cipher_iv_length($method);
	$iv = openssl_random_pseudo_bytes($iv_length);

	$first_encrypted = openssl_encrypt($data, $method, $first_key, OPENSSL_RAW_DATA, $iv);
	$second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

	$output = base64_encode($iv . $second_encrypted . $first_encrypted);
	return $output;
}

function secured_decrypt($input)
{
	$first_key = base64_decode(primeraClave);
	$second_key = base64_decode(segundaClave);
	$mix = base64_decode($input);

	$method = "aes-256-cbc";
	$iv_length = openssl_cipher_iv_length($method);

	$iv = substr($mix, 0, $iv_length);
	$second_encrypted = substr($mix, $iv_length, 64);
	$first_encrypted = substr($mix, $iv_length + 64);

	$data = openssl_decrypt($first_encrypted, $method, $first_key, OPENSSL_RAW_DATA, $iv);
	$second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

	if (hash_equals($second_encrypted, $second_encrypted_new))
		return $data;

	return false;
}
function getDato($array, $key)
{
	foreach ($array as $index => $value) {
		$rows = explode("=", $value);
		if (strtolower($rows[0]) == strtolower($key)) {
			return $rows[1];
		}
	}
	return false;
}
function getBoolean($var)
{
	$type = get_debug_type($var);
	$bok = false;
	if ($type == "boolean" || $type == "bool") {
		$bok = true;
	} else {
		$bok = false;
	}
	return $bok;
}
function encriptar($texto)
{
	return base64_encode($texto);
}
function desencriptar($texto)
{
	return base64_decode($texto);
}
function getPaginacion($parametro)
{
	foreach ($parametro as $key => $value) {
		$$key = $value;
	}

	$presentar = "";
	$pi = $p_pagNum - 2;
	$pf = $p_pagNum + 2;
	if ($pi < 1) {
		$pf += $pi * -1 + 1;
		$pi = 1;
	}

	if ($pf > $totalPage) {
		$pi = $pi - ($pf - $totalPage);
		$pf = $totalPage;
		if ($pi < 1) {
			$pi = 1;
		}
	}

	$bf = $pf + tamanioBloque - 2;
	$bi = $pi - tamanioBloque;

	if ($bi < 1) {
		$bi = 1;
	}
	if ($bf > $totalPage) {
		$bf = $totalPage;
	}

	$pb = $p_pagNum - 1;
	$pa = $p_pagNum + 1;

	if ($pb < 1)
		$pb = 1;

	if ($pa > $totalPage)
		$pa = $totalPage;

	$accion = encriptar("pagina");

	$parametro = "p_pagNum=1&opcion=$opcion&clase=" . $clase_php;
	$parametro = secured_encrypt($parametro);
	$onclick = "";
	$disable = "disable-control";
	if ($p_pagNum > 1) {
		$onclick = "onclick=\"cargarObjeto('detalle','$accion','$parametro');\"";
		$disable = "";
	}
	$presentar .= "<a href='#' $onclick class='button $disable'><span><strong>PRIMERA</strong></span></a>";

	$parametro = "p_pagNum=$bi&opcion=$opcion&clase=" . $clase_php;
	$parametro = secured_encrypt($parametro);
	$onclick = "";
	$disable = "disable-control";
	if ($pi > 1 && $p_pagNum > (tamanioBloque / 2) && $totalPage > tamanioBloque) {
		$onclick = "onclick=\"cargarObjeto('detalle','$accion','$parametro');\"";
		$disable = "";
	}
	$presentar .= "<a href='#' $onclick class='button $disable'><span><strong><<</strong></span></a>";

	$parametro = "p_pagNum=$pb&opcion=$opcion&clase=" . $clase_php;
	$parametro = secured_encrypt($parametro);
	$onclick = "";
	$disable = "disable-control";
	if ($p_pagNum > 1) {
		$onclick = "onclick=\"cargarObjeto('detalle','$accion','$parametro');\"";
		$disable = "";
	}
	$presentar .= "<a href='#' $onclick class='button $disable'><span><strong><</strong></span></a>";

	for ($i = $pi; $i <= $pf && $i <= $totalPage; $i++) {
		$parametro = "p_pagNum=$i&opcion=$opcion&clase=" . $clase_php;
		$parametro = secured_encrypt($parametro);

		if ($i == $p_pagNum) {
			$presentar .= "<a href='#' onclick=\"cargarObjeto('detalle','$accion','$parametro');\" class='button activo'><span><strong>$i</strong></span></a>";
		} else {

			$presentar .= "<a href='#' onclick=\"cargarObjeto('detalle','$accion','$parametro');\" class='button'><span>$i</span></a>";
		}
	}

	$parametro = "p_pagNum=$pa&opcion=$opcion&clase=" . $clase_php;
	$parametro = secured_encrypt($parametro);

	$onclick = "";
	$disable = "disable-control";
	if ($p_pagNum < $totalPage) {
		$onclick = "onclick=\"cargarObjeto('detalle','$accion','$parametro');\"";
		$disable = "";
	}

	$presentar .= "<a href='#' $onclick class='button $disable'><span><strong>></strong></span></a>";

	$parametro = "p_pagNum=$bf&opcion=$opcion&clase=" . $clase_php;
	$parametro = secured_encrypt($parametro);

	$onclick = "";
	$disable = "disable-control";
	if ($pf < $totalPage && $p_pagNum < ($totalPage - (tamanioBloque / 2)) && $totalPage > tamanioBloque) {
		$onclick = "onclick=\"cargarObjeto('detalle','$accion','$parametro');\"";
		$disable = "";
	}

	$presentar .= "<a href='#' $onclick class='button $disable'><span><strong>>></strong></span></a>";

	$parametro = "p_pagNum=$totalPage&opcion=$opcion&clase=" . $clase_php;
	$parametro = secured_encrypt($parametro);
	$onclick = "";
	$disable = "disable-control";
	if ($p_pagNum < $totalPage) {
		$onclick = "onclick=\"cargarObjeto('detalle','$accion','$parametro');\"";
		$disable = "";
	}
	$presentar .= "<a href='#' $onclick class='button $disable'><span><strong>ULTIMA</strong></span></a>";
	return $presentar;
}

function readCall($parametro, &$mensaje, $json_return = true)
{
	foreach ($parametro as $key => $value) {
		$$key = $value;
	}
	try {
		$db = new baseDato();
		$filas = $db->readCall($db_procedimiento, $parametro_sql, $mensaje, $json_return);
		unset($db);
		if ($mensaje != "[OK]") {
			$filas = array();
			$filas[0] = $mensaje;
		}
	} catch (Throwable $e) {
		$mensaje = "[ERROR] " . $e->getMessage();
	}

	return $filas;
}
