<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Maintenance extends Component
{
    public $artisanResult = ['title' => '', 'type' => 'notice'];
    public $artisanCommands;
    public $previousCommandId;
    public $code;

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function mount()
    {
        //add you command here
        $this->artisanCommands = [
            'config:clear' => [
                'title' => 'Clear Configuration cache',
                'command' => 'config:cache'
            ],
            'config:cache' => [
                'title' => 'Cache Configs',
                'command' => 'config:cache'
            ],

            'route:clear' => [
                'title' => 'Clearing Route Cache',
                'command' => 'route:clear'
            ],
            'route:cache' => [
                'title' => 'Cache Routes',
                'command' => 'route:cache'
            ],

            'view:clear' => [
                'title' => 'Clear all compiled view files',
                'command' => 'view:clear'
            ],
            'view:cache' => [
                'title' => 'Compile all of the application\'s Blade templates',
                'command' => 'view:cache'
            ],

            'event:clear' => [
                'title' => 'Clear Events Cache',
                'command' => 'event:clear'
            ],
            'event:cache' => [
                'title' => 'Cache Events',
                'command' => 'event:cache'
            ],

            'cache:clear' => [
                'title' => 'Clearing Application Cache',
                'command' => 'cache:clear'
            ],

            'optimize:clear' => [
                'title' => 'Clearing All Cache',
                'command' => 'optimize:clear'
            ],

            'migrate' => [
                'title' => 'Run the database migrations',
                'command' => 'migrate'
            ],

            'redis:clearall' => [
                'title' => 'Clear Redis Cache',
                'php_command' => '\Illuminate\Support\Facades\Cache::flush();'
            ],
        ];

        if ($this->code) {
            $this->run($this->code);
        }
    }

    public function run($id)
    {
        if (!key_exists($id, $this->artisanCommands)) {
            $this->artisanResult = [
                'title' => 'Error. Command not found',
                'type' => 'error'
            ];
        } else {
            $this->previousCommandId = $id;
            if (isset($this->artisanCommands[$id]['command'])) {
                \Artisan::call($this->artisanCommands[$id]['command']);
                $this->artisanResult = [
                    'title' =>
                        '<p><strong>> php artisan '.$this->artisanCommands[$id]['command'].'</strong></p><br />'
                        .'<p>'.implode('</p><p>', explode(PHP_EOL, \Artisan::output())).'</p>',
                    'type' => 'notice'
                ];
            } elseif ($this->artisanCommands[$id]['php_command']) {

                eval($this->artisanCommands[$id]['php_command']);

                $this->artisanResult = [
                    'title' =>
                        '<p><strong>'.$this->artisanCommands[$id]['php_command'].'</strong></p><br />'
                        .'<p>Successful</p>',
                    'type' => 'notice'
                ];
            }
        }
    }

    public function render()
    {
        return view('maintenance.maintenance-livewire', [
            'artisanResult' => $this->artisanResult,
            'artisanCommands' => $this->artisanCommands,
            'previousCommandId' => $this->previousCommandId,
        ]);
    }
}
