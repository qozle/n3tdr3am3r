<?PHP
require_once('simple_html_dom.php');

//  API consumer key- r5wFFzL5IGfqrUp8tmkKYIBsq
//  API consumer secret key- ueuS9k3VygaizceqlH4uhLKIS2W62yJVgGEcqnYxZkahmadzIn

//  access token - 1341698998294503430-R23FNAQjzMrTmwIWf6Oc7bVD4qn2lp
//  access token secret - fWxuBP2ogGzKGalU4LwQJpgstG0WwNHSjn8qQvzSLVRGJ



//  Generate unique nonce for each request
function generate_nonce(){
    //  generate 32 bits of random data 
    $random_bytes = random_bytes(32);
    $random_base64 = base64_encode($random_bytes);
    $random_string = preg_replace('/\W/', '', $random_base64);
    return $random_string;
}


//  Generatore Oauth header string
function generate_oAuth($status, $method = 'POST', $url = 'https://api.twitter.com/1.1/statuses/update.json'){
    
    $secret = 'ueuS9k3VygaizceqlH4uhLKIS2W62yJVgGEcqnYxZkahmadzIn';
    $token_secret = 'fWxuBP2ogGzKGalU4LwQJpgstG0WwNHSjn8qQvzSLVRGJ';
    $signing_key = rawurlencode($secret) . '&' . rawurlencode($token_secret);

    $consumer_key = "r5wFFzL5IGfqrUp8tmkKYIBsq";
    $nonce = generate_nonce();
    $sig_method = "HMAC-SHA1";
    $timestamp = time();
    $token = "1341698998294503430-R23FNAQjzMrTmwIWf6Oc7bVD4qn2lp";
    $version = "1.0";
    
    $base_signature = '';
    
    $oAuth_header_val = 'OAuth ';

    $params = ['oauth_consumer_key'=>$consumer_key, 'oauth_nonce'=>$nonce, 'oauth_signature_method'=>$sig_method, 'oauth_timestamp'=>$timestamp, 'oauth_token'=>$token, 'oauth_version'=>$version, 'status'=>$status];
    $encoded_params = [];
    
    //  % encode each parameter
    foreach($params as $key=>$param){
        $encoded_params[rawurlencode($key)] = rawurlencode($param);
    }
    
    //  sort alphabetically by encoded key
    if(!ksort($encoded_params)){
        return false;
    }

    //  create encoded parameter string
    $encoded_string = '';
    foreach($encoded_params as $key=>$param){
        if($key != array_key_last($encoded_params)){
            $encoded_string .= $key . '=' . $param . "&";
        } else {
            $encoded_string .= $key . '=' . $param;
        }
    }

    //  Create the signature base string
    $base_signature .= strtoupper($method) . '&';
    $base_signature .= rawurlencode($url) . '&';
    $base_signature .= rawurlencode($encoded_string);

    //  Create the signature value
    $calc_signature = hash_hmac('sha1', $base_signature, $signing_key, true);
    $oAuth_signature = base64_encode($calc_signature);
    
    //  remove status from the encoded_params array so we can reuse the array 
    unset($encoded_params['status']);
    //  add the signature to the encoded params array
    $encoded_params['oauth_signature'] =  rawurlencode($oAuth_signature);

    //  Sort it again, becasue something isn't working...
    if(!ksort($encoded_params)){
        return false;
    }

    //  build header string
    foreach($encoded_params as $key=>$param){
        if($key != array_key_last($encoded_params)){
            $oAuth_header_val .= "{$key}=\"{$param}\", ";
        } else {
            $oAuth_header_val .= "{$key}=\"{$param}\"";
        }
    }

    return $oAuth_header_val;
}


//  Send a request
function twitter_request($status, $method = null, $url = null){
    if(is_null($url)){
        if(is_null($method)){
            $auth = generate_oAuth($status);
        } else {
            $auth = generate_oAuth($status, $method);
        }
    } else {
        if(is_null($method)){
            $auth = generate_oAuth($status, 'GET', $url);
        } else {
            $auth = generate_oAuth($status, $method, $url);
        }
    }

    $status = rawurlencode($status);

    $api_url = is_null($url) ? "https://api.twitter.com/1.1/statuses/update.json?status=$status}" : $url;

    $api_method = is_null($method) ? 'POST' : $method;

    echo 'Status: ' . $status . "\n";
    echo 'Method: ' . $api_method . "\n";
    echo 'Url: ' . $api_url . "\n";
    echo 'Auth: ' . $auth . "\n";
    
    
    $curl = curl_init($api_url);  
    $header = ["Authorization: {$auth}"];
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); 
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $api_method);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    // if($api_method == 'POST'){
    //     curl_setopt($curl, CURLOPT_POSTFIELDS, ['status'=>$status]);
    // }
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $resp = curl_exec($curl);
    $respCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE); 
    curl_close($curl);
    
    $json = json_decode($resp, true);

    echo $header[0] . "\n";

    if($respCode == 200){
        return $json;
    } else {
        return [$respCode, $json, $resp];
    }    
}

$resp = twitter_request('this is a test status!');

echo var_dump($resp) . "\n";





  //  Requisite parameters:
    
    //  status (included in URL above)
    //  include_entities (true)
    //  oauth_consumer_key="I476jzAaONMAXRd2yrlTegdGP"
    //  oauth_nonce (32 bytes of random data, base 64 encoded, strip out non-word chars)
    //  oauth_signature
    //  oauth_signature_method - HMAC-SHA1
    //  oauth_timestamp (in ms since epoch)
    //  oauth_token 1341698998294503430-jbIkVmbNxzemsgGBBIML2r6ikLPXHe
    //  oauth_version 1.0
    
    //  all of these are used to calc oauth_signature
    
    
    // token_key=1341698998294503430-jbIkVmbNxzemsgGBBIML2r6ikLPXHe





// $html = file_get_html('https://www.ouiinfrance.com/let-impress-holiday-soup-in-france/');

// $page = $html->find('p');

// $sentence_arr = [];
// foreach($page as $p){
//     $p = $p->plaintext;
//     $p = html_entity_decode($p);
//     $p = preg_replace('/\s+/', ' ', $p);
//     $p = trim($p);
//     //  Totally had to google this, but hey now I know more about positive lookbehinds!
//     //  /(?<!Mr.|Mrs.|Dr.)(?<=[.?!;:])\s+/
//     $p_arr = preg_split('/(?<!Mr.|Mrs.|Dr.)(?<=[.?!;:])\s+/', $p, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
//     foreach($p_arr as $sentence){
//         array_push($sentence_arr, $sentence);
//     }
// }
// //  Pick two random sentences, remove the first one so we don't get doubles.
// $rand_indx1 = array_rand($sentence_arr);
// $sentence1 = $sentence_arr[$rand_indx1];
// array_splice($sentence_arr, $rand_indx1, 1);
// $rand_indx2 = array_rand($sentence_arr);
// $sentence2 = $sentence_arr[$rand_indx2];


?>