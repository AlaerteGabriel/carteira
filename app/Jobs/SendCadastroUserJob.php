<?php

namespace App\Jobs;

use App\Mail\SendUserEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendCadastroUserJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private $dados)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Mail::to($this->dados['us_email'], $this->dados['us_nome'])->send(new SendUserEmail($this->dados));
    }

    public function failed()
    {
        // Log the failure message
        // Notify the admin of the failure
    }
}
