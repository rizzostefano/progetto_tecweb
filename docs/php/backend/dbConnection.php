<?php
/*
 *  Classe per la gestione del database
 *  Implementa metodi per la connessione e gestione delle query
 */
 class DbConnection
 {
     private const HOST = 'localhost';
     private const USERNAME = 'root';
     private const PASSWORD = '';
     private const DATABASE_NAME = 'test';
     private $current_connection;

     /* Il costruttore della classe tenta di effettuare una connessione al database. In caso di mancata riuscita ritorna a video un errore (non di php) */
     public function __construct()
     {
         if (!($this->current_connection = @mysqli_connect(static::HOST, static::USERNAME, static::PASSWORD, static::DATABASE_NAME))) {
             error_log("Debugging errno: " . mysqli_connect_errno()."Debugging error: " . mysqli_connect_error());
             echo "Momentaneamente i dati non sono disponibili. Riprovare più tardi.";
         }
     }
     /* Il metodo ritorna la corrente connessione */
     public function getCurrent()
     {
         return $this->current_connection;
     }
     /* Il metodo esegue e ritorna il risultato di una query */
     public function execute($query)
     {
         $result = @mysqli_query($this->current_connection, $query);
         @mysqli_close($this->current_connection);
         return $result;
     }
     /* Il metodo esegue la disconnessione dal database */
     public function disconnect()
     {
         @mysqli_close($this->current_connection);
     }
 }
 ?>