<?php
/**
 * Created by XZ Software.
 * Smart code for smart wallet
 * http://xzsoftware.pl
 * User adrianmodliszewski
 * Date: 21/01/2019
 * Time: 20:45
 */

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class GenerateApiToken extends Command
{
    /** @var string */
    protected $signature = 'api:token {nick}';

    /** @var string */
    protected $description = 'Generates new api token for given user';

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $user = User::where('nick', $this->argument('nick'))->first();

        if ($user === null) {
            $this->error('Nie znaleziono takiego usera w systemie');

            return;
        }

        $user->api_key = Uuid::uuid4()->toString();
        $user->save();

        $this->info('Klucz usera ' . $user->nick . ' ustawiono na nową wartość: ' . $user->api_key);
    }
}
