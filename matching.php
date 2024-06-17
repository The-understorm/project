<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Matching - Home</title>

  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form">
            <form action="" method="post">
                <input type='text' name='preferences' placeholder="Ваши предпочтения"><br>
                <input type='text' name='men' placeholder="Количество мужчин"><br>
                <input type='text' name='women' placeholder="Количество женщин"><br>
                <input type='submit' value='Отправить'>
            </form>
        </div>

</body>
</html>

<?php

//Подлючаем сервер и таблички, из которых будем доставать данные
include("connection.php");
include("tablemanagement.php");

echo "<br>";

//Записываем предпочтения мужчин в массив
$men_preferences = array();

$sql_men_preferences = "SELECT id, preferences FROM men";
$result = $conn->query($sql_men_preferences);

if ($result->num_rows > 0) {
    $k = 0;
    while($row = $result->fetch_assoc()) {
        ++$k;
        echo 'm' . $k .' : ';
        $preferences = explode('.', $row["preferences"]);
        foreach ($preferences as $key => $value) {
            $men_preferences[$key][] = $value;
            echo $value . ' ';
        }
        echo '<br>';
    }
}

echo '<br>';
$women_preferences = array();

//Записываем предпочтения женщин в массив
$sql_women_preferences = "SELECT preferences FROM women";
$result = $conn->query($sql_women_preferences);

if ($result->num_rows > 0) {
    $k = 0;
    while($row = $result->fetch_assoc()) {
        ++$k;
        echo 'w' . $k .' : ';
        $preferences = explode('.', $row["preferences"]);
        foreach ($preferences as $key => $value) {
            $women_preferences[$key][] = $value;
            echo $value . ' ';
        }
        echo '<br>';
    }
}

echo '<br>';

function Transpose($array) {
    $transposedArray = array();
    foreach ($array as $key => $subArray) {
        foreach ($subArray as $subKey => $subValue) {
            $transposedArray[$subKey][$key] = $subValue;
        }
    }
    return $transposedArray;
}


function ProcessProposing ($number_of_proposing, $number_of_receiving, $preferences_of_proposing, $preferences_of_receiving, $position_in_preferences, $type) {
    $demand = array();

    for ($i = 0; $i < $number_of_receiving; ++$i) {
        for ($j = 0; $j < $number_of_proposing; ++$j) {
            $demand[$i][$j] = 0;
        }
    }

    //Найти следующих по предпочтительности партнеров
    $position_updated = false;
    for ($i = 0; $i < $number_of_proposing; ++$i) {
        $j = $position_in_preferences[$i];
        if ($j != -1) {
            $preferable_partner = $preferences_of_proposing[$j][$i];
            $demand[$preferable_partner - 1][$i] = 1;
            if ($type == 0) {
                echo 'w' . $preferable_partner . ' ';
            } else {
                echo 'm'. $preferable_partner .' ';
            }
        } else {
            if ($type == 1) {
                echo 'w' . $i+1 . ' ';
            } else {
                echo 'm'. $i+1 .' ';
            }
        }
    }

    echo '<br>';

    $transposed_preferences = Transpose($preferences_of_receiving);

    //Партнер может выбирать самого предпочтительного, из тех, кто либо обручен с ним, 
    //либо делает предложение
    //Остальные, те кого он не выбрал, в следующий раунд будут пытаться сделать предложение 
    //следующему по препочтениям партнеру. Если следующего партнера нет в списке, то он осается один
    for ($i = 0; $i < $number_of_receiving; ++$i) {
        $sum = array_sum($demand[$i]);
        $key_list = array();
        if (isset($transposed_preferences[$i])) {
            $k = 0;
            for ($j = 0; $j < $number_of_proposing; ++$j) {
                if ($demand[$i][$j] == 1) {
                    $key = array_search($j + 1, $transposed_preferences[$i]);
                    $key_list[$k] = $key;
                    $k++;
                }
            }

            for ($j = 0; $j < count($key_list); ++$j) {
                $k = $key_list[$j];
                if ($k > min($key_list)) {
                    $proposing_number = $transposed_preferences[$i][$k] - 1;
                    if ($position_in_preferences[$proposing_number] + 1 != $number_of_receiving && $position_in_preferences[$i] != -1) {
                        $position_in_preferences[$proposing_number] += 1;
                        $position_updated = true;
                    } else {
                        $position_in_preferences[$proposing_number] = -1;
                        $position_updated = true;
                    }
                }
            }
        }
    }

    //Если что-то изменилось, то есть не было достигнуто стаблильное паросочетание, то продолжаем
    if ($position_updated) {
        ProcessProposing($number_of_proposing, $number_of_receiving, $preferences_of_proposing, $preferences_of_receiving, $position_in_preferences, $type);
    }
}

function GoProposing ($number_of_proposing, $number_of_receiving, $preferences_of_proposing, $preferences_of_receiving, $type) {
    $position_in_preferences = array();
    for ($i = 0; $i < $number_of_proposing; ++$i) {
        $position_in_preferences[$i] = 0;
    }

    if ($type == 0) {
        echo 'Pезультат работы алгоитма, если предложения будут делать мужчины:<br>';
    } else {
        echo 'Pезультат работы алгоитма, если предложения будут делать женщины:<br>';
    }

    for ($i = 0; $i < $number_of_proposing; ++$i) {
        if ($type == 0) {
            echo 'm' . $i + 1 . ' ';
        } else {
            echo 'w' . $i + 1 . ' ';
        }
    }

    echo '<br>';
    for ($i = 0; $i < $number_of_proposing; ++$i) {
        echo '----';
    }
    echo '<br>';
    
    ProcessProposing($number_of_proposing, $number_of_receiving, $preferences_of_proposing, $preferences_of_receiving, $position_in_preferences, $type);
}

$type = 1;
GoProposing($nw, $nm, $women_preferences, $men_preferences, $type);

echo '<br>';
echo '<br>';

$type = 0;
GoProposing($nm, $nw, $men_preferences, $women_preferences, $type);


$conn->close();
?>

</body>
</html>