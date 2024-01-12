<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use GuzzleHttp\Client;
use LZCompressor\LZString;
class BaseVclaim
{
    private
        $consid,$password,$user_key,$timestamp,$signature,$key,
        $base_uri,$url,$headers,$params,$method,$response,$config,
        $encrypt_method;
    public $error_msg;
    function __construct()
    {
        date_default_timezone_set(env('APP_TIMEZONE_BPJS'));
        $this->base_uri=env('BPJS_URL');
        $this->consid=env('BPJS_CONSID');
        $this->password=env('BPJS_PASSWORD');
        $this->user_key=env('BPJS_USER_KEY');
        $this->timestamp = strval(time()-strtotime('1970-01-01 00:00:00'));
        $this->signature = base64_encode(hash_hmac('sha256', $this->consid."&".$this->timestamp, $this->password, true));
        $this->headers=[
            'Content-Type'=>'Application/x-www-form-urlencoded',
            'Accept' => 'application/json',
            'X-cons-id'=>$this->consid,
            'X-timestamp'=>$this->timestamp,
            'X-signature'=>$this->signature,
            'user_key'=>$this->user_key
        ];
        $this->key=$this->consid.$this->password.$this->timestamp;
        $this->encrypt_method=env('BPJS_ENCRYPT_METHOD');
        date_default_timezone_set(env('APP_TIMEZONE_BPJS'));

    }
    /**
     * url : link api
     * method : get,post, put, delete : string
     * header : header config : array
     * params : data to be sent : array
     */
    function setup($options=[])
    {
        //set url
        if(isset($options['url'])){
            $this->url=$options['url'];
            // $this->url=str_replace(' ','%20',$this->base_uri.DIRECTORY_SEPARATOR.$options['url']);
        }
        //set method
        if(isset($options['method'])){
            $this->method=$options['method'];
        }
        //set header config
        if(isset($options['header'])){
            if(count($options['header'])>0){
                foreach($options['header'] as $k => $v){
                    $this->headers[$k]=$v;
                }
            }
        }
        //set params
        if(isset($options['param'])){
            // $this->config[ strtolower($this->method)=="get" ? 'query' : 'form_params'] = $options['param'];
            $this->config[ strtolower($this->method)=="get" ? 'query' : 'body'] = json_encode($options['param']);
        }
        return $this;
    }
    //set options
    function setOptions()
    {
        $this->config['base_uri']=$this->base_uri;
        $this->config['headers']=$this->headers;
        $this->config['verify']=false;
        $this->config['curl']=[
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false,
            CURLOPT_SSL_CIPHER_LIST=>'DEFAULT:!DH',
        ];
    }
    //set request for api
    function request()
    {
        $client = new Client($this->config);
        $this->response=$client->request($this->method,$this->url);
    }
    //run bridging
    function run()
    {
        $this->setOptions();
        $this->request();
        return $this->validate();
    }
    /** validate bridging result */
    function validate()
    {
        if($this->response->getStatusCode()==200){ //success
            $result=json_decode($this->response->getBody());
            if(isset($result->metaData)){
                if($result->metaData->code==200){
                    return true;
                }else{
                    $this->error_msg='BPJS : '.$result->metaData->message;
                    return false;
                }
            }else{
                $this->error_msg="Tidak ada respon dari BPJS Kesehatan";
                return false;
            }
        }else{ //error
            $this->error_msg=$this->response->getStatusCode()." : ".$this->response->getReasonPhrase();
            return false;
        }
    }
    /** format response */
    function getResponse()
    {
        $result=json_decode($this->response->getBody());
        if(!empty($result->response)){
            return json_decode($this->decompress($result->response));
        }
        return NULL;
    }
    /** decrypt response */
    function decryptResponse($data)
    {
        $key_hash = hex2bin(hash('sha256',$this->key));
        $iv = substr(hex2bin(hash('sha256',$this->key)), 0, 16);
        return openssl_decrypt(base64_decode($data), $this->encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
    }
    /** decompress response */
    function decompress($data)
    {
        $data=$this->decryptResponse($data);
        return LZString::decompressFromEncodedURIComponent($data);
    }
}