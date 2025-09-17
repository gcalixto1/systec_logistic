<?php
require_once("includes/class.php");

$queryEmpresa = $conexion->query("SELECT * FROM configuracion WHERE id = 1");
$Empresa = $queryEmpresa->fetch_assoc();

define("MH_AMBIENTE", $Empresa['dato8']); // 00 = PRUEBAS, 01 = PRODUCCIÓN
define("MH_USER", str_replace('-', '', $Empresa['dni']));
define("MH_PWD", $Empresa['claveAPI']);
define("MH_GRANT_TYPE", "password");
define("TOKEN_CACHE_FILE", __DIR__ . '/../storage/token_cache.json');
define("TOKEN_CACHE_MINUTES", 50);
define("MH_PWD_DTE", $Empresa['clavePRIV']); // Puedes cambiarlo si es distinto para producción

if ($Empresa['dato8'] === '01') {
    // Ambiente de PRODUCCIÓN
    define("MH_API_URL", "https://api.dtes.mh.gob.sv/seguridad/auth");
    define("MH_ENVIO_DTE_URL", "https://api.dtes.mh.gob.sv/fesv/recepciondte");
    define("MH_ENVIO_DTE_URL_ANULACION", "https://api.dtes.mh.gob.sv/fesv/anulardte");
    define("MH_ENVIO_DTE_URL_CONTINGENCIA", "https://api.dtes.mh.gob.sv/fesv/contingencia");
} else {
    // Ambiente de PRUEBAS (por defecto si no es '01')
    define("MH_API_URL", "https://apitest.dtes.mh.gob.sv/seguridad/auth");
    define("MH_ENVIO_DTE_URL", "https://apitest.dtes.mh.gob.sv/fesv/recepciondte");
    define("MH_ENVIO_DTE_URL_ANULACION", "https://apitest.dtes.mh.gob.sv/fesv/anulardte");
    define("MH_ENVIO_DTE_URL_CONTINGENCIA", "https://apitest.dtes.mh.gob.sv/fesv/contingencia");
}

// El firmador local es el mismo para ambos ambientes
define("MH_API_FIRMADOR", "http://localhost:8113/firmardocumento/");