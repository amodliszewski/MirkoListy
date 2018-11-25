<?php
namespace App\Services;

use WykoCommon\Services\TemplateService as Base;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Spamlist;

class TemplateService extends Base
{
    private $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function getSexClass($sex) {
        switch ($sex) {
            case 1:
                return 'sm';
            case 2:
                return 'sf';
            default:
                return 's';
        }
    }

    public function generateListPanel($item) {
?>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading--spamlist">
                        <a href="<?php echo route('getSpamlistUrl', array('uid' => $item['uid'])); ?>"><h4><?php echo $this->clearValue($item['name']); ?></h4></a>
                    </div>
                    <div class="panel-body">
                        <p><?php echo $this->clearValue($item['description']); ?></p>
                        <hr />
                        <p>
                            Zapisane osoby: <?php echo $item['joined_count']; ?>
                            <br />
                            Wołań: <?php echo $item['called_count']; ?>
                        </p>
                    </div>
                </div>
            </div>
<?php
    }

    public function generateStatisticsTags() {
        if (
            Route::currentRouteNamed('getSpamlistPeopleUrl')
            || Route::currentRouteNamed('getSpamlistUrl')
        ) {
            $entity = Spamlist::withTrashed()
                ->where('uid', '=', $this->request->route('uid'))
                ->first();

            if ($entity === null || $entity->trashed()) {
                return;
            }
?>
        gtag('set', {
            'author': '<?php echo $entity->user_id; ?>',
            'article': '<?php echo $entity->uid; ?>'
        });
<?php
        }
    }
}