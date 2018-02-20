<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * @category   Kumbia
 * @package    Db
 * @copyright  Copyright (c) 2005-2012 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
/**
 * @see DbBaseInterface
 */
require_once CORE_PATH . 'libs/db/db_base_interface.php';
/**
 * @see DbBase
 */
require_once CORE_PATH . 'libs/db/db_base.php';

/**
 * Clase que maneja el pool de conexiones
 *
 * @category   Kumbia
 * @package    Db
 */
class Db
{

    /**
     * Singleton de conexiones a base de datos
     *
     * @var array
     */
    protected static $_connections = array();

    /**
     * Devuelve la conexión, si no existe llama a Db::connect para crearla
     *
     * @param boolean $new nueva conexion //TODO mirar si es necesaria
     * @param string $database base de datos a donde conectar
     * @return db
     */
    public static function factory($database = null, $new = false)
    {

        //Cargo el mode para mi aplicacion
        if (!$database) {
            $database = Config::get('config.application.database');
//			$database = self::asigna($database);
        }
        //Si no es una conexion nueva y existe la conexion singleton
        if (isset(self::$_connections[$database])) {
            return self::$_connections[$database];
        }

        return self::$_connections[$database] = self::connect($database);
    }

    /**
     * Realiza una conexión directa al motor de base de datos
     * usando el driver de Kumbia
     *
     * @param string $database base de datos a donde conectar
     * @return db
     */
    private static function connect($database)
    {
        $databases = Config::read('databases');
        $config = $databases[$database];
		$config = self::asigna($config);
        // carga los valores por defecto para la conexión, si no existen
        $default = array('port' => 0, 'dsn' => NULL, 'dbname' => NULL, 'host' => 'localhost', 'username' => NULL, 'password' => NULL);
        $config = $config + $default;				
        //Si usa PDO
        if (isset($config['pdo'])) {
            $dbclass = "DbPdo{$config['type']}";
            $db_file = "libs/db/adapters/pdo/{$config['type']}.php";
        } else {
            $dbclass = "Db{$config['type']}";
            $db_file = "libs/db/adapters/{$config['type']}.php";
        }

        //Carga la clase adaptadora necesaria
        if (!include_once CORE_PATH . $db_file) {
            throw new KumbiaException("No existe la clase $dbclass, necesaria para iniciar el adaptador");
        }

        return new $dbclass($config);
    }

	private static function escapacon($string, $key) {
		$result = '';
		$key .= 's07n31m4dn4m';
		$string = base64_decode(str_rot13($string));
		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
		if($result!=''){
			$result = substr($result,1,strlen($result)-2);   
			$let = $result[0];
			$let2 = $result[strlen($result)-1];
			$result[0] = $let2;
			$result[strlen($result)-1] = $let;
		}
		return strrev($result);
	}
	
	private static function asigna($param){
		$config['host'] = self::escapacon($param['host'],'host');
		$config['username'] = self::escapacon($param['username'],'username');
		$config['password'] = self::escapacon($param['password'],'password');
		$config['name'] = self::escapacon($param['name'],'name');
		$config['type'] = self::escapacon($param['type'],'type');
		return $config;
	}
}
