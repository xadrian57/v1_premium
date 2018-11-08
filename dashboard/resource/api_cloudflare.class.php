<?php

class cloudflare_api
{    
    const TIMEOUT = 5;
    
    private $URL = 'https://api.cloudflare.com/client/v4/';
    
    
    private $auth_email;
    private $auth_key;
    
    public function __construct()
    {
        
        $num_args = func_num_args();
        if ($num_args == 2){
            $parameters = func_get_args();
            $this->auth_email = $parameters[0];
            $this->auth_key = $parameters[1];
        }else{
            //erro
        }
        
    }

    public function identificador($domain){
        $result = $this->getZona($domain);
        if (isset($result->result) && count($result->result) == 1)
           return $result->result[0]->id;
       return false;
    }


    public function purgeAll($identificador){
        $data = [
            'purge_everything' =>  true
        ];
        return $this->http_request('zones/'.$identificador.'/purge_cache',$data,'delete');
    }

    public function purgeArquivos($identificador, $files = []){
        $data = array(
            'files' => $files
        );

        return $this->http_request('zones/'.$identificador.'/purge_cache',$data,'delete');
    }

    public function getZona($nome){
        $data = [
            'name'      => $nome,
            'status'    => 'active',
            'page'      => 1,
            'match'     => 'all'
        ];
        return $this->get('zones',$data);
    }

    public function getZonas(){
        return $this->get('zones',[]);
    }

    private function get($endpoint,$data){
        return $this->http_request($endpoint,$data,'get');
    }

    /**
    * Handle http request to cloudflare server
    */
    private function http_request($endpoint,$data, $method)
    {
        //setup url
        $url = $this->URL.$endpoint;
        
        //echo $url;exit;
        
        //headers set
        $headers = ["X-Auth-Email: {$this->auth_email}", "X-Auth-Key: {$this->auth_key}"];
        $headers[] = 'Content-type: application/json';
        
        //json encode data
        $json_data = json_encode($data);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        if ($method == 'delete')
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        if (!isset($method) || $method == 'get')
            $url .= '?'.http_build_query($data);
        else
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        //add headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        
        
        $http_response = curl_exec($ch);
        $error       = curl_error($ch);
        $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
       
        if ($http_code != 200) {
            //hit error will add in error checking but for now will return back to user to handle
            return json_decode($http_response);
        } else {
            return json_decode($http_response);
        }
    }
}