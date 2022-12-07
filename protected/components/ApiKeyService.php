<?php

class ApiKeyService {

    const CURRENT_YEAR = 2022;

    private function __construct() {}

    public static function getCurrentYear(){
        return self::CURRENT_YEAR;
    }

    public static function ipApi(){
        //return '8a2a08ce43e0.sn.mynetname.net:8003/';
        //return '195.69.204.200:10018/';
//        return '192.168.3.57:10018/';
        return 'ecabapi.omgtu.ru:10018/';
    }

    public static function queryApi($typeOperation, $params, $keyApi = '', $method = 'POST', $unicode = false){

        if($unicode == true) {
            $postParams = json_encode($params, JSON_UNESCAPED_UNICODE);
        }else{
            $postParams = json_encode($params);
        }

        $query = curl_init();
        curl_setopt($query, CURLOPT_URL, self::ipApi().$typeOperation);
        curl_setopt($query, CURLOPT_RETURNTRANSFER, true);
        if($method == 'POST') {
            curl_setopt($query, CURLOPT_POST, true);
            curl_setopt($query, CURLOPT_POSTFIELDS, $postParams);
        }elseif ($method == 'PUT'){
            curl_setopt($query, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($query, CURLOPT_POSTFIELDS, $postParams);
        }elseif ($method == 'PUT2'){
            curl_setopt($query, CURLOPT_CUSTOMREQUEST, "PUT");
            if(!empty($params)) {
                curl_setopt($query, CURLOPT_URL, self::ipApi() . $typeOperation . '?' . http_build_query($params));
            }
        }elseif ($method == 'DELETE'){
            curl_setopt($query, CURLOPT_CUSTOMREQUEST, "DELETE");
            if(!empty($params)) {
                curl_setopt($query, CURLOPT_URL, self::ipApi() . $typeOperation . '?' . http_build_query($params));
            }
        }elseif ($method == 'GET'){
            if(!empty($params)) {
                curl_setopt($query, CURLOPT_URL, self::ipApi() . $typeOperation . '?' . http_build_query($params));
            }
        }
        $fnpp = Yii::app()->user->getFnpp();
        curl_setopt($query, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'up.omgtu.ru: '.$keyApi,
            'fnpp: '.$fnpp, 'type: portal') );
        curl_setopt($query, CURLOPT_TIMEOUT, 40);
        $reply = curl_exec($query);
        $code = curl_getinfo($query, CURLINFO_HTTP_CODE);
        curl_close($query);

        $result = json_decode($reply,true);

        return ['code' => $code, 'json_data' => $result];
    }


    public static function checkResponseApi($data, $typeOperation){
        $thisAction = new Controller('Api');
        $code = $data['code'];
        $json_data = $data['json_data'];
        //var_dump($code);
        if(in_array($code, [0,400,401,402,403,404,480])){
            $thisAction->render('//admin/apikeys/apiKeyError', ['code' => $code, 'text' => $json_data, 'typeOperation' => $typeOperation ]);
            die;
        }
    }
}