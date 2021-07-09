<?PHP

namespace Spatie\Crawler\CrawlObservers;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;


//  This is our class that we'll instantiate that has some callback functions
class NetCrawlObserver extends CrawlObserver {
    public $requestsFailed = [];

    //  Called when the crawler will crawl the URL (before?).
    public function willCrawl(UriInterface $url):void {
        // echo "We're about to crawl {$url}...\n";
    }


    //  Called when the url is crawled successfully.
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null):void {
        try{
            //  Make sure that simpleDOMParser can actually get the HTML.  If not, we get it ourselves.
            if(!$html = file_get_html($url)){
                $curl = curl_init(); 
                curl_setopt($curl, CURLOPT_URL, $url);  
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); 
                curl_setopt($curl, CURLOPT_HEADER, 1);
                curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
                $str = curl_exec($curl);
                $respCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE); 
                curl_close($curl);  
                
                //  If we got the HTML ourselves, just use that.
                if($respCode <= 200){
                    $html= str_get_html($str); 
                } else if($respCode == 301 || $respCode == 302) {
                    //  If the resource is permanently moved, just report it and continue.
                    list($header) = explode("\r\n\r\n", $html, 2);
                    $matches = array();
                    preg_match("/(Location:|URI:)[^(\n)]*/", $header, $matches);
                    $redirect_url = trim(str_replace($matches[1],"",$matches[0]));
                    $url_parsed = parse_url($redirect_url);
                    array_push($this->requestsFailed, [$url, "Got HTTP response code {$respCode}, moved to {$redirect_url}."]);
                    return;
                } else {
                    array_push($this->requestsFailed, [$url, "Got HTTP response code {$respCode}."]);
                    return;
                }
            }
            //  If there's no title, we don't want it.
            if(isset($html->find('title')[0])){
                $html->find('title')[0]->innertext();
            } else {
                return;
            }
            
            //  This is redundant- Crawler is set to only crawl internal pages.
            if(preg_match('/^http(s)*:\/\/(www\.)*minutemanpress.com/', $url)){
                $this->writeToCsv([$title, $url]);
                echo "Wrote:\n";
                echo "{$title}\n";
                echo "{$url}.\n\n";
            } else {
                echo "Didn't write:\n";
                echo "{$title}\n";
                echo "{$url}\n\n";
            }
            //  I don't think this serves any purpose...
        } catch (Exception $e){
            echo "We had an error:\n";
            echo $e->getMessage();
            echo "\n\n\n";
            die();
        }
    }

    
    //  Called when the url fails.
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null):void {
        if(preg_match('/^http(s)*:\/\/(www\.)*minutemanpress.com/', $url)){
            //  Double check that it's an internal link.  If so, for one reason or another (>_<!),
            //  we can't crawl the page.
            array_push($this->requestsFailed, [$url, "Not sure."]);
            echo "\n\n\n";
            echo var_dump($requestException) . "\n\n\n";
            echo $requestException->getMessage() . "\n\n";
            if($requestException->getPrevious()){
                echo $requestException->getPrevious()->getMessage() . "\n\n";
            }
            echo "\n\n\n";
        } else {
            //  This shouldn't go off because we're only crawling internal links anyways.
            echo "We got an error but it wasn't from a MMP site so who cares.\n\n\n";
            echo "URL: {$url}\n\n";
            $miscErrors = fopen('other_errors.txt', 'w+');
            fwrite($miscErrors, "{$url}\n");
            fclose($miscErrors);
        }
    }


    //  Called when the crawling has finished.
    public function finishedCrawling():void {
        echo "\n\n\n\nWe finished crawling bruh bruh bruh bruhhh...\n\n\n\n\n\n";
        echo var_dump($this->requestsFailed);
        $errorFile = fopen('did-not-crawl.csv', 'w+');
        fputcsv($errorFile, ['URL', 'Description of error']);
        foreach($this->requestsFailed as $field){
            fputcsv($errorFile, $field);
        }
        fclose($errorFile);
    }
}
?>