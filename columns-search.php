<?php

/** Column search: plugin for Adminer
 * Search tables by given column name
 * @author Yehor Chernyshov
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */

class AdminerColumnsSearch{


    function tablesPrint($tables) {
        $resultOfSearch = [];
        if (!empty($_POST['searchColumn'])){
            $search = trim($_POST['searchColumn']);
            foreach (tables_list() as $table => $type){
                foreach (fields($table) as $column => $columnData){
                    if (stripos($column, $search) !== false) {
                        $resultOfSearch[$table] = $type;
                    }
                }
            }
        }
        if ($_POST['resetSearchColumn']){
            unset($_POST['searchColumn']);
        }
        ?>
        <form action="" method="post">
            Search column in tables<br>
            <input type="search" name="searchColumn" value="<?php echo $_POST['searchColumn'];?>">
            <input type="submit" name="search" value="Search">
            <input type="submit" name="resetSearchColumn" value="Reset">
            <input type='hidden' name='token' value='<?php echo  get_token(); ?>'>
        </form>
        <p class="jsonly">
            Search table<br>
            <input id="filter-field" onkeyup="tablesFilterInput();" autocomplete="off">
        <p id='tables' onmouseover='menuOver(this, event);' onmouseout='menuOut(this);'>
        <script type="text/javascript">
            var tablesFilterTimeout = null;
            var tablesFilterValue = '';

            function tablesFilter(){
                var value = document.getElementById('filter-field').value.toLowerCase();
                if (value == tablesFilterValue) {
                    return;
                }
                tablesFilterValue = value;
                if (value != '') {
                    var reg = (value + '').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, '\\$1');
                    reg = new RegExp('('+ reg + ')', 'gi');
                }
                if (sessionStorage) {
                    sessionStorage.setItem('adminer_tables_filter', value);
                }
                var tables = document.getElementById('tables').getElementsByTagName('span');
                for (var i = 0; i < tables.length; i++) {
                    var a = tables[i].getElementsByTagName('a')[1];
                    var text = tables[i].getAttribute('data-table-name');
                    if (value == '') {
                        tables[i].className = '';
                        a.innerHTML = text;
                    } else {
                        tables[i].className = (text.toLowerCase().indexOf(value) == -1 ? 'hidden' : '');
                        a.innerHTML = text.replace(reg, '<strong>$1</strong>');
                    }
                }
            }

            function tablesFilterInput() {
                window.clearTimeout(tablesFilterTimeout);
                tablesFilterTimeout = window.setTimeout(tablesFilter, 200);
            }

            if (sessionStorage){
                var db = document.getElementById('dbs').getElementsByTagName('select')[0];
                db = db.options[db.selectedIndex].text;
                if (db == sessionStorage.getItem('adminer_tables_filter_db') && sessionStorage.getItem('adminer_tables_filter')){
                    document.getElementById('filter-field').value = sessionStorage.getItem('adminer_tables_filter');
                    tablesFilter();
                }
                sessionStorage.setItem('adminer_tables_filter_db', db);
            }
        </script>
        <?php
        $tables = !empty($_POST['searchColumn']) ? $resultOfSearch : $tables;
        if (count($tables) > 0){
            foreach ($tables as $table => $type) {
                echo '<span data-table-name="'.h($table).'"><a href="'.h(ME).'select='.urlencode($table).'"'.bold($_GET["select"] == $table).">".lang('select')."</a> ";
                echo '<a href="'.h(ME).'table='.urlencode($table).'"'.bold($_GET["table"] == $table).">".h($table)."</a><br></span>\n";
            }
        } else {
            echo "<p class='message'>" . lang('No tables.') . "\n";
        }
        return true;
    }

}
