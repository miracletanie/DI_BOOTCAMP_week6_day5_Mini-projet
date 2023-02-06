<?php

//grille du jeu
$state = [
    ['', '', ''],
    ['', '', ''],
    ['', '', ''],
];

$play = 'X';
$activeCellule = [0 => 0, 1 => 0];



function renderGame($state, $activeCellule, $play)
{
    $output = '';
    $output .= 'play:' . $play . "\n";
    foreach ($state as $x => $line) {
        $output .= '|';
        foreach ($line as $y => $item) {
            switch ($item) {
                case '':
                $cell = ' ';
                break;
                case 'X':
                $cell = 'X';
                break;
                case 'O':
                $cell = 'O';
                break;
            }
            if ($activeCellule[0] == $x && $activeCellule[1] == $y) {
                $cell = '-'. $cell . '-';
            } else {
                $cell = ' ' . $cell . ' ';
            }

            $output .= $cell . '|';
        }
        $output .= "\n";
    }
    return $output;
}


function translateKeypress($string)
{
    switch ($string) {
        case "\033[A":
            return "UP";
        case "\033[B":
            return "DOWN";
        case "\033[C":
            return "RIGHT";
        case "\033[D":
            return "LEFT";
        case "\n":
            return "ENTER";
        case " ":
            return "SPACE";
        case "\010":
        case "\177":
            return "BACKSPACE";
        case "\t":
            return "TAB";
        case "\e":
            return "ESC";
    }
    return $string;
}


/*la fonction permettre aux joueurs de se déplacer sur le plateau et de sélectionner leur mouvement.
 pour se fait on écoute les touches qui sont saisies et on effectue l'action correspondant. la fonction met à jour
 la cellule active
 */

function move($stdin, &$state, &$activeCellule, &$play)
{
    $key = fgets($stdin);
    if ($key) {
        $key = translateKeypress($key);
        switch ($key) {
            case "UP":
                if ($activeCellule[0] >= 1) {
                    $activeCellule[0]--;
                }
                break;
            case "DOWN":
                if ($activeCellule[0] < 2) {
                    $activeCellule[0]++;
                }
                break;
            case "RIGHT":
                if ($activeCellule[1] < 2) {
                    $activeCellule[1]++;
                }
                break;
            case "LEFT":
                if ($activeCellule[1] >= 1) {
                    $activeCellule[1]--;
                }
                break;
            case "ENTER":
            case "SPACE":
                if ($state[$activeCellule[0]][$activeCellule[1]] == '') {
                    $state[$activeCellule[0]][$activeCellule[1]] = $play;
                    if ($play == 'X') {
                        $play = 'O';
                    } else {
                        $play = 'X';
                    }
                }
                break;
        }
    }
}


function isWinState($state)
{
    foreach (['X', 'O'] as $play) {
        foreach ($state as $x => $line) {
            if ($state[$x][0] == $play && $state[$x][1] == $play && $state[$x][2] == $play) {
                die($play . ' wins');
            }

            foreach ($line as $y => $item) {
                if ($state[0][$y] == $play && $state[1][$y] == $play && $state[2][$y] == $play) {
                    die($play . ' wins');
                }
            }
        }
        if ($state[0][0] == $play && $state[1][1] == $play && $state[2][2] == $play) {
            die($play . ' wins');
        }
        if ($state[2][0] == $play && $state[1][1] == $play && $state[0][2] == $play) {
            die($play . ' wins');
        }
    }

    $blankQuares = 0;
    foreach ($state as $x => $line) {
        foreach ($line as $y => $item) {
            if ($state[$x][$y] == '') {
                $blankQuares++;
            }
        }
    }
    if ($blankQuares == 0) {
        die('DRAW!');
    }
}


$stdin = fopen('php://stdin', 'r');
stream_set_blocking($stdin, 0);
system('stty cbreak -echo');

while (1) {
    system('clear');
    move($stdin, $state, $activeCellule, $play);
    echo renderGame($state, $activeCellule, $play);
    isWinState($state);
}
