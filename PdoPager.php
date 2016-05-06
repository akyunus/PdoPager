<?php namespace AGTR;
/**
 * 
 * PdoPager Class calculates and adds LIMIT statement to a SQL query string for paginating results. 
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
  /**
   * construct method dependency injection with pdo
   */
  public function __construct(\PDO $pdo,$opts=null)
  {
  	$this->_pdo = $pdo;
  	$this->_pp	= (isset($opts['per_page'])?$opts['per_page'] : $this->_pp);
  }
/**
 * Generates the query string, execute it with variables and return the result set.
 * 
 * 
 */
  public function paginate($sql,$params = null,$page = 1,$perpage = null, $getmeta = true)
  {
	
  	$perpage 	= (is_null($perpage)) 	? $this->_pp 	: $perpage;
  	$page    	= ($page < 1)		? 1 		: $page;
	$start 		= $perpage * ($page - 1);
	
	$sql .= " LIMIT $start, $perpage";
		
	try
	{
		$stmt = $this->_pdo->prepare($sql);
		$stmt->execute($params);
	}
	catch(\PDOException $e)
	{
		error_log($e->getMessage());
	}
	
	$data['results'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
	
	
	// get total found rows
	$this->_total = $this->_pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
	// current page no is known
	$this->_current = $page;
	// calcute total number of pages
	$this->_last = floor((($this->_total - 1) / $perpage) + 1);
	
	// only if in last page, current pages record count may be 
	if ($this->_current == $this->_last) 
	{
	    $this->_count   = count($data);
	} else {
	    $this->_count   = $perpage;	
	}
	
	
	$data['pagination'] = $this->getPaginationMeta();
    
    return $data;
}
  
/**
 * use mysql queries with SELECT SQL_CALC_FOUND_ROWS for accurate pagination data.
 */
  private function getPaginationMeta()
  {
  	$meta = array(
  			"records_total" => $this->_total,
  			"records_current" => $this->_count,
  			"current_page_no" => $this->_current,
  			"last_page_no" => $this->_last,
  		);
  		
    return $meta;
  }
}
