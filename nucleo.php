<?php
abstract class nucleo
{
    protected  $name="";
    private $frontEnd="";
    private $code="";
    private $rutaLogs="";
    
    private $user_agent="";
    private $navegador="";
    private $ip="";
    private $sistema="";
    private $dispositivo="";
    
    public function __construct()
    {
        $this->setFrontEnd("HTML");
        
        $this->rutaLogs =  __DIR__ . "logs";
        
        # Crear carpeta si no existe
        if (!file_exists($this->rutaLogs)) {
            mkdir($this->rutaLogs);
        }
        # Poner fecha y hora de M�xico, esto es por si el servidor tiene
        # otra zona horaria
        date_default_timezone_set("America/Guayaquil");
        
        # Configuramos el ini para que...
        # No muestre errores
        ini_set('display_errors', 0);
        # Los ponga en un archivo
        ini_set("log_errors", 1);
        # Y le indicamos en d�nde los va a poner, ser�a en algo como:
        # RUTA_LOGS/2019-02-07.log
        # As� cada d�a tenemos un archivo de log distinto
        ini_set("error_log", $this->rutaLogs . "/" . date("Y-m-d") . ".log");
        
        if ( isset( $_SERVER ) ) {
            $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            global $HTTP_SERVER_VARS;
            if ( isset( $HTTP_SERVER_VARS ) ) {
                $this->user_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
            } else {
                global $HTTP_USER_AGENT;
                $this->user_agent = $HTTP_USER_AGENT;
            }
        }
        
    }
    
    public function getFrontEnd()
    {
        $value=$this->frontEnd;
        return $value;
    }
    public function setFrontEnd($value)
    {
        $this->frontEnd=$value;
    }
    public function setName($name)
    {
        $this->name=$name;
    }
    public function getName()
    {
        return $this->name;
    }
    protected  function getRutaLogs()
    {
        return $this->rutaLogs;
    }
    
    public  function getIp()
    {
        
        if (isset($_SERVER["HTTP_CLIENT_IP"]))
        {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
        {
            return $_SERVER["HTTP_X_FORWARDED"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
        {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED"]))
        {
            return $_SERVER["HTTP_FORWARDED"];
        }
        else
        {
            return $_SERVER["REMOTE_ADDR"];
        }
        
    }
    
    public function getNavegador()
    {
		$browser_array = array(
            '/msie/i'       =>  'Internet Explorer',
            '/firefox/i'    =>  'Firefox',
            '/safari/i'     =>  'Safari',
            '/chrome/i'     =>  'Chrome',
            '/edge/i'       =>  'Edge',
            '/opera/i'      =>  'Opera',
            '/netscape/i'   =>  'Netscape',
            '/maxthon/i'    =>  'Maxthon',
            '/konqueror/i'  =>  'Konqueror',
            '/mobile/i'     =>  'Handheld Browser'
        );
        $browser = "Unknown Browser";
        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $this->user_agent)) {
                $browser = $value;
            }
        }
        return $browser;
    }
    public function getDispositivo()
    {
        return gethostbyaddr($this->getIp());
    }
    
    public function getSistema() {
            $os_array =  array(
                '/windows nt 10/i'      =>  'Windows 10',
                '/windows nt 6.3/i'     =>  'Windows 8.1',
                '/windows nt 6.2/i'     =>  'Windows 8',
                '/windows nt 6.1/i'     =>  'Windows 7',
                '/windows nt 6.0/i'     =>  'Windows Vista',
                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                '/windows nt 5.1/i'     =>  'Windows XP',
                '/windows xp/i'         =>  'Windows XP',
                '/windows nt 5.0/i'     =>  'Windows 2000',
                '/windows me/i'         =>  'Windows ME',
                '/win98/i'              =>  'Windows 98',
                '/win95/i'              =>  'Windows 95',
                '/win16/i'              =>  'Windows 3.11',
                '/macintosh|mac os x/i' =>  'Mac OS X',
                '/mac_powerpc/i'        =>  'Mac OS 9',
                '/linux/i'              =>  'Linux',
                '/ubuntu/i'             =>  'Ubuntu',
                '/iphone/i'             =>  'iPhone',
                '/ipod/i'               =>  'iPod',
                '/ipad/i'               =>  'iPad',
                '/android/i'            =>  'Android',
                '/blackberry/i'         =>  'BlackBerry',
                '/webos/i'              =>  'Mobile'
            );
            //
            $os_platform = "Unknown OS Platform";
            foreach ($os_array as $regex => $value) {
                if (preg_match($regex, $this->user_agent)) {
                    $os_platform = $value;
                }
            }
            return $os_platform;
        }
}
?>