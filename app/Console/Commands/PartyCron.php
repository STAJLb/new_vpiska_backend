<?php

namespace App\Console\Commands;

use App\Party;
use Illuminate\Console\Command;
use Carbon\Carbon;

class PartyCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'party:cron';

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
       $parties = Party::all();
       foreach ($parties as $party){
           if($party->date_time < (Carbon::today()->addDays(1))){
                $party->status = 2;
                $party->save();
           }
       }
    }
}
