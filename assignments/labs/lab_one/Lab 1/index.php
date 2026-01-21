<?php 
// Makes it need connect.php to be able to run
require "connect.php";

require "header.php"; 
// Makes it need car.php to be able to run
require "car.php";

// Creating car object of my own car (Listing make, model, and year to use later for constructor)
$car = new Car("Acura", "MDX", 2011);

// Outputting my car info
echo $car->displayInfo();

require "footer.php"; 

/* 

Easy Parts: Creating the car object/constructor was familiar since last semester we did something similar in C++.

Hard Parts: Getting used to using the "$" sign to make a variable and in index I tried to do echo $car without the displayInfo() and I was stuck on that for a bit.

*/

