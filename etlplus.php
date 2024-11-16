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

    // Apply transformations
    $transformed = standardizeEmailDomain($transformed);
    $transformed = normalizeNames($transformed);
    $transformed = addAgeGroup($transformed);
    $transformed = convertSalaryToThousands($transformed);

    $averageSalary = count($transformed) ? $totalSalary / count($transformed) : 0;
    return ['data' => $transformed, 'averageSalary' => $averageSalary];
}

// Load data and display results
function loadData($transformedData) {
    echo "<h1>People Data</h1><ul>";
    foreach ($transformedData['data'] as $person) {
        echo "<li>{$person['name']} ({$person['age']}) - {$person['email']} - \${$person['salary']} - Age Group: {$person['age_group']}</li>";
    }
    echo "</ul>";
    echo "<h2>Average Salary: \$" . number_format($transformedData['averageSalary'], 2) . "</h2>";
}

// Transformation functions
function standardizeEmailDomain($data) {
    return array_map(function ($person) {
        $emailParts = explode('@', $person['email']);
        if (isset($emailParts[1]) && is_string($emailParts[1])) {
            $emailParts[1] = strtolower($emailParts[1]);
        }
        $person['email'] = implode('@', $emailParts);
        return $person;
    }, $data);
}

function normalizeNames($data) {
    return array_map(function ($person) {
        $person['name'] = ucwords(strtolower($person['name']));
        return $person;
    }, $data);
}

function addAgeGroup($data) {
    return array_map(function ($person) {
        if ($person['age'] < 30) {
            $person['age_group'] = 'Young';
        } elseif ($person['age'] <= 40) {
            $person['age_group'] = 'Middle-aged';
        } else {
            $person['age_group'] = 'Old';
        }
        return $person;
    }, $data);
}

function convertSalaryToThousands($data) {
    return array_map(function ($person) {
        $person['salary'] = $person['salary'] / 1000 . 'K';
        return $person;
    }, $data);
}

// Main execution
$data = extractData('data.csv');
$transformedData = transformData($data);
loadData($transformedData);

