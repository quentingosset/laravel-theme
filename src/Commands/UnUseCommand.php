<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;

class UnUseCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:unuse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forget the used theme with theme:use';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->laravel['themes']->forgetUsed();

        $this->info('Previous theme used successfully forgotten.');
    }
}
