<?php
    require dirname(__FILE__) . '/../header.php';
?>

        <div class="row">
            <div class="col-md-12">
                <h3>Profil użytkownika</h3>
                <form method="POST" action="<?php echo route('checkProfileUrl'); ?>">
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Nick:</label>
                            <input type="text" class="form-control" name="nick" value="<?php echo Illuminate\Support\Facades\Input::old('nick'); ?>" />
                            <p class="help-block"></p>
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />

                    <button type="submit" class="btn btn-primary">Zobacz profil</button>
                </form>
            </div>
        </div>
<?php
    require dirname(__FILE__) . '/../footer.php';