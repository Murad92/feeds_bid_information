<?php

namespace App\Console\Commands;

use App\Feed;
use App\Http\Controllers\GetInfoFromCHController;
use Illuminate\Console\Command;

class AdmavenStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "update-feed-stat {key} {date}";
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update feed statistics keys:
      Admaven 
      Adjux
      Megapush
      Admashin
      AdsCompass
      AdsKeeper
      ZeroPark
      TrafficMedia
      Propeller
      PpcBuzz
      LizardTrack';

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
        $model = new Feed();
        $model->insertData($this->argument('key'),$this->argument('date'));
    }
}
