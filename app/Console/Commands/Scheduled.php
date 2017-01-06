<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use WykoCommon\Services\WykopAPI;
use App\Models\ScheduledPost;

class Scheduled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduled:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Posts scheduled';

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
        $item = ScheduledPost::where('post_at', '<=', date('Y-m-d H:i:s', strtotime('+65 seconds')))
                ->first();

        if ($item === null) {
            return;
        }

        while (true) {
            if ($item->post_at->timestamp <= time()) {
                break;
            }

            usleep(500);
        }

        $result = $this->wykopAPI->doRequest('entries/add,userkey,' . $item->user_key, array(
            'body' => $item->content
        ));

        if ($result) {
            if (!empty($item->embed)) {
                $this->wykopAPI->doRequest('entries/edit/' . $result['id'] . ',userkey,' . $item->user_key, array(
                    'body' => $item->content,
                    'embed' => $item->embed
                ));
            }

            $item->delete();

            $this->info("\n\nPosted");
        } else {
            $this->warn("\n\nNot posted!");
        }
    }
}