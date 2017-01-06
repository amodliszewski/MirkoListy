<?php
    require dirname(__FILE__) . '/../header.php';
?>

        <div class="row">
            <div class="col-md-12">
                <h3>Edytuj wpis</h3>
                <form method="POST" action="">
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Data publikacji:</label>
                            <input type="text" class="form-control" name="post_at" value="<?php echo !empty(Illuminate\Support\Facades\Input::old('post_at')) ? Illuminate\Support\Facades\Input::old('post_at') : $item->post_at; ?>" placeholder="RRRR-MM-DD HH:ii:ss" />
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Obrazek/video:</label>
                            <input type="text" class="form-control" name="embed" value="<?php echo !empty(Illuminate\Support\Facades\Input::old('embed')) ? Illuminate\Support\Facades\Input::old('embed') : $item->embed; ?>" />
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Treść:</label>
                            <textarea rows="10" cols="100" class="form-control" name="content" required data-validation-required-message="Musisz uzupełnić treść" maxlength="9999"><?php echo !empty(Illuminate\Support\Facades\Input::old('content')) ? Illuminate\Support\Facades\Input::old('content') : $item->content; ?></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />

                    <button type="submit" class="btn btn-primary">Zapisz</button>
                </form>
            </div>
        </div>
<?php
    require dirname(__FILE__) . '/../footer.php';