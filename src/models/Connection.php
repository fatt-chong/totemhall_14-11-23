<?php
class Connection extends PDO
{
	private $dns;
	private $lnk; //puntero a la BD
	private $user;
	private $pass;
	private $server; //nombre del servidor
	private $database; //nombre de la base de datos
	private $rs; //resultado de la consulta.
	protected $transactionCounter = 0;
	private $isDEV=false;

	function __construct()
	{

		$this->dns      = dnsDbTotemHall;
		$this->database = dataBaseTotemHall;

		$this->user   = userDbTotemHall;
		$this->pass   = passDbTotemHall;
		$this->server = servidorDbTotemHall;


	}

	/*************SOBRECARGAS*************/
	// function beginTransaction()
	// {
	// 	if (!$this->transactionCounter++)
	// 		return parent::beginTransaction();
	// 	return $this->transactionCounter >= 0;
	// }

	// function commit()
	// {
	// 	if (!--$this->transactionCounter)
	// 		return parent::commit();
	// 	return $this->transactionCounter >= 0;
	// }

	// function rollback()
	// {
	// 	if ($this->transactionCounter >= 0) {
	// 		$this->transactionCounter = 0;
	// 		return parent::rollback();
	// 	}
	// 	$this->transactionCounter = 0;
	// 	return false;
	// }
	/************************************/

