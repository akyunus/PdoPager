<?php namespace Bny\Helpers;
/**
 * 
 * SQL sorgu sonuçlarına LIMIT ekleyerek otomatik sayfalama yapar. 
 * sayfalama meta verisini otomatik oluşturur.
 * Pdo ile çalışır.
 * sql sorguları SELECT SQL_CALC_FOUND_ROWS ile yapılmalı
 * 
 * @author Yunus AK <yunus@ak.gen.tr>
 */
class PdoPager 
{

  // sayfa başına kayıt sayısı.		
  private $_pp = 15;
  //toplam kayıt sayısı
  private $_total = 0;
  // mevcut sayfa no
  private $_current = 0;
  // son sayfa no yani toplam sayfa adedi
  private $_last = 0;
  // mevcut sayfadaki kayıt sayısı, son sayfa değilse _pp'ye eşit
  private $_count = 0;
  
  private $_pdo;
  
  
  public function __construct($pdo,$opts=null)
  {
  	$this->_pdo = $pdo;
  	$this->_pp	= (isset($opts['per_page'])?$opts['per_page'] : $this->_pp);
  }
  
  // sql sorgusu SELECT SQL_CALC_FOUND_ROWS ifadesi ile başlıyorsa 
  // getPaginationMeta fonksiyonu düzgün çalışacaktır. performans için bu yöntem kullanıldı.
  // Bu fonksiyon verilen page no ile belirtilen sayfa numarasında bulunan kayıtların listesini
  // array formatında döndürür.
  public function paginate($sql,$vars = null,$page = 1,$perpage = null, $getmeta = false)
  {
	
  	$perpage 	= (is_null($perpage)) 	? $this->_pp 	: $perpage;
  	$page    	= ($page < 1) 			? 1 			: $page;
  	
		$start 		= $perpage * ($page - 1);
		
		$sql .= " LIMIT $start, $perpage";
		
	// tüm sorguları error_log dosyasına kaydetmek için...
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
    
    // toplam kayıt sayısını bul
    $this->_total = $this->_pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
    
    $this->_current = $page;
    
    $this->_last = floor((($this->_total - 1) / $perpage) + 1);
    
    //TODO: mevcut kayıt sayısını hesapla
    if ($this->_current == $this->_last) 
    {
    	$this->_count   = count($data);
    } else {
    	$this->_count   = $perpage;	
    }
    
    
    
    return $data;
    
  }
  
  // paginate ile sonuçlar alındıktan sonra getpaginationmeta ile sayfalama bilgisi alınabilir.
  // sql sorgusu SELECT SQL_CALC_FOUND_ROWS ifadesi ile başlıyorsa 
  // getPaginationMeta fonksiyonu anlamlı çalışacaktır.
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