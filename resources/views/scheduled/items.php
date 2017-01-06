<?php
    require dirname(__FILE__) . '/../header.php';
    
?>
        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">
                    Wszystkie zaplanowane
                </h2>
            </div>

            <div class="col-md-12">
                <a class="btn btn-default" href="<?php echo route('addScheduledUrl'); ?>">zaplanuj wpis</a><br /><br />
<?php
    if (!is_object($items) || $items->count() === 0) {
?>
                <div class="alert alert-info" role="alert" style="margin-top: 25px">Brak zaplanowanych</div>
<?php
    } else {
?>
                <table class="table">
                    <thead>
                        <th>
                            ID
                        </th>
                        <th>
                            Data
                        </th>
                        <th>
                            Opcje
                        </th>
                    </thead>
<?php
        foreach ($items as $key => $foreachItem) {
?>
                    <tr>
                        <td>
                            <?php echo $foreachItem->id; ?>
                        </td>
                        <td>
                            <?php echo $foreachItem->post_at; ?>
                        </td>
                        <td>
                            <form method="POST" action="<?php echo route('deleteScheduledUrl', array('id' => $foreachItem->id)); ?>" style="display: inline">
                                <input type="submit" class="btn btn-danger" value="usuń" />
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                            </form>
                            <a class="btn btn-info" href="<?php echo route('editScheduledUrl', array('id' => $foreachItem->id)); ?>">edytuj</a>
                        </td>
                    </tr>
<?php
        }
?>
                </table>
<?php
    }
?>

        </div>

<?php
    require dirname(__FILE__) . '/../footer.php';