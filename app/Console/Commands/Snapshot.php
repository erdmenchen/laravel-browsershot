<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\File;
use Spatie\Image\Manipulations;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Console\Helper\ProgressBar;

class Snapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:take';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take screenshot of given websites from snapshot.json config file';

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
            
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'];
            
            try {
                Browsershot::url($url)
                    ->windowSize(2560, 1298)
                    ->fit(Manipulations::FIT_CONTAIN, 3840, 1947)                
                    ->setDelay(40000)
                    ->dismissDialogs()
                    ->save('output/' . $name . '_' . $timestamp . '_'. $domain . '_screenshot.png');
            } catch (Symfony\Component\Process\Exception\ProcessTimedOutException $th) {
                $bar->setMessage('Processing ' . $name . ' Failed. Retrying - URL: ' . $url);
                Browsershot::url($url)
                    ->windowSize(2560, 1298)
                    ->fit(Manipulations::FIT_CONTAIN, 3840, 1947)                
                    ->setDelay(40000)
                    ->dismissDialogs()
                    ->save('output/' . $name . '_' . $timestamp . '_'. $domain . '_screenshot.png');
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
