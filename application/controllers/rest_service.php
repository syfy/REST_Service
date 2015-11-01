<?php
abstract class Rest_service extends CI_Controller {

    protected $method; // HTTP method, e.g. GET, POST, etc.
    protected $format; // Requested response format, e.g. JSON
    protected $parameters; // Request parameters from the query string
    protected $valid_date_range_in_seconds;

    // set this if you want to define a default response format.
    protected $default_format; 

    // List all supported formats
    protected $supported_formats = array();

    function __construct()
    {
        parent::__construct();
        $this->load->helper('auth_helper');
        $this->load->helper('http_helper');

        // Set the valid date range of the request
        // to within 15 minutes of the server time.
        $this->valid_date_range_in_seconds = 900;
    }

    public function index()
    {



        // Get the requested method.
        $this->method = request_method();

        // Check to make sure the authentication credentials
        // are valid.
        $this->checkAuth($this->method);

        // Detect the requested format.
        $request_format = request_format();
        $this->format = detect_format($request_format,
                                      $this->supported_formats,
                                      $this->default_format);    

        $this->parameters = $this->input->get();

        switch($this->method) {
            case 'get':
                $this->get();
                break;
                        case 'post':
                    $this->post();
                    break;


            default;
                $error_code = "404";
                $error_message = $error_code;
                $error_message .= " Unsupported method: ";
                $error_message .= $this->method;
                show_error($error_message, $error_code);
                break;
        }
    }

    // HTTP GET is supported by this REST service class
    // and must be implemented in child classes.
    abstract protected function get();

    private function checkAuth($method) {

        $auth = "";
        if($this->input->server('HTTP_X_AUTHORIZATION'))
        {
            $auth = $this->input->server('HTTP_X_AUTHORIZATION');
        }

        $request_date = "";
        if($this->input->server('HTTP_DATE'))
        {
            $request_date = $this->input->server('HTTP_DATE');
        }

        $query_string = "";
        if($this->input->server('QUERY_STRING'))
        {
            $query_string = $this->input->server('QUERY_STRING');
        }

        if (empty($request_date)
            || !$this->checkDate($request_date)) {
            $error_code = "403";
            $error_message = $error_code . " Date is invalid";
            show_error($error_message, $error_code);
            exit;
        }

        if (empty($auth)
            || !isAuthorized($auth, $request_date,
                             $method, $query_string)) {
            $error_code = "401";
            $error_message = $error_code . " Unauthorized";
            show_error($error_message, $error_code);
            exit;
        }
    }

    private function checkDate($request_date)
    {
        if (!preg_match("/GMT/i", $request_date)
            && !preg_match("/UTC/i", $request_date)
            && !preg_match("/Z/i", $request_date))
        {
            $request_date .= " UTC";
        }
        $ts_req = strtotime($request_date);
        $ts_server = (int)gmdate('U');

        $valid_date_range = $this->valid_date_range_in_seconds;
        if ( ($ts_req > $ts_server - $valid_date_range)
             && ($ts_req < $ts_server + $valid_date_range) )
        {
            return true;
        }
        return false;
    }
}

/* End of file rest_service.php */
/* Location: ./application/controllers/rest_service.php */
