<?php
namespace App\Services;

use WykoCommon\Services\TemplateService as Base;

class TemplateService extends Base
{
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
}