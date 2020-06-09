<?php

namespace asgardaec\AsgardsMiddleware\Console\Command;

use asgardaec\AsgardsMiddleware\Models\ApiKey;
use Illuminate\Console\Command;

class DeactiveApiKey extends Command 
{
    /**
     * Error messages
     */
    const MESSAGE_ERROR_INVALID_NAME                = 'Invalid Name';
    const MESSAGE_ERROR_NAME_DOES_NOT_EXIST         = 'Name does not exist';

    /**
     * @var string
     */
    protected $signature = 'apikey:deactive {$name}';

    /**
     * The console command description
     * @var string
     */

    public function handle()
    {
        $name = $this->argument('name');

        $error = $this->validateName($name);

        if ($error) {
            $this->error($error);
            return;
        }

        $key = ApiKey::where('name', $name)->first();

        if (!$key->active) {
            $this->info('Key "' . $name . '" is already deactivated');
            return;
        }

        $key->active = 0;
        $key->save();

        $this->info('Deactivated key: ' . $name);
    }

    /**
     * Validate name
     *
     * @param string $name
     * @return string
     */
    protected function validateName($name)
    {
        if (!ApiKey::isValidName($name)) {
            return self::MESSAGE_ERROR_INVALID_NAME;
        }
        if (!ApiKey::nameExists($name)) {
            return self::MESSAGE_ERROR_NAME_DOES_NOT_EXIST;
        }
        return null;
    }

}