<?php

namespace App\Console\Commands;

use App\Party;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ParserEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parserEvents:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = "https://kudago.com/public-api/v1.4/events/?expand=place,location,description,short_title,dates,site_url&fields=place,location,description,short_title,dates,site_url,categories&location=spb&page_size=150&order_by=-id&text_format=text";

        $json = json_decode(file_get_contents($url), true);
        foreach ($json['results'] as $result) {

            if (is_null(Party::where('title_party', $result['short_title'])->first())) {
                if ($result['place']['address'] != null) {
                    foreach ($result['categories'] as $category) {
                        if ($category == 'ball' || $category == 'discount' || $category == 'holiday' || $category == 'magic' || $category == 'night' || $category == 'open' || $category == 'party'
                            || $category == 'permanent-exhibitions' || $category == 'quest' || $category == 'sale' || $category == 'shopping'
                            || $category == 'speed-dating' || $category == 'slug' || $category == 'yarmarki-razvlecheniya-yarmarki') {
                            $type = 'walk';
                        } else if ($category == 'education' || $category == 'global' || $category == 'business-events' || $category == 'meeting' || $category == 'romance'
                          ) {
                            $type = 'tea';
                        }else if ($category == 'cinema' || $category == 'circus' || $category == 'comedy-club' || $category == 'concert' || $category == 'kvn' || $category == 'show' || $category == 'theater' ) {
                            $type = 'film';
                        } else if ($category == 'dance-trainings' || $category == 'games' || $category == 'kids' || $category == 'masquerade' || $category == 'sport' || $category == 'stand-up') {
                            $type = 'game';
                        } else if ($category == 'photo' || $category == 'presentation' ) {
                            $type = 'picture';
                            } else {
                            $type = 'tea';
                        }
                    }
                    $party = new Party();
                    $party->created_id = 58;
                    $party->title_party = $result['short_title'];
                    $party->description_party = $result['description'];
                    $party->created_name = "KUDA.GO";
                    $party->address = $result['place']['address'];
                    $party->coordinates = $result['place']['coords']['lat'] . ' ' . $result['place']['coords']['lon'];
                    $party->max_count_people = 12;
                    $party->alcohol = 0;
                    $party->date_time = $result['dates'][0]['start_date'] . ' ' . $result['dates'][0]['start_time'];
                    $party->type = $type;
                    $party->source_id = $result['place']['id'];
                    $party->source_url = $result['site_url'];
                    $party->save();
                }
            }


        }
    }
}
