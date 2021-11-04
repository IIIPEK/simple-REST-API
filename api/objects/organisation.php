<?php
define('__ROOT__', dirname(dirname(__FILE__)));

require_once 'api.php';
require_once __ROOT__ . '/config/database.php';

class Organisation extends Api
{

    // connect to table 'orgs' in database
    private $conn;
    private $table_name = 'orgs';
    private $relations = 'daughters';

    // object properties
    public $id;
    public $organisation;
    public $apiName = 'orgs';

    // constructor with connection to DB
    public function __construct($db)
    {
        parent::__construct();
        $this->conn = $db;
    }

    public function indexAction()
    {
        $organisations_arr = $this->getAllData();

        if (count($organisations_arr) > 0) {
            // set the response code - 200 OK 
            // and export data
            //var_dump();
            return $this->response($organisations_arr, 200);
        } else {

            // if number of records is 0 
            // set response code - 204 No Content 
            // report to user
            return $this->response(array("message" => $this->requestStatus(204)), 204);
        }
    }

    public function viewAction()
    {
        if (isset($this->requestParams["name"])) {
            $orgs_rels = $this->getData(trim($this->requestParams["name"]), $this->requestParams["p"] ? $this->requestParams["p"] : 1);
            if (isset($orgs_rels)) {
                usort($orgs_rels, array($this, "org_cmp"));
                return $this->response($orgs_rels, 200);
            } {
                // if number of records is 0 
                // set response code - 204 No Content 
                // report to user
                return $this->response(array("message" => $this->requestStatus(204)), 204);
            }
        } else {
            $index = $this->indexAction();
            return $index;
        }
    }

    public function createAction()
    { 

        $entityBody = json_decode(file_get_contents('php://input'), true);
        $new_list = $this->scanArr($entityBody);
        $old_list = array_column($this->getAllData($UPPER = true), "id", "org_name");
        $diff_data = array_diff_key($new_list, $old_list);
        $answer_orgs=null;
        $answer_rels=null;


        if (count($diff_data) > 0) {
            $answer_orgs=$this->addOrgs($diff_data);
            $old_list = array_column($this->getAllData($UPPER = true), "id", "org_name");
        }

        $old_rels = $this->getRels();

        $new_rels = $this->scanArrRels($entityBody, $old_list);
        $diff_data = array_diff_key($new_rels, $old_rels);
        if (count($diff_data))
        {
            $answer_rels=$this->addRels($diff_data);
        }
        if (isset($answer_orgs) or isset($answer_rels))
        {
        return $this->response(array("message" => $this->requestStatus(200)), 200);
        }
        else
        {
            return $this->response(array("message" => $this->requestStatus(304)), 304);
        }

    }

    public function updateAction()
    {
        return $this->response(array("message" => $this->requestStatus(405)), 405);
    }

    public function deleteAction()
    {
        return $this->response(array("message" => $this->requestStatus(405)), 405);
    }


