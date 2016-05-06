<?php namespace AGTR;
/**
 * 
 * SQL sorgu sonuçlarýna LIMIT ekleyerek otomatik sayfalama yapar. 
 * sayfalama meta verisini otomatik oluþturur.
 * Pdo ile çalýþýr.
 * sql sorgularý SELECT SQL_CALC_FOUND_ROWS ile yapýlmalý
 * 
 * @author Yunus AK <yunus@ak.gen.tr>
 */
class PdoPager 
{

  // records per page		
  private $_pp = 15;
  // total record cout
  private $_total = 0;
  // current page record count, 
  private $_count = 0;  
  // current page no
  private $_current = 0;
  // total page no
  private $_last = 0;

  // this class 
  private $_pdo;
  
  
  public function __construct($pdo,$opts=null)
  {
  	$this->_pdo = $pdo;
  	$this->_pp	= (isset($opts['per_page'])?$opts['per_page'] : $this->_pp);
  }
  
  public function paginate($sql,$vars = null,$page = 1,$perpage = null, $getmeta = false)
  {
	
  	$perpage 	= (is_null($perpage)) 	? $this->_pp 	: $perpage;
  	$page    	= ($page < 1)		? 1 		: $page;
	$start 		= $perpage * ($page - 1);
	
	$this->_current = $page;
	
	$sql .= " LIMIT $start, $perpage";
		
	try
	{
		$stmt = $this->_pdo->prepare($sql);
		$stmt->execute($vars);
	}
	catch(\PDOException $e)
	{
		error_log($e->getMessage());
	}
	
	$data['results'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
	
	
	$data['pagination'] = $this->getPaginationMeta();
    
    return $data;
}
  
  // paginate ile sonuçlar alýndýktan sonra getpaginationmeta ile sayfalama bilgisi alýnabilir.
  // sql sorgusu SELECT SQL_CALC_FOUND_ROWS ifadesi ile baþlýyorsa 
  // getPaginationMeta fonksiyonu anlamlý çalýþacaktýr.
  private function getPaginationMeta()
  {
  	// get total found rows
	$this->_total = $this->_pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
	
	// current page no is known
	
	
	// calcute total number of pages
	$this->_last = floor((($this->_total - 1) / $perpage) + 1);
	
	// only if in last page, current pages record count may be 
	if ($this->_current == $this->_last) 
	{
	    $this->_count   = count($data);
	} else {
	    $this->_count   = $perpage;	
	}
  	
  	$meta = array(
  			"total" => $this->_total,
  			"count" => $this->_count,
  			"current" => $this->_current,
  			"last" => $this->_last,
  		);
  		
    return $meta;
  }
}
