<?php 

class LogFileComponent extends Component 
{
    private $requet = 1;
	private $response_success = 2;
	private $response_error = 3;
	private $sys_error = 4;

	// get current plugin name, purpose write log
	public function writeLog($type = "", $data) {
        $message = (is_array($data) || is_object($data)) ? json_encode($data) : $data;
        $format_message = '';
        $prefix = '';
        switch($type){
            case $this->requet:
                $format_message = 'Data: %s';
                $prefix = '----Request---------';
                break;
            case $this->response_success:
                $format_message = 'Data: %s';
                $prefix = '----Response Success';
                break;
            case $this->response_error:
                $format_message = 'Data: %s';
                $prefix = '----Response Error--';
                break;
            case $this->sys_error:
                $format_message = 'ERROR: %s';
                $prefix = '____System Error____';
                break;
        }

        CakeLog::write($prefix, sprintf($format_message, $message));
	}

	public function writeLogStart($url)
	{
		CakeLog::write( '***START***', $url);
	}

	public function writeLogEnd($url)
	{
		CakeLog::write( '*** END ***', $url);
    }
    
    public function get_request(){
        return $this->requet;
    }
    
    public function get_response_success(){
        return $this->response_success;
    }
    
    public function get_response_error(){
        return $this->response_error;
    }
    
    public function get_system_error(){
        return $this->sys_error;
    }
}
?>
