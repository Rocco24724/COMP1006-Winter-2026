<?php
declare(strict_types=1);

//inline Comment

/*
multi-line Comment
*/

//3. Variables, Data Tyoes, Concatenation, Conditional Statements & Echo

$firstName = "Rocco";
$lastName = "Minetola";
$age = 20;
$bool = true;

echo "<p> Hello, my name is " . $firstName . " " . $lastName . "</p>";

if ($bool) {
    echo "<p> I am True </p>";
}
else {
    echo "<p> I am False </p>";
}

//4. Loosely Typed Language Demo

$num1 = 1;
$num2 = 10;

function add(int $num1, int $num2) : int {
    return $num1 + $num2;
}

echo "<p>" . add($num1, $num2) . "</p>";

//5. Strict Types & Types Hints


//6. OOP with PHP 
