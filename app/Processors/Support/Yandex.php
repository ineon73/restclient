<?php
    namespace App\Processors\Support;
    

  class Yandex {
    public $api_key=CONFIG::YANDEX_API_KEY;

    public function translate($text) {
      
        if (CONFIG::DEBUG) {
            echo '<pre> yandex api qtext';
            print_r($text);
            echo '</pre>';
        }
      
      $post_data= 'text='.$text;//[   'text'=>$text ];
      $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key='.$this->api_key.'&lang=en-ru';

      //$response = shell_exec("/usr/bin/curl -L $url);

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $headers = array();
      $headers[] = 'Accept: */*';
      $headers[] = 'Content-Type: application/x-www-form-urlencoded';

      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);




      //curl_setopt($ch,CURLOPT_HTTPHEADER,array("Expect:  "));
      $server_output = curl_exec ($ch);
      if ($server_output!==FALSE) {
        //var_dump(curl_getinfo($ch));
        return ['result'=>json_decode($server_output,1),'error'=>''];
      } else {
        return ['result'=>FALSE,'error'=>curl_error($ch)];
      }
        curl_close ($ch);
    }
  }