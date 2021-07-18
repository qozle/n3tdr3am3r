<?PHP
namespace Spatie\Crawler\CrawlObservers;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use \main;
use \get_ai_text;
use \pick_random_sentence;


$debug = false;

//  This is our class that we'll instantiate that has some callback functions
class NetCrawlObserver extends CrawlObserver {
    // public $internal_urls;

    function __construct(){
        $this->internal_urls = [];
    }


    //  Called when the crawler will crawl the URL (before?).
    public function willCrawl(UriInterface $url):void {
        // echo "About to crawl {$url}...\n";
    }


    //  Called when the url is crawled successfully.
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null):void {
        // echo "Crawled {$url}...\n";
        array_push($this->internal_urls, $url);
    }

    
    //  Called when the url fails.
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null):void {
        // echo "\nCouldn't crawl {$url}.\n\n";
    }


    //  Called when the crawling has finished.
    public function finishedCrawling():void {
        global $debug;
        if($debug) echo "Finished crawling the site, these are the internal URLS:\n\n";
        foreach($this->internal_urls as $url){
            if($debug) echo $url . "\n";
        }
        if($debug) echo "\n";
        if(count($this->internal_urls)){
            $random_i = array_rand($this->internal_urls);
            $random_url = $this->internal_urls[$random_i];
            if($debug) echo "We pick {$random_url}\n";
            if($debug) echo "Making a sentence of two randomly selected sentences to feed to the AI.\n";
            $sentence = pick_random_sentence($random_url);
            if($debug) echo "We got:\n";
            if($debug) echo $sentence . "\n";
            if($debug) echo "Feeding sentence to AI...\n";
            $ai_text_raw = json_decode(get_ai_text($sentence), true);
            if($debug) echo "Removing our origial sentence from the result...\n";
            $ai_text = str_replace($sentence, '', $ai_text_raw['output']);
            
            echo $ai_text;
            if($debug) echo "\n";
        } else {
            if($debug) echo "There were no internal URLs, finding another site...\n\n";
            main();
        }
    }
}
?>