<?php
    require dirname(__FILE__) . '/../header.php';
    
?>
        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-lg-7">
                <ol class="breadcrumb">
                    <li>
                    Katalog list
                    </li>
                </ol>
            </div>
            <div class="col-lg-5">
                <form class="form-inline" action="<?php echo route('getSpamlistsUrl'); ?>" id="searchForm" method="GET">
                    <div class="input-group">
                        <select name="categoryId" class="form-control">
                                <option value="">-- kategoria --</option>
<?php
    foreach ($categories as $loopItem) {
?>
                            <option value="<?php echo $loopItem->id; ?>"<?php echo $loopItem->id === $categoryId ? ' selected="selected"' : ''; ?>><?php echo $loopItem->name; ?></option>
<?php
    }
?>
                        </select>
                    </div>
                    <div class="input-group">
                        <select name="cityId" class="form-control">
                                <option value="">-- miasto --</option>
<?php
    foreach ($cities as $loopItem) {
?>
                            <option value="<?php echo $loopItem->id; ?>"<?php echo $loopItem->id === $cityId ? ' selected="selected"' : ''; ?>><?php echo $loopItem->name; ?></option>
<?php
    }
?>
                        </select>
                    </div>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" onClick="document.location.href = '<?php echo route('getSpamlistsUrl'); ?>';">Wyczyść</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
<?php
    if ($paginator->count() === 0) {
?>
                <div class="alert alert-info" role="alert" style="margin-top: 25px">Brak list</div>
<?php
    } else {
?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Właściciel</th>
                        <th>Nazwa</th>                            
                        <th class="visible-desktop">Opis</th>
                        <th>Zapisani</th>
                        <th>Wołania</th>
                    </tr>
                </thead>

                <tbody>
<?php
        foreach ($paginator as $key => $loopItem) {
?>
                    <tr>
                        <td style="text-align: center">
                            <a href="<?php echo $_ENV['WYKOP_BASE_URL']; ?>ludzie/<?php echo $loopItem->user->nick; ?>" target="_blank" rel="nofollow">
                                <img src="<?php echo $loopItem->user->avatar_url; ?>" class="img-polaroid <?php echo $app->make('App\Services\TemplateService')->getSexClass($loopItem->user->sex); ?>" />
                                <br />
                                <span style="color: #FF5917;" class="visible-desktop"><?php echo $loopItem->user->nick; ?></span>   
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo route('getSpamlistUrl', ['uid' => $loopItem->uid]); ?>"><?php echo $app->make('App\Services\TemplateService')->clearValue($loopItem->name); ?></a>
                        </td>
                        <td class="visible-desktop">
                            <?php echo $app->make('App\Services\TemplateService')->clearValue($loopItem->description); ?>
                        </td>
                        <td>
                            <span class="badge badge-info"><?php echo $loopItem->joined_count; ?></span>
                        </td>
                        <td>
                            <span class="badge badge-info"><?php echo $loopItem->called_count; ?></span>
                        </td>
                    </tr>
<?php
        }
?>
                </tbody>
            </table>
<?php
    }
?>

        </div>
        <div style="text-align: center">
<?php
    echo $paginator->appends(\Illuminate\Support\Facades\Input::except('page'))->render();
?>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery('#searchForm select').on('change', function() {
                    jQuery('#searchForm').submit();
                })
            });
        </script>
<?php
    require dirname(__FILE__) . '/../footer.php';