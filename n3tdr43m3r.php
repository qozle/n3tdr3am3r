<?PHP
require_once('vendor/autoload.php');
require_once('crawler-config-class.php');
require_once('simple_html_dom.php');

use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlObservers\NetCrawlObserver;

//  Net_dreamer.php

//  IP range is from 0-255
//  Might want to consider lookin for Ipv4 / Ipv6
function generate_url(){
    $octet_array = [];
    $ip = "http://";
    for($i = 0; $i < 4; $i++){
        $ip .= rand(0, 255);
    }
    return $ip;    
}


//  Keep looking at random IPs until we get one that gives a valid response
function find_url(){
    $url = generate_url();
    
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $url);  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); 
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    $str = curl_exec($curl);
    $respCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE); 
    curl_close($curl);

    if($respcode > 200 && $respCode < 300){
        return $url;
    } else {
        return find_url();
    }
}


//  Pick a random IP address, see if we get data from it.  
$valid_ip = find_url();



//  Crawl the site, build a list of all internal URLs, pick a random one.
$netCrawlObserver = new NetCrawlObserver();
Crawler::create()
    ->setCrawlObserver($netCrawlObserver)
    ->setCrawlProfile(new \Spatie\Crawler\CrawlProfiles\CrawlInternalUrls($valid_ip))
    ->startCrawling($valid_ip);




//  Pull text from the chosen website.  Format it such that there's only text.

//  Feed it to the AI.  

//  Get what it spits out, format it so there's only text.

//  Post it to twitter.  

//  Make an API?  So on get requests, the page serves a random internet dream.
?>