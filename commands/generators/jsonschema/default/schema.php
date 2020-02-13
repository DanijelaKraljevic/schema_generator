<?php
/**
 * This is the default for generating json schema of the specified table.
 */

echo "{";

echo "\n";

echo '"id": "http://localhost:80/'.$id.'",';

echo "\n";
echo '"type": "object",';

echo "\n";

echo '"properties": {';

echo "\n";
$columnCounter = 0;
foreach ($tableSchema->columns as $column) {
    $columnCounter++;
    echo '"'.$column->name.'" : {';

    echo "\n";
    
    echo str_repeat("\x20", 2);
    echo '"id": "/properties/'.$column->name.'",';

    echo "\n";

    echo str_repeat("\x20", 2);

    $phpType = $column->phpType;

    if ($phpType == "boolean" || $phpType== "string") {
        $phpType = '"string"';
    }else if($phpType == "double"){
        $phpType = '"number"';
    } else {
        $phpType = '"'.$phpType.'"';
    }

    if ($column->allowNull) {
            $phpType =  '['.$phpType.', "null"]';
    } else {
        $required[] = $column->name;
    }


    echo '"type": '.$phpType.',';

    echo "\n";
    echo str_repeat("\x20", 2);

    if ($phpType == "integer") {
        echo '"minimum": 1,';

        echo "\n";
    echo str_repeat("\x20", 2);
    }

    if ($column->defaultValue) {
        echo '"default": "'.$column->defaultValue.'",';

        echo "\n";
    echo str_repeat("\x20", 2);
    }
    if ($column->size) {
        echo '"maxLength": '.$column->size.',';

        echo "\n";
    echo str_repeat("\x20", 2);
    }
    echo '"title": "'.$labels[$column->name].'",';

    echo "\n";
    echo str_repeat("\x20", 2);
    echo '"description": "'.($column->comment ?$column->comment : $labels[$column->name]).'"';

    echo "\n";
    echo "}";

    if ($columnCounter != sizeof($tableSchema->columns)) {
        echo ",";
    }

    echo "\n";
}


echo "}";


if (!empty($required) && strtolower($action) != 'patch') {
    echo ",";
    echo "\n";
    echo '"required": [';

    echo "\n";

    foreach ($required as $key => $r) {
        echo '"'.$r.'"';

        if (!(sizeof($required)-1 == $key)) {
            echo ",";
        }

        echo "\n";
    }
    echo '],';

    echo "\n";
    echo '"minProperties": '.sizeof($required);
}

echo "\n";

echo "}";
