<?php

class Car {
    //Car Info
    public $make;
    public $model;
    public $year;

    // Constructor
    public function __construct($make, $model, $year) {
        $this->make = $make;
        $this->model = $model;
        $this->year = $year;
    }

    // Function to display car info
    public function displayInfo() {
        return "Make: " . $this->make . ", Model: " . $this->model . ", Year: " . $this->year;
    }
}