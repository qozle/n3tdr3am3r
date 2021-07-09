<?PHP

namespace Spatie\Crawler\CrawlObservers;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;


//  This is our class that we'll instantiate that has some callback functions
class NetCrawlObserver extends CrawlObserver {
    public $internal_urls = [];

    //  Called when the crawler will crawl the URL (before?).
    public function willCrawl(UriInterface $url):void {
        // echo "We're about to crawl {$url}...\n";
    }


    //  Called when the url is crawled successfully.
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null):void {
        array_push($this->internal_urls, $url);
    }

    
    //  Called when the url fails.
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null):void {
       
    }


    //  Called when the crawling has finished.
    public function finishedCrawling():void {
        
    }
}
?>