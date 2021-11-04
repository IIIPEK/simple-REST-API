<?php
/**
 * Abstract Api Class
 * 
 * for creating child api classes 
 * @method initializing  properties by current request
 */
abstract class Api
{
    /**
     * @var string $apiName name of resource
     * @var string $method type of request
     * @var array $requestUri  URI of current request exploded by "/"
     * @var array $requestPaqrams  array of request paraneters where keys converted to lowercase
     * @var int $page number page to display
     * @var int $rec_per_page number of records showing on page, default limits 1..100
     * @var string $action protected parameter for call class method by name
     */
    public $apiName = ''; //organisations
    protected $method = ''; //GET|POST|PUT|DELETE
    public $requestUri = [];
    public $requestParams = [];
    public $page = 1;
    public $recs_per_page=100;
    protected $action = ''; // method's name for action

    /**
     * @api constructor, initialize Api-object, prepare and route requests
     * 
     */
    public function __construct() {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json; charset=UTF-8");

        //param'3s array of GET splitted by "/"
        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $this->requestParams = array_change_key_case($_REQUEST);

        // parameters for pagination
        $this->page = isset($this->requestParams['p']) ? $this->requestParams['p'] : $this->page;
        $this->recs_per_page=isset($this->requestParams['rp']) ? $this->requestParams['rp'] : $this->recs_per_page;
        
        // records per page in range 1 to 100
        $this->recs_per_page= ($this->recs_per_page>100 )? 100: ($this->recs_per_page < 1 ? 100: $this->recs_per_page) ;

        //define the request method
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    /**
     * @api run execute action if action defined else return "Error 404"
     * 
     * @return $this->action result
     */
    public function run() {
        //first element of URI have be "api" and second element table name
        if(array_shift($this->requestUri) !== 'api' || array_shift($this->requestUri) !== $this->apiName){
            throw new RuntimeException('API Not Found', 404);
        }
        //Define an action 
        $this->action = $this->getAction();
//        var_dump($this->action);

        //if method is defined in a child API class
        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    /**
     * @api this method prepare JSON from $data array
     * 
     * @return JSON from $data and set $status to answer header
     * @param array $data array of returned data for requst
     * @param int $status answer code for request
     */
    protected function response($data, $status = 500) {

        $pages=array_slice($data,$this->recs_per_page * ( $this->page-1),$this->recs_per_page);
        $status= count($pages)==0 ? 416: $status;

        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        
        return json_encode($pages, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @api sets some statuses wich may be returned by Api
     * 
     * @return string  $status by $code, if code not found using 500
     * @param int $code key 
     * @see /shared/Errors.php for full list of statuses
     */
    protected function requestStatus($code) {
        $status = array(
            200 => 'OK',
            201 => 'Created',
		    202 => 'Accepted',
		    203 => 'Non-Authoritative Information',
		    204 => 'No Content',
            205 => 'Reset Content',
		    206 => 'Partial Content',
		    300 => 'Multiple Choices',
		    301 => 'Moved Permanently',
		    302 => 'Found',
		    303 => 'See Other',
		    304 => 'Not Modified',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            416 => 'Requested Range Not Satisfiable',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }
    /**
     * @api select method name by type reuest [GET,POST,...]
     * @return method for handling the current request
     */
    protected function getAction()
    {
        $method = $this->method;
        switch ($method) {
            case 'GET':
                if($this->requestUri){
                    return 'viewAction';
                } else {
                    return 'indexAction';
                }
                break;
            case 'POST':
                return 'createAction';
                break;
            case 'PUT':
                return 'updateAction';
                break;
            case 'DELETE':
                return 'deleteAction';
                break;
            default:
                return null;
        }
    }

    /**
     * @api abstract method indexAction() for define in child's class
     * @api abstract method viewAction() for define in child's class
     * @api abstract method createAction() for define in child's class
     * @api abstract method updateAction() for define in child's class
     * @api abstract method deleteAction() for define in child's class
     */
    abstract protected function indexAction();
    abstract protected function viewAction();
    abstract protected function createAction();
    abstract protected function updateAction();
    abstract protected function deleteAction();
}

?>