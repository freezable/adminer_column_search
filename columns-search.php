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
