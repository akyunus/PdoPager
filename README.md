# PdoPager
A php class for paginating sql select statement results with counting total found rows.

## Usage
Simplest implementation example with none additional option.
Use your defined pdo object to construct PDOPager class. Assuming $pdo variable is defined and PDOPager.php is included.

    // $pdo = new PDO("...");
    // include('PdoPager.php');
  
    
    $pager = new AGTR\PDOPager($pdo);

    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM table WHERE field = :value";
    $params = array("value"=>1);

    $result = $pager->paginate($sql,$params);
  
  Result array has two keys 
