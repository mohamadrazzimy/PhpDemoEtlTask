<?php

// Extract data from CSV
function extractData($filename) {
    $data = [];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $data[] = $row;
        }
        fclose($handle);
    }
    return $data;
}

// Transform data into associative array and calculate average salary
function transformData($data) {
    $transformed = [];
    $totalSalary = 0;
    foreach ($data as $row) {
        $salary = (int)$row[4]; // Convert salary to integer
        $transformed[] = [
            'id' => $row[0],
            'name' => $row[1],
            'age' => $row[2],
            'email' => $row[3],
            'salary' => $salary
        ];
        $totalSalary += $salary;
    }
    $averageSalary = count($transformed) ? $totalSalary / count($transformed) : 0;
    return ['data' => $transformed, 'averageSalary' => $averageSalary];
}

// Load data and display results
function loadData($transformedData) {
    echo "<h1>People Data</h1><ul>";
    foreach ($transformedData['data'] as $person) {
        echo "<li>{$person['name']} ({$person['age']}) - {$person['email']} - \${$person['salary']}</li>";
    }
    echo "</ul>";
    echo "<h2>Average Salary: \$" . number_format($transformedData['averageSalary'], 2) . "</h2>";
}

// Main execution
$data = extractData('data.csv');
$transformedData = transformData($data);
loadData($transformedData);