    private function getAllData($UPPER = false)
    {
        $organisations_arr=array();
        $query = "SELECT
            id, " . ($UPPER ? "UPPER(organisation) " : "") . "organisation " .
            "FROM
        " . $this->table_name;

        // prepare query 
        $stmt = $this->conn->prepare($query);

        // execute query 
        $stmt->execute();
        $num = $stmt->rowCount();
        // if found records 
        if ($num > 0) {

            // array of organisations 
            $organisations_arr = array();

            // gets data from DB 
            // fetch() faster, them fetchAll() 
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                // extact record
                extract($row);

                $org_item = array(
                    "id" => $id,
                    "org_name" => $organisation,
                );

                array_push($organisations_arr, $org_item);
            }
        }
        return $organisations_arr;
    }

    // read relations from DB
    private function getRels()
    {
        $rels_arr=array();
        $query = "SELECT
            id_org, id_rel " .
            "FROM
        " . $this->relations;

        // prepare query 
        $stmt = $this->conn->prepare($query);

        // execute query 
        $stmt->execute();
        $num = $stmt->rowCount();
        // if found records 
        if ($num > 0) {

            // array of organisations 
            $rels_arr = array();

            // получаем содержимое нашей таблицы 
            // fetch() быстрее, чем fetchAll() 
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                // extact record
                extract($row);

                $org_item = array(
                    "id_org" => $id_org,
                    "id_rel" => $id_rel,
                );

                array_push($rels_arr, $org_item);
            }
        }
        return $rels_arr;
    }
    // create array from relations and dictionary table 
    private function getData($org_name)
    {

        // get all organisations with id_s
        $org_arr = $this->getAllData($UPPER = true);
        // search id for organisation in prarameters
        $org_id = array_search(strtoupper($org_name), array_map('trim', array_column($org_arr, "org_name", "id")));


        // if id is found
        if ($org_id) {
            $sisters_arr = array();


            $query = "SELECT 'parent' rel_type , organisation org_name from " . $this->table_name . ", " . $this->relations . "
            WHERE daughters_id = ? AND id=id_org";

            // prepare query 
            $parents_arr = $this->getQuery($query, array($org_id));

            $query =  "SELECT distinct 'sister' rel_type, organisation org_name
            FROM orgs, daughters as d, (select id_org from daughters where daughters_id=?) as a
            WHERE id=daughters_id and d.id_org = a.id_org and daughters_id<>?";
            $sisters_arr = $this->getQuery($query, array($org_id, $org_id));


            $query = "SELECT 'daughter' rel_type , organisation org_name from " . $this->table_name . ", " . $this->relations . "
            WHERE  id_org = ? AND id= daughters_id";

            $daughters_arr = $this->getQuery($query, array($org_id));

            $organisations_arr = array_merge($parents_arr, $sisters_arr, $daughters_arr);

            return $organisations_arr;
        }
    }
    // execute query and extract data to array

    private function getQuery($query, $arr_parms)
    {
        $data_arr = array();

        $stmt = $this->conn->prepare($query);

        // execute query 
        $stmt->execute($arr_parms);


        $num = $stmt->rowCount();

        // if found records 

        if ($num > 0) {


            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                // extact record
                extract($row);

                $org_item = array(
                    "relationship_type" => $rel_type,
                    "org_name" => $org_name,
                );

                array_push($data_arr, $org_item);
            }
        }
        return $data_arr;
    }
    // method for add organisations from array
    private function addOrgs($arr)
    {
        $qs = (str_repeat("(?),", count($arr) - 1));
        $query = "INSERT INTO " . $this->table_name . " (organisation) VALUES ${qs}(?)";
        $stmt = $this->conn->prepare($query);
        $vals = array_values($arr);
        return $stmt->execute($vals);
    }

    // method for add ralations from array
    private function addRels($arr)
    {
        $qs = (str_repeat("(?,?),", count($arr) - 1));
        $query = "INSERT INTO " . $this->relations . " (id_org, daughters_id) VALUES ".$qs."(?,?)";
        $stmt = $this->conn->prepare($query);
        $vals = call_user_func_array('array_merge',$arr);
        return $stmt->execute($vals);
    }

    // preparation method of POSTed array for insert new record to DB  
    private function scanArr($arr, $parent = null)
    {
        $new_org_list = array();
        foreach ($arr as $key => $val) {
            if ((strtolower(trim($key)) == "daughters") or (strtolower(trim($key)) != "org_name")) {
                $new_org_list = array_merge($new_org_list, $this->scanArr($val, $parent));
            } else {
                $parent = $val;
                if (!isset($new_org_list[(strtoupper(trim($val)))])) {
                    $new_org_list[strtoupper(trim($val))] = (trim($val));
                }
            }
        }
        return $new_org_list;
    }

    // preparation method of POSTed array for insert new relation record to DB
    private function scanArrRels($arr, $org_list, $parent = null)
    {
        $new_rels = array();
        $next_parent = null;


        foreach ($arr as $key => $val) {

            $key=strtolower(trim($key));
            if (($key == "daughters") or ($key != "org_name")) {
                if ($key == "daughters") {
                    //echo $key.PHP_EOL." : ".$next_parent;
                    $new_rels = array_merge($new_rels, $this->scanArrRels($val, $org_list, $next_parent));
                    //echo $parent.PHP_EOL;
                } else {
                    $new_rels = array_merge($new_rels, $this->scanArrRels($val, $org_list, $parent));
                }
            } else {
                $val = strtoupper(trim($val));
                if ($parent != null) {
                    if (isset($org_list[$val])) {
                        array_push($new_rels, array($parent, $org_list[$val]));
                    }
                }
                $next_parent = $org_list[$val];
            }
        }
        return $new_rels;
    }


    // method for sort by usort function
    private function org_cmp($a, $b)
    {
        return strnatcmp($a["org_name"], $b["org_name"]);
    }
}
