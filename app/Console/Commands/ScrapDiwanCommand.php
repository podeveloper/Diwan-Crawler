<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Poet;
use App\Models\Poem;
use App\Models\Couplet;

class ScrapDiwanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:diwan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scraps al-diwan.net';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting al-diwan.net scraping...');

        // Call the crawlCategories function
        $this->crawlCategories();

        $this->info('Scraping completed.');
    }

    protected function crawlCategories()
    {
        $response = Http::get('https://www.aldiwan.net/');
        $crawler = new Crawler($response->body());

        $faPagelinesDiv = $crawler->filter('.fa-pagelines');
        $categoriesDiv = $faPagelinesDiv->closest('.s-menu');
        $categories = [];

        foreach ($categoriesDiv->filter('.col-6.block-col a') as $category) {
            $categoryCrawler = new Crawler($category);
            $categories[] = [
                'name' => $categoryCrawler->text(),
                'link' => 'https://www.aldiwan.net/' . $categoryCrawler->attr('href'),
            ];
        }

        array_pop($categories);

        $this->info('Found Categories: ' . implode(', ', array_column($categories, 'name')));

        $categoryLinks = [];
        foreach ($categories as $category) {
            $categoryLinks[] = $category['link'];
        }

        foreach ($categoryLinks as $categoryLink)
        {
            $this->crawlPoetsInCategory($categoryLink);
        }
    }

    protected function crawlPoetsInCategory($categoryUrl)
    {
        $response = Http::get($categoryUrl);
        $crawler = new Crawler($response->body());
        $poetCards = $crawler->filter('.s-menu1 .row .col-lg-4');

        $this->info('Crawling poets in category: ' . $categoryUrl);

        $poetUrls = [];
        foreach ($poetCards as $card) {
            $cardCrawler = new Crawler($card);
            $links = $cardCrawler->filter('.col-6.col-md-4 a');

            foreach ($links as $link) {
                $linkCrawler = new Crawler($link);
                $poetUrl = 'https://www.aldiwan.net/'.$linkCrawler->attr('href');
                $poetUrls[] = $poetUrl;
            }

        }

        foreach ($poetUrls as $poetUrl)
        {
            $this->info('Crawling poet: ' . $poetUrl);

            // Create or retrieve poet information
            $poet = $this->crawlPoet($poetUrl);

            // Now crawl poems for the poet
            $this->crawlPoetPoems($poetUrl, $poet);
        }
    }

    protected function crawlPoet($poetUrl)
    {
        $response = Http::get($poetUrl);
        $poetCrawler = new Crawler($response->body());

        $poetNameNode = $poetCrawler->filter('.s-menu1 h2.text-center');
        $eraNode = $poetCrawler->filter('.s-menu1 p.text-center');

        $poetName = $poetNameNode->text();
        $era = $eraNode->text();

        $this->info('Poet Name: ' . $poetName);
        $this->info('Era: ' . $era);

        // Create or retrieve poet information
        return Poet::firstOrCreate([
            'full_name' => $poetName,
            'era' => $era,
        ]);
    }

    protected function crawlPoetPoems($poetUrl, $poet)
    {
        $response = Http::get($poetUrl);
        $poetCrawler = new Crawler($response->body());

        $poemRecords = $poetCrawler->filter('.content .record.col-12');

        foreach ($poemRecords as $poemKey => $poemRecord) {
            $poemRecordCrawler = new Crawler($poemRecord);

            $poemUrl = 'https://www.aldiwan.net/' . $poemRecordCrawler->filter('a.float-right')->attr('href');
            $poemName = $poemRecordCrawler->filter('a.float-right')->first()->text();
            $this->info('Poem URL: ' . $poemUrl);
            $this->info('Poem Name: ' . $poemName);

            try {
                $type = $poemRecordCrawler->filter('.text-data a:nth-child(1)')->first()->text();
                $meter = $poemRecordCrawler->filter('.text-data a:nth-child(2)')->first()->text();
                $coupletCount = (int)$poemRecordCrawler->filter('.text-data a:nth-child(3)')->first()->text();

                $this->info('Type: ' . ($type ?? ''));
                $this->info('Meter: ' . ($meter ?? ''));
                $this->info('Couplet Count: ' . ($coupletCount ?? ''));
            }catch (\Exception $e)
            {
                $type = null; $meter = null; $coupletCount = null;
                $this->info('Failed : ' . $poemName);
            }

            // Create or retrieve poet information
            $poem = Poem::firstOrCreate([
                'number_of_poem' => $poemKey+1,
                'title' => $poemName,
                'type' => $type,
                'couplet_count' => $coupletCount,
                'meter' => $meter,
                'url' => $poemUrl,
                'poet_id' => $poet->id,
            ]);

            // Now crawl and store couplets for the poem
            //$this->crawlPoemCouplets($poem);
        }
    }

    protected function crawlPoemCouplets($poem)
    {
        $response = Http::get($poem->url);
        $crawler = new Crawler($response->body());

        $h3Elements = $crawler->filter('.bet-1.row.pt-0.px-5.pb-4.justify-content-center #poem_content h3');

        // Loop through h3 elements in pairs
        for ($i = 0; $i < $h3Elements->count(); $i += 2) {
            $firstLine = $h3Elements->eq($i)->text();
            $secondLine = $h3Elements->eq($i + 1)->text();

            dd($firstLine,$secondLine);
            // Create a Couplet array
            $couplet = [
                'number_of_couplet' => ($i / 2) + 1,
                'first_line' => $firstLine,
                'second_line' => $secondLine,
                'poem_id' => $poem->id,
            ];

            dd($couplet);

            // Insert the Couplet into the database
            Couplet::create($couplet);
        }

        return $poem;
    }
}
