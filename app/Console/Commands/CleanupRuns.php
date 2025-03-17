<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Run;
use Carbon\Carbon;

class CleanupRuns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:cleanup-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old and abandoned runs';

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

        $oldRuns = Run::where('updated_at', '<', Carbon::now()->subDays(5))->get();
	$this->info($oldRuns->count().' old runs found');
	foreach ($oldRuns as $run) {
		$this->info('Deleting run '.$run->dir);
		$run->delete();
	}

	$this->info("Now looking for abandonded run dirs");
	$runDirs=glob('storage/runs/*',GLOB_ONLYDIR);
	$runDirBaseNames = array_map('basename', $runDirs);
	$nonPermanentRuns=array_filter($runDirBaseNames,function($v){return preg_match('/^\d+_?\d*$/',$v);});
	$inDbRuns=Run::all()->pluck('dir')->toArray();
	$abandonedRunDirs=array_diff($nonPermanentRuns, Run::all()->pluck('dir')->toArray());

	$this->info(count($abandonedRunDirs)." abandoned run dirs found");
	foreach ($abandonedRunDirs as $dir) {
		$fullDir = storage_path()."/runs/$dir";
		$this->info("Deleting rundir $fullDir");
		try {
			if(!\File::deleteDirectory($fullDir)){
				$this->error("Error deleting runDir, check permissions");
			}
		} catch (\Exception $e) {
			$this->error("Error deleting runDir");
		}
	}

	$this->info("Now looking for abandoned data dirs");
	$dataDirBaseNames=array_map('basename',glob('storage/data/*'));
	$nonPermanentDataDirNames=array_filter($dataDirBaseNames,function($v){return preg_match('/^\d+_?\d*$/',$v);});
	$dataLinks=glob('storage/runs/*/workingDir/Data');
	
	$usedLinkNames=array_map(function($v){return basename(realpath($v));},$dataLinks);
	$abandonedDataDirs=array_diff($nonPermanentDataDirNames, $usedLinkNames);

	foreach ($abandonedDataDirs as $dir) {
		$fullDir = storage_path()."/data/$dir";
		$this->info("Deleting datadir $fullDir");
		\File::deleteDirectory($fullDir);	
	}






    }
}
