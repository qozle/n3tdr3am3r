<?PHP
require_once('vendor/autoload.php');
require_once('lib/lib.php');



$debug = false;

//  Generate a random word.
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