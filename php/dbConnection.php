<?php
/*
 *  Classe per la gestione del database
 *  Implementa metodi per la connessione e gestione delle query
 */
class DbConnection
{
    private const HOST = '127.0.0.1';
    private const USERNAME = 'root';
    private const PASSWORD = '';
    private const DATABASE_NAME = 'test';
    private $current_connection;
    private $error = '';

    /**
	 * @summary Il costruttore della classe tenta di effettuare una connessione al database. In caso di mancata riuscita ritorna a video un errore (non di php)
     */
    public function __construct()
    {
        if (!(@$this->current_connection = mysqli_connect(static::HOST, static::USERNAME, static::PASSWORD, static::DATABASE_NAME))) {
            error_log("Debugging errno: " . mysqli_connect_errno()."Debugging error: " . mysqli_connect_error());
            $this->error = "Momentaneamente i dati non sono disponibili. Riprovare più tardi.";
        }
    }

    /**
	 * @summary Ritorna l'ultimo errore loggato avvenuto all'interno della classe. 
     * @return stringa contenente una descrizione dell'errore avvenuto
     */
    public function getLastError()
    {
        return $this->error;
    }
    
    /**
     * @return il metodo ritorna la connesione corrente di $this
	 */
    public function getCurrent()
    {
        return $this->current_connection;
    }

	/**
	 * @param query con zero o più parametri espressi da '?'
	 * @return sql statement che contiene la $query pronta a ricevere i parametri 
     */
    public function prepareQuery($query) {
        $stmt = mysqli_stmt_init($this->current_connection);
        mysqli_stmt_prepare($stmt, $query);
        return $stmt;
    }

	/**
	 * @param stmt statement con una query di interrogazione dati
	 *        a cui sono già stati forniti gli  eventuali parametri
	 * @return il risultato di $stmt appilcato a $this->current_connection
	 */
    public function executePreparedQuery($stmt) // select
    {
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

	/**
	 * @param stmt statement con una query di manipulazione  dati
	 *        a cui sono già stati forniti gli  eventuali parametri
	 * @return il risultato di $stmt appilcato a $this->current_connection
	 */
    public function executePreparedQueryDML($stmt) // insert e update
    {
        return mysqli_stmt_execute($stmt);
    }

	/**
	 * @return L'id generato dall'ultima query di manipolazione dati
	 *         riuscita su $this->current_connection
 	 */
    public function getInsertId(){
        return  mysqli_insert_id($this->current_connection);
    }

    /**
	 * @summary viene eseguita la disconnessione di $this->current_connection 
	 */
    public function disconnect()
    {
        mysqli_close($this->current_connection);
    }
}
?>
