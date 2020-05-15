<?php

namespace App\Console\Commands;

use App\AnimGif;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\File;
use Spatie\Image\Manipulations;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Console\Helper\ProgressBar;

class CreateGif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:gif';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make gif from taken screenshots based on snapshot.json in output folder';

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
        //Input
        $content = File::get(base_path() . '/snapshot.json');
        $target_urls = json_decode($content, true);        
        $timestamp = Carbon::now()->format('Y-m-d_H-i');
        
        //Configure ProgressBar
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %percent:3s%% - %elapsed:6s% - %message%');        
        $bar = $this->output->createProgressBar(count($target_urls));
        $bar->setFormat('custom');
        $bar->start();
        $bar->setMessage('Starting');
        
        //Take screenshots
        foreach ($target_urls as $name => $url) {
            $bar->advance();
            $bar->setMessage('Processing ' . $name . ' - URL: ' . $url);  
            
            try {
                $files = glob('output/' . $name . '_2020*');
                
                $anim = new AnimGif();
                $durations = array_fill(0, count($files), 20);// in 1/100s units
                $anim->create($files, $durations);
                $anim->create($files); // default 100ms
                $anim->save('output/' . $name . '_'. $timestamp .'_animated.gif');

            } catch (Symfony\Component\Process\Exception\ProcessTimedOutException $th) {
                $bar->setMessage('Processing ' . $name . ' Failed.');                
            }           
        }
        $bar->finish();
        $this->line('');

        $this->info('Finished.');
        
        /*
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% - %message%');        
        $bar = $this->output->createProgressBar(10);
        $bar->setFormat('custom');
        $bar->start();
        $i = 0;
        while ($i++ < 10) {    
            $bar->advance();
            sleep(1.5);    
            $bar->setMessage('Step ' . $i);
        }
        $bar->finish();
        */

    }
}
