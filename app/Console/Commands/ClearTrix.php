<?php
namespace App\Console\Commands;

use App\TrixImage;
use Workerman\Worker;
use Workerman\Lib\Timer;
use Workerman\Autoloader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ClearTrix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trix:clear {action} {--d|daemon}';

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
        global $argv;
        $action = $this->argument('action');
        $argv[1] = $action;
        $argv[2] = $this->option('daemon') ? '-d' : '';

        if ($action === 'start') {
            $this->setWorker(4);
        }

        Worker::runAll();
    }

    protected function setWorker($workerCount = 1)
    {
        global $worker;

        $worker = new Worker();

        $worker->count = $workerCount;

        $worker->name = 'trix_clear_worker';

        $worker->onWorkerStart = [$this, 'onWorkerStart'];
    }

    public function onWorkerStart($worker)
    {
        echo $this->setMessage($worker, "開始刪除待刪圖片");

        Timer::add(1, function () use($worker) {

            if (Carbon::now()->hour < 12) {
                echo $this->setMessage($worker, "尚未超過當天中午1200, 所以不執行");
            }

            $date = Carbon::now()->subDay()->toDateString();

            $cacheKey = TrixImage::rpop($date);

            if (! $cacheKey) {

                if ($worker->id === 0) {
                    echo $this->setMessage($worker, '已無待刪圖片需要處理, 工作結束');
                    $this->call('trix:clear', ['action' => 'stop']);
                    return;
                }

                return;
            }

            if (! TrixImage::exists($cacheKey)) return;

            $filePath = TrixImage::get($cacheKey);

            Storage::disk('public')->delete($filePath);
            TrixImage::delete($cacheKey);
            echo $this->setMessage($worker, "$filePath:圖片已被刪除");
        });
    }

    protected function setMessage($worker, $message)
    {
        return "$worker->name: $message" . PHP_EOL;
    }
}
