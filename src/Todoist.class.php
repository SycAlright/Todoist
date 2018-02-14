<?php
/**
* Class-Todoist
* Todoist Developer REST-API
* Authorization: Token
*
* Author: Syc <syc@bilibili.de>
* Version: 20180212
* GNU General Public License V3.0
*
*/

class Todoist
{
    public $type, $id;
    private static $api = 'https://beta.todoist.com/API/v8/';
    private static $token, $enable_cache, $cache_dir, $enable_log, $log_dir;

    /**
     * Todoist constructor.
     * @param string $_token
     * @param bool $_enable_cache
     * @param string $_cache_dir
     * @param bool $_enable_log
     * @param string $_log_dir
     */
    public function __construct($_token, $_enable_cache = false, $_cache_dir = __DIR__ . '/data.json', $_enable_log = false, $_log_dir = __DIR__ . '/work.log')
    {
        if($_token == null)
        {
            exit("Token Error");
        }
        self::$token = $_token;
        self::$enable_cache = $_enable_log;
        self::$cache_dir = $_cache_dir;
        self::$enable_log = $_enable_log;
        self::$log_dir = $_log_dir;
    }

    /**
     * Get action
     * @return string $body
     */
    public function get()
    {
        $body = $this->curl_get($this->type, $this->id, self::$token);
        return $body;
    }

    /**
     * Delete action
     * @return string $body
     */
    public function delete()
    {
        if($this->id == null)
        {
            exit("Cannot delete all projects or tasks");
        }
        $body = $this->curl_delete($this->type, $this->id, self::$token);
        if($body == 204)
        {
            return('Success Delete');
        }
        return("Error: " . $body);
    }

    /**
     * Update action
     * @param string $_post
     * @return string $body
     */
    public function update($_post = array())
    {
        if($this->id == null)
        {
            exit("Cannot update all projects or tasks");
        }
        $body = $this->curl_update($this->type, $this->id, self::$token, $_post);
        if($body == 204)
        {
            return('Success Update');
        }
        return("Error: " . $body);
    }

    /**
     * Create action
     * @param string $_post
     * @return string $body
     */
    public function create($_post = array())
    {
        if($this->id !== null)
        {
            exit("Cannot specify ID");
        }
        $body = $this->curl_post($this->type, $this->id, self::$token, $_post);
        return $body;
    }

    /**
     * Close action
     * @return string $body
     */
    public function close()
    {
        if($this->type !== 'tasks')
        {
            exit("Only close task");
        }
        if($this->id == null)
        {
            exit("Not empty ID");
        }
        $body = $this->curl_put($this->type, $this->id, self::$token, 1);
        if($body == 204)
        {
            return('Success Close');
        }
        return("Error: " . $body);
    }

    /**
     * Reopen action
     * @return string $body
     */
    public function reopen()
    {
        if($this->type !== 'tasks')
        {
            exit("Only reopen task");
        }
        if($this->id == null)
        {
            exit("Not empty ID");
        }
        $body = $this->curl_put($this->type, $this->id, self::$token, 2);
        if($body == 204)
        {
            return('Success Reopen');
        }
        return("Error: " . $body);
    }

    /**
     * Project
     * @param string $_pid
     */
    public function project($_pid = '')
    {
        if($_pid != '')
        {
            $this->type = 'projects';
            $this->id = $_pid;
            
        }else{
            $this->type = 'projects';
            $this->id = null;
        }
        return $this;
    }

    /**
     * Task
     * @param string $_tid
     */
    public function task($_tid = '')
    {
        if($_tid != '')
        {
            $this->type = 'tasks';
            $this->id = $_tid;
        }else{
            $this->type = 'tasks';
            $this->id = null;
        }
        return $this;
    }

    /**
     * Comment
     * @param string $_tid
     */
    public function comment($_tid = '')
    {
        if($_tid != '')
        {
            $this->type = 'comments';
            $this->id = $_tid;
        }else{
            $this->type = 'comments';
        }
        return $this;
    }

    /**
     * Label
     * @param string $_tid
     */
    public function label($_lid = '')
    {
        if($_lid != '')
        {
            $this->type = 'labels';
            $this->id = $_lid;
            
        }else{
            $this->type = 'labels';
            $this->id = null;
        }
        return $this;
    }

    /**
     * Curl_Get
     * @param string $_type
     * @param string $_id
     * @param string $_token
     * @return string $data(success) / $http_status(error)
     */
    private function curl_get($_type, $_id, $_token)
    {
        $header = array(
            'Authorization: Bearer ' . $_token,
        );
        if($_id == '')
        {
            $uri = $_type;
        }else{
            $uri = $_type . '/' . $_id;
        }
        var_dump($uri);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$api . $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch,CURLOPT_USERAGENT,"Todoist-PHP-Library_Syc");
        $data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_status == 200)
        {
            return $data;
        }
        return $http_status;
    }

    /**
     * Curl_Delete
     * @param string $_type
     * @param string $_id
     * @param string $_token
     * @return string $data(success) / $http_status(error)
     */
    private function curl_delete($_type, $_id, $_token)
    {
        $header = array(
            'Authorization: Bearer ' . $_token,
        );
        $uri = $this->type . '/' . $this->id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$api . $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch,CURLOPT_USERAGENT,"Todoist-PHP-Library_Syc");
        $data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_status == 200)
        {
            return $data;
        }
        return $http_status;
    }
    
    /**
     * Curl_Post
     * @param string $_type
     * @param string $_id
     * @param string $_token
     * @param array $_post
     * @return string $data(success) / $http_status(error)
     */
    private function curl_post($_type, $_id, $_token, $_post = array())
    {
        $header = array(
            'Authorization: Bearer ' . $_token,
            'Content-Type: application/json',
            'X-Request-Id: ' . uniqid()
        );
        $post = json_encode($_post);
        if($_id == '')
        {
            $uri = $this->type;
        }else{
            $uri = $this->type . '/' . $this->id;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$api . $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); //设置请求体，提交数据包
        curl_setopt($ch,CURLOPT_USERAGENT,"Todoist-PHP-Library_Syc");
        $data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_status == 200)
        {
            return $data;
        }
        return $http_status;
    }
    
    /**
     * Curl_Put
     * @param string $_type
     * @param string $_id
     * @param string $_token
     * @param int $_get (1:close/2:reopen)
     * @return string $data(success) / $http_status(error)
     */
    private function curl_put($_type, $_id, $_token, $_get)
    {
        $header = array(
            'Authorization: Bearer ' . $_token,
        );
        if($_id == '')
        {
            $uri = $this->type;
        }else{
            $uri = $this->type . '/' . $this->id;
        }
        switch ($_get) {
            case 1:
                $uri .= '/close';
                break;
            case 2:
                $uri .= '/reopen';
                break;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$api . $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_USERAGENT,"Todoist-PHP-Library_Syc");
        $data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_status == 200)
        {
            return $data;
        }
        return $http_status;
    }
}