	function db_connect()
	{
		try {
			$this->lnk = parent::__construct($this->dns . ':dbname=' . $this->database . ';host=' . $this->server, $this->user, $this->pass);
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
		//$this->lnk = mysql_connect($this->server,$this->user,$this->pass) or die("Error de conexion a BD");
	}

	function db_select()
	{
		$this->query("USE {$this->database}");
	}

	// function db_close()
	// {
	// 	mysql_close($this->lnk);
	// }

	function consultaSQL($query, $error)
	{
		//set_time_limit ( 0 );
		$datos = array();
		$this->query('SET NAMES UTF8');
		$this->rs = $this->query($query, PDO::FETCH_ASSOC) or die($error . "\n" . print_r($this->errorInfo()));

		foreach ($this->rs as $v) {
			$datos[] = $v;
		}
		return $datos;
	}

	function consultaSQLSimple($query, $error)
	{
		//set_time_limit ( 0 );
		$datos = array();
		$this->query('SET NAMES UTF8');
		$this->rs = $this->query($query, PDO::FETCH_ASSOC) or die($error . "\n" . print_r($this->errorInfo()));

		foreach ($this->rs as $v) {
			$datos[] = $v;
		}
		return $datos;
	}

	function ejecutarSQL($query, $error)
	{ //PARA LOS INSERT, UPDATE Y DELETE
		//set_time_limit ( 0 );
		$this->query('SET NAMES UTF8');
		$cont = $this->query($query) or die($error . "\n" . print_r($this->errorInfo()));
		return $cont;
	}

	function setDB($database)
	{
		$this->database = $database;
		$this->db_select();
	}

	function setServer($server)
	{
		$this->server = $server;
	}

	function setIsDev($isDEV){
		$this->isDEV = $isDEV;
	}

	function getIsDev(){
		return $this->isDEV;
	}
	
	/**
	* Seteando nuevos datos de conexion
	* @author Fatt Chong
	* @param String $dns motor Db
	* @param String $database Nombre de la base de datos
	* @param String $username Usuario de conexion
	* @param String $pass Contraseña del usuario
	* @param String $server ip del servidor 
	*/
	function setDataConexion($dns, $database, $user, $pass, $server){
		$this->dns      = $dns;
		$this->database = $database;
		$this->user   = $user;
		$this->pass   = $pass;
		$this->server = $server;
	}

	/**
	* Metodo consulta select segura sql (SELECT) 
	* @param String $query La consulta SQL
	* @param String $clase Nombre del Modelo
	* @param String $metodo Nombre del metodo del modelo utilizado
	* @param Array $bind los BindParam de la consulta segura, si no tiene dejar en valor null
    * @return Array $response con su respectivo contenido en caso de ser valido o invalido
	* @author Fatt Chong 
	*/
	function consultaSelectSeguraSQL($query, $clase, $metodo, $bind){
		//Es posible hacer un SELECT sin bindParam, para eso el $bind debe ser null

        try{
			//nueva transaccion en la conexion de la DB
            $this->beginTransaction();
			//declaro un array de datos vacio
			$datos = array();
			//establece una conexion tipo UTF-8
			$this->prepare('SET NAMES UTF8');
			//prepara la consulta sql, si usa bindParams aun no contiene los valores reales, posee marcadores de posicion
			$x = $this->rs = $this->prepare($query);
			//si tiene bindParam
			if($bind){
				//la $key es el nombre del marcador de posicion en la consulta preparada y el $val es el que se enlazara en el marcador de posicion
				foreach($bind as $key => &$val){
					//asignamo el tipo de dato correspondiente
					if(is_int($val)){
						$PDOtipoDato = PDO::PARAM_INT;
					}else if(is_bool($val)){
						$PDOtipoDato = PDO::PARAM_BOOL;
					}else{
						$PDOtipoDato = PDO::PARAM_STR;
					}
					//enlaza cada valor a su respectivo marcador de posicion
					$x->bindParam($key, $val, $PDOtipoDato);
				}
			}
			//ejecuta la query
			$x->execute();
			//recuperando todas las filas del resultado
			$result = $x->fetchAll(PDO::FETCH_ASSOC);
			//se almacena el resultado de la consulta
			$this->rs = $result;
			//copia los datos almacenadoi en rs al array datos
			foreach($this->rs as $v){
				$datos[] = $v;
			}
			//Operacion realizada con exito, confirmar transaccion
            $this->commit();
			//devuelve la respuesta
            return $datos;
        }catch(PDOException $e){
			//se produjo un error, deshacer toda las operaciones
			$this->rollback();
			//devuelve una respuesta errorea
			return array(
                "status"  => 500,
                "type"    => "Error mySql",
                "message" => $e->getMessage(),
				"clase"   => $clase,
                "metodo"  => $metodo
            );

        }
        
    }

	/**
	* Metodo para ejecutar sql de forma segura (INSERT, UPDATE Y DELETE).
	* No devuelve el ultimo id registrado o los datos actualizado ni el id eliminado
	* @param String $query La consulta SQL
	* @param String $clase Nombre del Modelo
	* @param String $metodo Nombre del metodo del modelo utilizado
	* @param Array $bind los BindParam de la consulta segura, si no tiene dejar en valor null
    * @return Array $response con su respectivo contenido en caso de ser valido o invalido
	* @author Fatt Chong 
	*/
	function ejecutarSQLSeguro($query, $clase, $metodo, $bind){
		//Es posible hacer un INSERT, UPDATE Y DELETE sin bindParam, para eso el $bind debe ser null
        
		try{
			//nueva transaccion en la conexion de la DB
            $this->beginTransaction();
			//establece una conexion tipo UTF-8
            $this->prepare('SET NAMES UTF8');
			//prepara la consulta sql, si usa bindParams aun no contiene los valores reales, posee marcadores de posicion
            $cont = $this->prepare($query);
			//si tiene bindParam
			if($bind){
				//la $key es el nombre del marcador de posicion en la consulta preparada y el $val es el que se enlazara en el marcador de posicion
				foreach($bind as $key => &$val){
					//asignamo el tipo de dato correspondiente
					if(is_int($val)){
						$PDOtipoDato = PDO::PARAM_INT;
					}else if(is_bool($val)){
						$PDOtipoDato = PDO::PARAM_BOOL;
					}else{
						$PDOtipoDato = PDO::PARAM_STR;
					}
					//enlaza cada valor a su respectivo marcador de posicion
					$cont->bindParam($key, $val, $PDOtipoDato);
				}
			}
			//ejecuta la query
            $p = $cont->execute();
			//Operacion realizada con exito, confirmar transaccion
            $this->commit();
			//devuelve la respuesta
            return $p;
        }catch(PDOException $e){
			//se produjo un error, deshacer toda las operaciones
            $this->rollback();
			//devuelve una respuesta errorea
            return array(
                "status"  => 500,
                "type"    => "Error mySql",
                "message" => $e->getMessage(),
				"clase"   => $clase,
                "metodo"  => $metodo
            );
        };
        
    }

	/**
	* Insertar registro en la DB
	*
	* Creacion: (FC) 28-09-23 - Modificado: (FC) 28-09-23
	*
	* @param String $query consulta sql
	* @param String $clase nombre de la clase desde la que se esta ejecutando el metodo
	* @param String $metodo nombre del metodo de la clase que esta ejecutando el metodo
	* @param Array $bind los bindParams con su nombre y valor, ejemplo $bind['nombre'] = 'valor'
	* @param Array $response respuesta de la consulta, puede ser con status 200 o 500
	* @author Fatt Chong 
	*/
	public function insert($query, $clase, $metodo, $bind){
		
		//si la query es insert
		if(stripos($query, "insert") !== false) {
			//seteando la respuesta ok
			$response = $this->ejecutarQuery($query, $clase, $metodo, $bind, "insert");
		}else{
			//seteando respuesta erronea
			$response = array(
				"status"  => 500,
				"type"    => "Error de query insert",
				"message" => "No fue encontrada la query para hacer un insert",
				"query"	  => $query,
				"clase"   => $clase,
				"metodo"  => $metodo
			);
		}
		//devuelvo la respuesta
		return $response;
	}

	/**
	* Actualizar registro en la DB
	*
	* Creacion: (FC) 28-09-23 - Modificado: (FC) 28-09-23
	*
	* @param String $query consulta sql
	* @param String $clase nombre de la clase desde la que se esta ejecutando el metodo
	* @param String $metodo nombre del metodo de la clase que esta ejecutando el metodo
	* @param Array $bind los bindParams con su nombre y valor, ejemplo $bind['nombre'] = 'valor'
	* @param Array $response respuesta de la consulta, puede ser con status 200 o 500
	* @author Fatt Chong 
	*/
	public function update($query, $clase, $metodo, $bind){
		//si la query es update
		if(stripos($query, "update") !== false) {
			//seteando la respuesta ok
			$response = $this->ejecutarQuery($query, $clase, $metodo, $bind, "update");
		}else{
			//seteando respuesta erronea
			$response = array(
				"status"  => 500,
				"type"    => "Error de query update",
				"message" => "No fue encontrada la query para hacer un update",
				"query"	  => $query,
				"clase"   => $clase,
				"metodo"  => $metodo
			);
		}
		//devuelvo la respuesta
		return $response;
	}

	/**
	* Eliminar registro en la DB
	*
	* Creacion: (FC) 28-09-23 - Modificado: (FC) 28-09-23
	*
	* @param String $query consulta sql
	* @param String $clase nombre de la clase desde la que se esta ejecutando el metodo
	* @param String $metodo nombre del metodo de la clase que esta ejecutando el metodo
	* @param Array $bind los bindParams con su nombre y valor, ejemplo $bind['nombre'] = 'valor'
	* @param Array $response respuesta de la consulta, puede ser con status 200 o 500
	* @author Fatt Chong 
	*/
	public function delete($query, $clase, $metodo, $bind){
		//si la query es delete
		if(stripos($query, "delete") !== false) {
			//seteando la respuesta ok
			$response = $this->ejecutarQuery($query, $clase, $metodo, $bind, "delete");
		}else{
			//seteando respuesta erronea
			$response = array(
				"status"  => 500,
				"type"    => "Error de query delete",
				"message" => "No fue encontrada la query para hacer un delete",
				"query"	  => $query,
				"clase"   => $clase,
				"metodo"  => $metodo
			);
		}
		//devuelvo la respuesta
		return $response;
	}

	/**
	* Seleccionar registros en la DB
	*
	* Creacion: (FC) 28-09-23 - Modificado: (FC) 28-09-23
	*
	* @param String $query consulta sql
	* @param String $clase nombre de la clase desde la que se esta ejecutando el metodo
	* @param String $metodo nombre del metodo de la clase que esta ejecutando el metodo
	* @param Array $bind los bindParams con su nombre y valor, ejemplo $bind['nombre'] = 'valor'
	* @param Array $response respuesta de la consulta, puede ser con status 200 o 500
	* @author Fatt Chong 
	*/
	public function select($query, $clase, $metodo, $bind){
		//si la query es select
		if(stripos($query, "select") !== false) {
			//seteando la respuesta ok
			$response = $this->ejecutarQuery($query, $clase, $metodo, $bind, "select");
		}else{
			//seteando respuesta erronea
			$response = array(
				"status"  => 500,
				"type"    => "Error de query update",
				"message" => "No fue encontrada la query para hacer un select",
				"query"	  => $query,
				"clase"   => $clase,
				"metodo"  => $metodo
			);
		}
		//devuelvo la respuesta
		return $response;
	}

	/**
	* Ejecutar query 
	* @param String $query La consulta SQL
	* @param String $clase Nombre del Modelo
	* @param String $metodo Nombre del metodo del modelo utilizado
	* @param Array $bind los BindParam de la consulta segura, si no tiene dejar en valor null
	* @param String $tipo puede ser INSERT, UPDATE, SELECT, DELETE
    * @return Array $response con su respectivo contenido en caso de ser valido o invalido
	* @author Fatt Chong 
	*/
	function ejecutarQuery($query, $clase, $metodo, $bind, $tipo=""){

        try{
			//nueva transaccion en la conexion de la DB
            $this->beginTransaction();
			//declaro un array de datos vacio
			$datos = array();
			//establece una conexion tipo UTF-8
			$this->prepare('SET NAMES UTF8');
			//prepara la consulta sql, si usa bindParams aun no contiene los valores reales, posee marcadores de posicion
			$x = $this->rs = $this->prepare($query);
			//si tiene bindParam
			if($bind){
				//la $key es el nombre del marcador de posicion en la consulta preparada y el $val es el que se enlazara en el marcador de posicion
				foreach($bind as $key => &$val){
					//asignamo el tipo de dato correspondiente
					if(is_int($val)){
						$PDOtipoDato = PDO::PARAM_INT;
					}else if(is_bool($val)){
						$PDOtipoDato = PDO::PARAM_BOOL;
					}else{
						$PDOtipoDato = PDO::PARAM_STR;
					}
					//enlaza cada valor a su respectivo marcador de posicion
					$x->bindParam($key, $val, $PDOtipoDato);
				}
			}
			//ejecuta la query
			$x->execute();

			switch(strtoupper($tipo)){
				
				case 'INSERT':
					//ultimo ID que registro en caso que la tabla tenga el campo 'id'
					$lastInsertId = $this->lastInsertId();
					//Operacion realizada con exito, confirmar transaccion
					
					//respuesta exitosa despues de registrar
					$datos = array(
						"status"	=> 200,
						"message"	=> "Se registro correctamente",
						"last_insert_id" => $lastInsertId,
					);

				break;
				
				case 'UPDATE':
					//echo $query;
					//seteando la respuesta
					$datos = array(
						"status"	=> 200,
						"message"   => "Actualizado con éxito"
					);

				break;

				case 'SELECT':

					//recuperando todas las filas del resultado
					$result = $x->fetchAll(PDO::FETCH_ASSOC);
					//se almacena el resultado de la consulta
					$this->rs = $result;
					//copia los datos almacenadoi en rs al array datos
					foreach($this->rs as $v){
						$datos[] = $v;
					}
					//seteando la respuesta
					$status = 200;
					$datos = compact("status", "datos");

				break;

				case 'DELETE':
					
					$datos = array(
						"status"	=> 200,
						"message"	=> "Se elimino correctamente"
					);

				break;

				default:

					$datos = array(
						"status"  => 500,
						"type"    => "Error de query",
						"message" => "No especifico que tipo de query quiere realizar",
						"clase"   => $clase,
						"metodo"  => $metodo
					);

				break;
			}
			
			//Operacion realizada con exito, confirmar transaccion
            $this->commit();
			//devuelve la respuesta
            return $datos;
        }catch(PDOException $e){
			//se produjo un error, deshacer toda las operaciones
			$this->rollback();
			//devuelve una respuesta errorea
			return array(
                "status"  => 500,
                "type"    => "Error mySql",
                "message" => $e->getMessage(),
				"clase"   => $clase,
                "metodo"  => $metodo
            );

        }
        
    }

}

?>