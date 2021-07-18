<?PHP
require_once('simple_html_dom.php');
require_once('n3tdr34m3r-crawler.php');

use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlObservers\NetCrawlObserver;

function load_vocab(){
    //  Read each of the parts of speech files, make an array of each.
    $random_lists = [];
    $random_lists['articles'] = ['the', 'a'];
    $dir = opendir('random-words');
    while(false !== $entry = readdir($dir)){
        if($entry != '.' && $entry != '..'){
            $fileName = "random-words/{$entry}";
            $file = fopen($fileName, 'r');
            switch($entry){
                case 'adjectives.txt':
                    $text = fread($file, filesize($fileName));
                    $words = explode("\r\n", $text);
                    $random_word = $words[array_rand($words)];
                    $random_lists['adjectives'] = $words;
                    fclose($file);
                    break;
                case 'adverbs.txt':
                    $text = fread($file, filesize($fileName));
                    $words = explode("\r\n", $text);
                    $random_lists['adverbs'] = $words;
                    fclose($file);
                    break;
                case 'nouns.txt':
                    $text = fread($file, filesize($fileName));
                    $words = explode("\r\n", $text);
                    $random_lists['nouns'] = $words;
                    fclose($file);
                    break;
                case 'prepositions.txt':
                    $text = fread($file, filesize($fileName));
                    $words = explode("\r\n", $text);
                    $random_lists['prepositions'] = $words;
                    fclose($file);
                    break;
                case 'verbs.txt':
                    $text = fread($file, filesize($fileName));
                    $words = explode("\r\n", $text);
                    $random_lists['verbs'] = $words;
                    fclose($file);
                    break;
            }
        }
    }
    return $random_lists;
}

$random_lists = load_vocab();

//  functions for getting random parts of speech
function art($int = 1){
    global $random_lists;
    $art_array = [];
    $arts = $random_lists['articles'];
    for($i = 0; $i < $int; $i++){
        array_push($art_array, $arts[array_rand($arts)]);
    }
    return $art_array;
}


function adj($int = 1){
    global $random_lists;
    $adj_array = [];
    $adjs = $random_lists['adjectives'];
    for($i = 0; $i < $int; $i++){
        array_push($adj_array, $adjs[array_rand($adjs)]);
    }
    return $adj_array;
}


function adv($int = 1){
    global $random_lists;
    $adv_array = [];
    $advs = $random_lists['adverbs'];
    for($i = 0; $i < $int; $i++){
        array_push($adv_array, $advs[array_rand($advs)]);
    }
    return $adv_array;
}


function noun($int = 1){
    global $random_lists;
    $noun_array = [];
    $nouns = $random_lists['nouns'];
    for($i = 0; $i < $int; $i++){
        array_push($noun_array, $nouns[array_rand($nouns)]);
    }
    return $noun_array;
}


function prep($int = 1){
    global $random_lists;
    $prep_array = [];
    $preps = $random_lists['prepositions'];
    for($i = 0; $i < $int; $i++){
        array_push($prep_array, $preps[array_rand($preps)]);
    }
    return $prep_array;
}


function verb($int = 1){
    global $random_lists;
    $verb_array = [];
    $verbs = $random_lists['verbs'];
    for($i = 0; $i < $int; $i++){
        array_push($verb_array, $verbs[array_rand($verbs)]);
    }
    return $verb_array;
}



//  build a random sentence
function build_sentence(){
    $rand_case = rand(0, 5);
    $random_sentence;
    switch($rand_case){
        case 0:
            $arts = art(2);
            $adjs = adj(2);
            $nouns = noun(2);
            $verbs = verb(1);
            $adv = adv(1);
            $random_sentence = "{$arts[0]} {$adjs[0]} {$nouns[0]} {$adv[0]} {$verbs[0]} {$arts[1]} {$adjs[1]} {$nouns[1]}";
            break;        
        case 1:
            $random_sentence = noun()[0];
            break;        
        case 2:
            $random_sentence = adj()[0] . " " . noun()[0] . " .";
            break;            
        case 3:
            $random_sentence = verb()[0] . " " . adv()[0];
            break;
        case 4:
            $random_sentence = art()[0] . " " . adj()[0] . " " . noun()[0] . " " . verb()[0];
            break;
        case 5:
            $random_sentence = adv()[0] . ' ' . verb()[0];
            break;
    }
    return $random_sentence;
}



function crawl($url){
    //  Pick a random IP address, see if we get data from it.  
    $netCrawlObserver = new NetCrawlObserver();
    
    //  Crawl the site, build a list of all internal URLs, pick a random one.
    Crawler::create()
    ->setCrawlObserver($netCrawlObserver)
    ->setCrawlProfile(new \Spatie\Crawler\CrawlProfiles\CrawlInternalUrls($url))
    ->setTotalCrawlLimit(250)
    ->startCrawling($url);
}


function get_ai_text($text){
    global $debug;
    $text = $text;
    $headers = ['api-key:4fe904a0-f080-4745-814f-5874275dc1d6'];
    $curl = curl_init("https://api.deepai.org/api/text-generator"); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, ['text'=>$text]);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3); 
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        $resp = curl_exec($curl);
        $respCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE); 
    curl_close($curl);

    if($respCode == 200){
        return $resp;
    } else {
        if($debug) echo "We got an error when trying to get the AI text: {$respCode}\n";
    }
}


function search($text){
    global $debug;
    $param = $text;
    $q = "https://api.goog.io/v1/search/q=" . $param;
    $curl = curl_init($q); 
        $headers = ['Accept: application/json', 'apikey: a0d8c6a5-d914-4244-8803-696d07250d05'];
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $resp = curl_exec($curl);
        $respCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE); 
        $error = curl_error($curl);
    curl_close($curl);
    
    if($respCode == 200){
        $json = json_decode($resp, true);
        return $json;
    } else {
        if($debug) echo "Got {$respCode} from google search:\n\n";
    }
}


function get_random_link($query){
    $search_result = search($query);
    
    $links = [];
    foreach($search_result['results'] as $result){
        array_push($links, $result['link']);
    }
    
    $random_link = $links[array_rand($links)];
    
    return $random_link;
    
}


function pick_random_sentence($link){
    global $debug;
    $html = file_get_html('https://www.ouiinfrance.com/let-impress-holiday-soup-in-france/');

    $page = $html->find('p');

    $sentence_arr = [];
    foreach($page as $p){
        $p = $p->plaintext;
        $p = html_entity_decode($p);
        $p = preg_replace('/\s+/', ' ', $p);
        $p = trim($p);
        //  Totally had to google this, but hey now I know more about positive lookbehinds!
        //  /(?<!Mr.|Mrs.|Dr.)(?<=[.?!;:])\s+/
        $p_arr = preg_split('/(?<!Mr.|Mrs.|Dr.)(?<=[.?!;:])\s+/', $p, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach($p_arr as $sentence){
            array_push($sentence_arr, $sentence);
        }
    }
    //  Pick two random sentences, remove the first one so we don't get doubles.
    $rand_indx1 = array_rand($sentence_arr);
    $sentence1 = $sentence_arr[$rand_indx1];
    array_splice($sentence_arr, $rand_indx1, 1);
    $rand_indx2 = array_rand($sentence_arr);
    $sentence2 = $sentence_arr[$rand_indx2];
    if($debug) echo "\nSentence 1: {$sentence1} \n\n";
    if($debug) echo "Sentence 2: {$sentence2} \n\n";
    $sentence = $sentence1 . ' ' . $sentence2;
    return $sentence;
}

?>