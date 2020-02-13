<?php

$actions = ["", "Post", "Put", "Patch", "Response"];

foreach ($actions as $action) :
    echo "\n";

    echo str_repeat("\x20", 4);
    echo "$TableNoSpace$action:";

    echo "\n";
    echo str_repeat("\x20", 6);
    echo 'type: object';

    echo "\n";

    echo str_repeat("\x20", 6);
    echo 'properties:';

    echo "\n";
    $columnCounter = 0;
    $required = [];
    foreach ($tableSchema->columns as $column) {
        $columnCounter++;

        echo str_repeat("\x20", 8);
        echo "$column->name:";

        echo "\n";

        $phpType = $column->phpType;
        $type = $column->type;
        $format = "";

        switch ($phpType) {
            case "string": {
                    switch ($type) {
                        case "binary":
                        case "date": {
                                $format = "format: \"$type\" ";
                                break;
                            }
                        case "datetime": {
                                $format = 'format: "date-time"';
                                break;
                            }
                    }
                    break;
                }
            case  "double": {
                    $phpType = "number";
                    $format = "format: \"$type\" ";
                    break;
                }
            case  "integer": {
                    switch ($type) {
                        case "smallint":
                        case "integer": {
                                $format = 'format: "int32"';
                                break;
                            }
                        case "bigint": {
                                $format = 'format: "int64"';
                                break;
                            }
                    }
                    break;
                }
                break;
        }

        echo str_repeat("\x20", 10);
        echo "type: {$phpType}";



        if (!empty($format)) {
            echo "\n";

            echo str_repeat("\x20", 10);
            echo $format;
        }

        if (!$column->allowNull) {
            $required[] = $column->name;
        }

        if ($phpType == "integer") {
            echo "\n";

            echo str_repeat("\x20", 10);
            echo 'minimum: 1';
        }

        if ($column->defaultValue) {
            echo "\n";

            echo str_repeat("\x20", 10);
            echo "default: \"$column->defaultValue \"";
        }
        if ($column->size) {
            echo "\n";

            echo str_repeat("\x20", 10);
            echo "maxLength: $column->size";
        }


        echo "\n";

        echo str_repeat("\x20", 10);
        $title = $labels[$column->name];
        echo "title: \"$title\"";

        echo "\n";
    }


    if (!empty($required) && in_array(strtolower($action), ["put", "post"])) {

        echo str_repeat("\x20", 6);
        echo 'required:';

        echo "\n";

        foreach ($required as $key => $r) {
            if ($r != "id") {

                echo str_repeat("\x20", 10);
                echo "- $r";
                echo "\n";
            }
        }

        echo str_repeat("\x20", 6);
        echo 'minProperties: ' . (sizeof($required) - 1);

        echo "\n";
    }

endforeach;