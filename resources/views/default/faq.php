<?php
    require dirname(__FILE__) . '/../header.php';

    $questions = array(
        array(
            'title' => 'Chciałem tylko zapisać się na spamlistę ale aplikacja prosi o uprawnienia do pisania w moim imieniu. Dlaczego?',
            'content' => 'Lista uprawnień jest wspólna dla osób tworzących spamlisty i zapisujących się do nich.<br />
                            Jeśli jednak nie masz obecnie założonej żadnej swojej spamlisty możesz spokojnie odznaczyć to uprawnienie.<br />
                            Nie jest to decyzja ostateczna i jeśli kiedyś będziesz chciał prowadzić swoją spamlistę, będziesz mógł ponownie nadać takie uprawnienia.'
        ),
        array(
            'title' => 'Przy próbie zawołania dostaję komunikat "brak uprawnień" Co robić?',
            'content' => 'Logując się do MirkoList za pierwszym razem nie nadałeś aplikacji uprawnień do pisania w Twoim imieniu.<br />
                            Informację jak nadać je w tym momencie znajdziesz w kolejnym pytaniu.'
        ),
        array(
            'title' => 'Nie przyznałem aplikacji uprawnień do pisania a chciałbym prowadzić swoją spamlistę. Co robić?',
            'content' => 'Po pierwsze musisz wylogować się z aplikacji MirkoListy.<br />
                            Następnie usunąć ją z listy uprawnionych do korzystania z Twojego konta na wykopie (tutaj <a href="' . $_ENV['WYKOP_BASE_URL'] . 'ustawienia/sesje/">' . $_ENV['WYKOP_BASE_URL'] . 'ustawienia/sesje/</a> ).<br />
                            Teraz możesz już ponownie zalogować się do MirkoList i zaznaczyć wymagane uprawnienia.'
        ),
        array(
            'title' => 'Mam swoją spamlistę w notatniku i chciałbym zacząć korzystać z MirkoList. Mogę ją zaimportować?',
            'content' => 'Jest taka możliwości jednak nie jest dostępna dla każdego ze względu na duże ryzyko nadużyć.'
            . '<br />W celu odblokowania importu skontaktuj się z administratorem (link w stopce).'
            . '<br />Będziesz musiał pokazać przykładowy wpis, w którym używałeś swojej obecnej spamlisty.'
        ),
        array(
            'title' => 'Mam już uprawnienia do importowania ale moja spamlista liczy kilkaset osób. Czy to nie za dużo?',
            'content' => 'Zdecydowanie nie, chociaż najlepiej będzie importować po 50-100 osób w jednym momencie.'
            . '<br />W trakcie importu wykonywanych jest wiele czasochłonnych czynności co przy kilkuset osobach może doprowadzić do przedwczesnego przerwania operacji.'
        ),
        array(
            'title' => 'Na liście użytkowników mogę nadać różne uprawnienia. Co one znaczą?',
            'content' => 'Każdemu użytkownikowi możesz przydzielić jedną z czterech grup:'
            . '<br /><strong>zbanowany</strong> - użytkownik nie będzie wołany do wpisów. Nie może opuścić listy i zapisać się ponownie. Nie jest liczony do ogólnej listy zapisanych'
            . '<br /><strong>użytkownik</strong> - nie daje żadnych uprawnienień poza byciem wołanym do wpisów (domyślna grupa)'
            . '<br /><strong>wołający</strong> - może wołać zapisane osoby przez stronę'
            . '<br /><strong>administrator</strong> - może wołać zapisane osoby oraz edytować ustawienia grupy (nazwę, opis, uprawnienia innych osób)'
        ),
        array(
            'title' => 'Dlaczego wołanie trwa tak długo?',
            'content' => 'Ze względu na limity wykorzystania API wykopu Twoje komentarze dodawane są w sekundowych odstępach.'
            . '<br />Dzięki temu nawet w przypadku dużych list wszystkie komentarze powinny dodać się prawidłowo.'
            . '<br />Jeśli jednak wykop odrzuci próbę dodania komentarza MirkoListy odczekają dodatkowe 10 sekund aby spróbować ponownie.'
        )
    );
?>
        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel-group" id="accordion">
<?php
    foreach ($questions as $key => $question) {
?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $key; ?>">
                                    <?php echo $question['title']; ?>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse<?php echo $key; ?>" class="panel-collapse collapse">
                            <div class="panel-body">
                                <?php echo $question['content']; ?>
                            </div>
                        </div>
                    </div>
<?php
    }
?>
                </div>
            </div>
<?php
    require dirname(__FILE__) . '/../footer.php';
?>