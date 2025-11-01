<?php
namespace App\Jobs;
use Illuminate\Bus\Queueable;
class SampleJob {
    use Queueable;
    public function handle() {
        // job work
    }
}
