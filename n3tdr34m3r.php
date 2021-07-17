<?PHP
require_once('vendor/autoload.php');
require_once('crawler-config-class.php');
require_once('lib.php');

use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlObservers\NetCrawlObserver;

$debug = false;

//  Generate a random word.

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



// function make_tweet($status){
//     const request_data = {
//         url: "https://api.twitter.com/1.1/statuses/update.json",
//         method: "POST",
//         data: { status: "this is th best test tweet i've ever written" },
//       };
// }





function main(){
    global $debug;
    if($debug) echo "Building search query...\n";
    $sentence = build_sentence();
    if($debug) echo "Getting results for: {$sentence}\n";
    $random_link = get_random_link($sentence);
    if($debug) echo "Crawling {$random_link}\n";
    crawl($random_link);
}



main();



?>