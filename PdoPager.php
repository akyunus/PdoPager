<?php namespace Bny\Helpers;
/**
 * 
 * SQL sorgu sonu�lar�na LIMIT ekleyerek otomatik sayfalama yapar. 
 * sayfalama meta verisini otomatik olu�turur.
 * Pdo ile �al���r.
 * sql sorgular� SELECT SQL_CALC_FOUND_ROWS ile yap�lmal�
 * 
 * @author Yunus AK <yunus@ak.gen.tr>
 */
class PdoPager 
{

  // sayfa ba��na kay�t say�s�.		
  private $_pp = 15;
  //toplam kay�t say�s�
  private $_total = 0;
  // mevcut sayfa no
  private $_current = 0;
  // son sayfa no yani toplam sayfa adedi
  private $_last = 0;
  // mevcut sayfadaki kay�t say�s�, son sayfa de�ilse _pp'ye e�it
  private $_count = 0;
  
  private $_pdo;
  
  
  public function __construct($pdo,$opts=null)
  {
  	$this->_pdo = $pdo;
  	$this->_pp	= (isset($opts['per_page'])?$opts['per_page'] : $this->_pp);
  }
  
  // sql sorgusu SELECT SQL_CALC_FOUND_ROWS ifadesi ile ba�l�yorsa 
  // getPaginationMeta fonksiyonu d�zg�n �al��acakt�r. performans i�in bu y�ntem kullan�ld�.
  // Bu fonksiyon verilen page no ile belirtilen sayfa numaras�nda bulunan kay�tlar�n listesini
  // array format�nda d�nd�r�r.
  public function paginate($sql,$vars = null,$page = 1,$perpage = null, $getmeta = false)
  {
	
  	$perpage 	= (is_null($perpage)) 	? $this->_pp 	: $perpage;
  	$page    	= ($page < 1) 			? 1 			: $page;
  	
		$start 		= $perpage * ($page - 1);
		
		$sql .= " LIMIT $start, $perpage";
		
	// t�m sorgular� error_log dosyas�na kaydetmek i�in...
	// error_log("Pager SQL : $sql");
	try
	{

		$stmt = $this->_pdo->prepare($sql);
		$stmt->execute($vars);
    }
	catch(\PDOException $e)
	{
		error_log($e->getMessage());
	}
	
    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // toplam kay�t say�s�n� bul
    $this->_total = $this->_pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
    
    $this->_current = $page;
    
    $this->_last = floor((($this->_total - 1) / $perpage) + 1);
    
    //TODO: mevcut kay�t say�s�n� hesapla
    if ($this->_current == $this->_last) 
    {
    	$this->_count   = count($data);
    } else {
    	$this->_count   = $perpage;	
    }
    
    
    
    return $data;
    
  }
  
  // paginate ile sonu�lar al�nd�ktan sonra getpaginationmeta ile sayfalama bilgisi al�nabilir.
  // sql sorgusu SELECT SQL_CALC_FOUND_ROWS ifadesi ile ba�l�yorsa 
  // getPaginationMeta fonksiyonu anlaml� �al��acakt�r.
  public function getPaginationMeta()
  {
  	$meta = array(
  			"total" => $this->_total,
  			"count" => $this->_count,
  			"current" => $this->_current,
  			"last" => $this->_last,
  		);
  		
    return $meta;
  }
}