<?php

// Get root, then get file contents of .sql files
$root = $_SERVER["DOCUMENT_ROOT"]."/sql";
// File contents of tables
$sqlFiles = [
    "/tables_schema.sql", // Shall ALWAYS be the first in this array/list!!!!
    "/posts.sql",
    "/users.sql",
    "/answers.sql"
];

// Create tables_schema if not exists
$schema_table_contents = file_get_contents($root.$sqlFiles[0]);
$db->tableCreate($schema_table_contents);

// Iterate over tables
foreach ($sqlFiles as $file) {
    // Get name of table:
    $tableName = str_replace("/", "", str_replace(".sql", "", $file));

    // Get last known version of table
    $schemaTable = str_replace("/", "", str_replace(".sql", "", $sqlFiles[0]));
    $res = $db->select("SELECT * FROM $schemaTable WHERE `name`=?;", "s", $tableName);
    if ($res->num_rows > 0) {
        $oldCols = mysqli_fetch_assoc($res)["rows"];
    } else {
        $oldCols = null;
    }

    // Get strings to remove:
    $firstRemoval = "CREATE TABLE IF NOT EXISTS $tableName (";
    $secondRemoval = ");";
    // Get contents of file
    $query = file_get_contents($root.$file);
    // Remove those strings
    $cols = str_replace($firstRemoval, "", $query);
    $cols = str_replace($secondRemoval, "", $cols);

    // Strip whitespace from cols
    $tmp_array = [];
    $tmp_explode = explode(",", $cols);
    foreach ($tmp_explode as $value) {
        $trimmed = trim($value);
        array_push($tmp_array, $trimmed);
    }
    // Combine string again after trimming white space:
    $cols = implode(", ", $tmp_array);

    // Insert cols into the database.
    if ($oldCols === null) {
        // If record for table doesn't exist previously
        $db->insert("INSERT INTO $schemaTable (`name`, `rows`) VALUES (?, ?);", "ss", $tableName, $cols);
    } else if ($oldCols !== $cols) {
        // Changes were made to table and the last known rendition of the table is different from current rendition
        $db->insert("UPDATE $schemaTable SET `rows`=? WHERE `name`=?;", "ss", $cols, $tableName);

        // Now, we've updated the database properly, but now, we need to actually modify the tables themselves... here comes the hard part
        $i = 0;
        $tmp_expl = explode(", ", $cols);
        foreach ($tmp_expl as $col) {
            // Ignore first column that has PRIMARY KEY
            if ($i !== 0) {
                // Get string
                // Get new col name:
                $firstSpacePos = strpos($col, " ");
                if ($firstSpacePos !== false) {
                    $colName = substr($col, 0, $firstSpacePos);
                    $newDataType = str_replace($colName." ", "", $col);
                } else {
                    // Nothing, because this theoretically should never get triggered.
                }

                // Finally, alter the table if need be:
                $db->addOrModifyColumn($colName, $newDataType, $tableName);
            }
            $i++;
        }
    }
    // Else do nothing, because that means the codebase & database are up to date with each other for this particular iteration

    // Create table, if not exists
    $db->tableCreate($query);
};






// Delete/drop no-longer-used tables:
$deprecatedTables = [
    "questions"
];
foreach ($deprecatedTables as $table) {
    $db->dropTable($table);
}