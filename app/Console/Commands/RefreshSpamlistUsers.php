<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use WykoCommon\Services\WykopAPI;
use App\Models\Spamlist;
use App\Models\UserSpamlist;

class RefreshSpamlistUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:spamlist {uid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh spamlist users';

    /**
     *
     * @var \WykoCommon\Services\WykopAPI
     */
    private $wykopAPI = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(WykopAPI $wykopAPI) {
        parent::__construct();

        $this->wykopAPI = $wykopAPI;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $spamlist = Spamlist::where('uid', $this->argument('uid'))
                ->first();

        if ($spamlist === null) {
            return;
        }

        $items = UserSpamlist::where('spamlist_id', $spamlist->id)
                ->get();

        if ($items === null) {
            return;
        }

        foreach ($items as $key => $item) {
            $result = $this->wykopAPI->doRequest('profile/index/' . $item->user->nick . ',userkey,' . $item->user_key);

            if (!isset($result['login'])) {
                $this->warn('Ignoruję ' . $item->user->nick);

                continue;
            }

            $item->user->nick = $result['login'];
            $item->user->avatar_url = $result['avatar_med'];
            $item->user->color = $result['author_group'];

            if ($result['sex'] === 'male') {
                $item->user->sex = 1;
            } else if ($result['sex'] === 'female') {
                $item->user->sex = 2;
            } else {
                $item->user->sex = 0;
            }

            $item->user->updated_at = time();

            $item->user->save();

            $this->info('Zaktualizowano ' . ($key + 1 ) . ' / ' . $items->count() . ' - ' . $item->user->nick);

            if ($key % 9 === 0) {
                sleep(5);
            }
        }
    }
}