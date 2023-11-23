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

        $poemLinks = $poetCrawler->filter('.content .record .col-sm-12.col-md a');

        $poemUrls = [];
        foreach ($poemLinks as $poemLink) {
            $poemLinkCrawler = new Crawler($poemLink);
            $poemUrl = 'https://www.aldiwan.net/' . $poemLinkCrawler->attr('href');
            $poemUrls[] = $poemUrl;
        }

        foreach ($poemUrls as $poemUrl)
        {
            $poem = $this->crawlPoemCouplets($poemUrl, $poet);
        }
    }

    protected function crawlPoemCouplets($poemUrl, $poet)
    {
        dd($poemUrl);
        $response = Http::get($poemUrl);
        $crawler = new Crawler($response->body());

        // Check if the nodes exist before trying to extract text
        $poemTitleNode = $crawler->filter('.content .record .col-sm-12.col-md a');

        // Extract other relevant information about the poem...

        $poemTitle = $poemTitleNode->text();

        $this->info('Poem Title: ' . $poemTitle);

        // Create or retrieve poem information
        $poem = Poem::firstOrCreate([
            'title' => $poemTitle,
            'poet_id' => $poet->id,
        ]);

        // Now crawl and store couplets...
    }
}
