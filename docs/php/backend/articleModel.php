<?php
/*
 *  Modello per l'entità Articolo
 */

require_once('dbConnection.php');

class ArticleModel
{
    private $conn='';
    
    /* 
     * Costruttore che crea una nuova connessione a db
     */
    public function __construct()
    {
        $this->conn = new DbConnection();
    }
    
    /* 
     * Metodo che ritorna tutti gli articoli
     */
    public function getArticles()
    {
        return @mysqli_fetch_all($this->conn->execute("select * from Articles order by InsertDate"), MYSQLI_ASSOC);
    }
    
    /* 
     * @summary: effettua una ricerca di uno specifico articolo dato un id
     * @param: articleId - id dell'articolo da cercare
     * @return: array associativo contenente la tupla dell'articolo corrispondente
     */
    public function findArticleById($articleId)
    {
        return @mysqli_fetch_array($this->conn->execute("select * from Articles where Id = $articleId"), MYSQLI_ASSOC);
    }
    
    /* 
     * @summary: effettua una ricerca di uno specifico articolo dato un titolo
     * @param: articleTitle - titolo dell'articolo da cercare
     * @return: array associativo contenente la tupla dell'articolo corrispondente 
     */
    public function findArticleByTitle($articleTitle)
    {
        return @mysqli_fetch_assoc($this->conn->execute("select * from Articles where Id = $articleTitle"));
    }
    
    /* 
     * @summary: elimina un articolo dato il suo id
     * @param: articleId - id dell'articolo da eliminare
     * @return: true in caso la query abbia successo altrimenti false;
     */
    public function deleteArticleById($articleId)
    {
        return $this->conn->execute("delete from Articles where Id = $articleId");
    }
    
    /* 
     * @summary: aggiunge un articolo al db
     * @param: title - titolo dell'articolo da inserire
     * @param: concent - contenuto dell'articolo da inserire
     * @param: insertDate - data in cui è inserito un articolo
     * @return: true in caso la query abbia successo altrimenti false;
     */
    public function add_conn($title, $content, $insertDate)
    {
        return $this->conn->execute("insert into Articles (Title,ArticleTextContent,InsertDate) values ('$title','$content','$insertDate';");
    }
    
    /* 
     * @summary: modifica un articolo dato un id
     * @param: id - id dell'articolo da modificare
     * @param: title - titolo dell'articolo da modificare
     * @param: concent - contenuto dell'articolo da modificare
     * @return: true in caso la query abbia successo altrimenti false;
     */
    public function edit_conn($id, $title, $content)
    {
        return $this->conn->execute("update Articles set Title = '$title' , ArticleTextContent = '$content' where Id = $id");
    }
    
}
?>