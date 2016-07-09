# PdoPager
A php class for pagination, it has a simply function to return results from sql select statement with counting total found rows.

## Usage
Here is a simplest implementation example with none additional option.
Use your defined pdo object to construct PDOPager class. Assuming $pdo variable is defined and PDOPager.php is included.

    // $pdo = new PDO("...");
    // include('PdoPager.php');
  
    
    $pager = new \AGTR\PdoPager($pdo);

    $sql = "SELECT SQL_CALC_FOUND_ROWS id,name FROM table WHERE field = :value";
    $params = array("value"=>1);

    $data = $pager->paginate($sql,$params,3); // get the third page 
  
  Result $data array has two keys: "results" key has an associative array of query results, "pagination" key has some pagination info as shown below. 
  
    $data == 
    [ 
        "results" => 
        [ 
          "0" => ["id" =>33 , "name" ="thirty three"], 
          "1" => ["id" =>34 , "name" ="thirty four"],
          "2" => ["id" =>35 , "name" ="thirty five"],
          ...
          "16" => ["id" =>48 , "name" ="forty eight"],
        ],
        "pagination" => 
        [
          "total_items" => 99, // total count of item when query not paginated
          "count_items" => 16, // item count on current page
          "current_page" => 3, // current page number
          "last_page" => 7 // last page number for 99 item, 16 item per page. 
        ]
    ]
    
