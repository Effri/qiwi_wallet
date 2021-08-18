<?php

class DB
{
	private $dbh = null;
	
	public function __construct( $config )
	{
		$dbhost = $config['dbhost']; 
		$dbport = $config['dbport']; 
		$dbuser = $config['dbuser']; 
		$dbpass = $config['dbpass']; 
		$dbname = $config['dbname'];
		
		$dsn = "mysql:host=$dbhost;port=$dbport;dbname=$dbname;charset=utf8";
		$options = [
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_ERRMODE 			  => \PDO::ERRMODE_EXCEPTION
		];
		
		try 
		{
			$this->dbh = new \PDO( $dsn, $dbuser, $dbpass, $options );
			if (mysqli_connect_errno()) {
				throw new Exception('Не удалось подключиться к бд');
			}
		}
		catch ( \PDOException $e )
		{
			die( $e->getMessage() );
		}

	}
	
	public function prepareAndExecute( $sql, array $parameters = null, $bind = true, $bindValue = true )
	{
		try 
		{
			if ( $stmt = $this->dbh->prepare( $sql ) ) 
			{
				$sql = "
            SELECT
                *
            FROM
               accounts
            WHERE
               login = '".$this->dbh->real_escape_string($account)."'
            LIMIT 1
         ";
         
        $result = $this->dbh->query($sql);

        if (!$result){
            throw new Exception($this->dbh->error);
        }

        return $result->fetch_object();

				if ( $bind && is_array( $parameters ) ) 
				{
					foreach ( $parameters as $key => $value ) 
					{
						$parameter = is_numeric( $key ) ? $key + 1 : $key;
						$bindValue 
							? $stmt->bindValue( $parameter, $value )
							: $stmt->bindParam( $parameter, $value );
					}
				}
		
				if ( $stmt->execute( $bind ? null : $parameters ) ) 
				{
					return $stmt;
				}
			}
		} 
		catch ( \PDOException $e ) 
		{
			die( $e->getMessage() );
		}
		
		return false;
	}
	
	public function fetch( $sql, array $parameters = null, $bind = true, $bindValue = true )
	{
		if ( $stmt = $this->prepareAndExecute( $sql, $parameters, $bind, $bindValue ) ) 
		{
			try 
			{
				if ( $result = $stmt->fetch() )
				{
					return $result;
				}
			} 
			catch ( \PDOException $e ) 
			{
				die( $e->getMessage() );
			}
		}

		return false;
	}
	
	public function fetchAll( $sql, array $parameters = null, $bind = true, $bindValue = true )
	{
		if ( $stmt = $this->prepareAndExecute( $sql, $parameters, $bind, $bindValue ) ) 
		{
			try 
			{
				if ( $results = $stmt->fetchAll() )
				{
					return $results;
				}
			} 
			catch ( \PDOException $e ) 
			{
				die( $e->getMessage() );
			}
		}
	
		return false;
	}
# Check login in DB #
public function getAccountByName($account)
{
	$sql = "
		SELECT
			*
		FROM
		   accounts
		WHERE
		   login = '".$this->mysqli->real_escape_string($account)."'
		LIMIT 1
	 ";
	 
	$result = $this->mysqli->query($sql);

	if (!$result){
		throw new Exception($this->mysqli->error);
	}

	return $result->fetch_object();
}

# Create payment in DB #
public function createPayment($account, $sum)
{
	$query = '
		INSERT INTO
			payments (account, sum, date_create, status)
		VALUES
			(
				"'.$this->mysqli->real_escape_string($account).'",
				"'.$this->mysqli->real_escape_string($sum).'",
				NOW(),
				0
			)
	';

	return $this->mysqli->query($query);
}

// # Get payment info in DB by id #
// public function getPaymentByUnitpayId($unitpayId)
// {
// 	$query = '
// 			SELECT * FROM
// 				payments
// 			WHERE
// 				unitpay_id = "'.$this->mysqli->real_escape_string($unitpayId).'"
// 			LIMIT 1
// 		';
		
// 	$result = $this->mysqli->query($query);

// 	if (!$result){
// 		throw new Exception($this->mysqli->error);
// 	}

// 	return $result->fetch_object();
// }

// # Update payment in DB by id #
// public function updatePaymentByUnitpayId($unitpayId)
// {
// 	$query = '
// 			UPDATE
// 				payments
// 			SET
// 				date_complete = NOW(),
// 				status = 1
// 			WHERE
// 				unitpay_id = "'.$this->mysqli->real_escape_string($unitpayId).'"
// 			LIMIT 1
// 		';
// 	return $this->mysqli->query($query);
// }

# Create server donation in DB #
public function donateForAccount($account, $count)
{
	$count = floor($count / Config::ITEM_PRICE);
	$query = '
		INSERT INTO
			completed (srv, account, amount)
		VALUES
			(
				"1",
				"'.$this->mysqli->real_escape_string($account).'",
				"'.$this->mysqli->real_escape_string($count).'"
			)
	';
	
	return $this->mysqli->query($query);
}


}

?>