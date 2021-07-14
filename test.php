<?PHP
require_once('simple_html_dom.php');



function generate_oAuth(){
    $oAuth = "authorization: OAuth ";


}


$curl = curl_init('https://api.twitter.com/1.1/statuses/update.json?status=hello'); 
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
    $headers = [
        'authorization: OAuth'
        'oauth_consumer_key="I476jzAaONMAXRd2yrlTegdGP"',

    ];
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $resp = curl_exec($curl);
    $respCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE); 
curl_close($curl);

$json = json_decode($resp, true);

echo $respCode . "\n";



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