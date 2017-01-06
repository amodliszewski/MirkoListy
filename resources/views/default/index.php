<?php
    require dirname(__FILE__) . '/../header.php';
?>

        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">
                    Najpopularniejsze listy <small>(<a href="<?php echo route('getSpamlistsUrl'); ?>">zobacz wszystkie</a>)</small>
                </h2>
            </div>
<?php
    if (isset($popular) && is_array($popular)) {
        foreach ($popular as $item) {
            $app->make('App\Services\TemplateService')->generateListPanel($item);
        }
    }
?>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">O MirkoListach</h2>
            </div>
            <div class="col-md-12">
                <p>MirkoListy to serwis, dzięki któremu tworzenie i zarządzanie "spamlistami" staje się szybkie, łatwe i przyjemne.</p>
                <p>Nie będziesz już musiał dodawać/usuwać zainteresowanych z listy ani wołać ręcznie. Każdy sam będzie decydował czy chce być wołany a Twoja rola ograniczy się do wklejenia linku do wpisu i kliknięcia "wołaj".</p>
                <p>&nbsp;</p>
                <p><strong>Chcesz dołączyć do istniejącej listy?</strong> Wystarczy, że klikniesz "dołącz" na stronie wybranej listy. Nic więcej!</p>
                <p>Jeśli rozmyślisz się zawsze możesz wypisać się z listy klikając "opuść". Bez potrzeby wielokrotnego upominania się o wykreślenie.</p>
                <p>&nbsp;</p>
                <p><strong>Chcesz utworzyć nową listę?</strong> Po zalogowaniu wybierz "dodaj listę" w prawym górnym rogu (będziesz musiał potwierdzić, że zgadasz się, żeby MirkoListy dodawały wpisy w Twoim imieniu).</p>
                <p>Po wypełnieniu krótkiego formularza będziesz mógł od razu wysłać zainteresowanym link do zapisania!</p>
                <p>&nbsp;</p>
                <p><strong>Chcesz tylko zawołać plusujących/komentujących Twój wpis?</strong> Po zalogowaniu wybierz "wołaj" z górnego menu.</p>
                <p>Wystarczy, że podasz link do wpisu i gotowe!</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>Obecnie mamy <strong><?php echo $statsUsers; ?></strong> użytkowników zapisanych na <strong><?php echo $statsSpamlists; ?></strong> listach, które wołano już <strong><?php echo $statsCalls; ?></strong> razy.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">Ostatnie wydarzenia</h2>
            </div>
            <div class="col-md-12">
<?php
    if (isset($latestLogs) && is_object($latestLogs) && $latestLogs->count() > 0) {
        foreach ($latestLogs as $loopItem) {
?>
                <p>
                    <i class="fa fa-clock-o"></i> <?php echo $loopItem->created_at; ?>
<?php
    if (is_object($loopItem->spamlist)) {
?>
                    - <a href="<?php echo route('getSpamlistUrl', array('uid' => $loopItem->spamlist->uid)); ?>"><?php echo $app->make('App\Services\TemplateService')->clearValue($loopItem->spamlist->name); ?></a>
<?php
    }
?>
                    - <?php echo $app->make('App\Services\LogService')->getLogText($loopItem); ?>
                </p>
<?php
        }
    }
?>
            </div>
        </div>

<?php
    require dirname(__FILE__) . '/../footer.php';