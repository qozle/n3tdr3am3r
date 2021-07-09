<?PHP
//  Load composer packages
require_once('vendor/autoload.php');
require_once('crawler-config-class.php');
require_once('simple_html_dom.php');

use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlObservers\MmpCrawlObserver;


$mmpCrawlObserver = new MmpCrawlObserver();
$mmpCrawlObserver->openCsvFile('mmp-sitemap.csv', 'w+');
$mmpCrawlObserver->createTitleCols(['Title', 'Url']);
$url = 'https://www.minutemanpress.com';

Crawler::create()
    ->setCrawlObserver($mmpCrawlObserver)
    ->setCrawlProfile(new \Spatie\Crawler\CrawlProfiles\CrawlInternalUrls($url))
    ->startCrawling($url);

?>