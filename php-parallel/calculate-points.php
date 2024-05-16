<?php

require_once 'vendor/autoload.php';

use Alura\Threads\Activity\Activity;
use Alura\Threads\Student\InMemoryStudentRepository;
use Alura\Threads\Student\Student;
use \parallel\Runtime;

$repository = new InMemoryStudentRepository();
$studentList = $repository->all();

$totalPoints = 0;
$runtimes = [];
$futures = [];
foreach ($studentList as $i => $student) {
  $activities = $repository->activitiesInADay($student);
  $runtimes[$i] = new Runtime(__DIR__ . 'vendor/autoload.php');

  $futures[$i] = $runtimes[$i]->run(function($activities, Student $student) {
    $points = array_reduce(
      $activities,
      fn(int $total, Activity $activity) => $total + $activity->points(),
      0
    );
    printf('%s made %d points today %s', $student->fullName(), $points, PHP_EOL);
    return $points;
  }, [$activities, $student]);
}
foreach ($futures as $future) {
  $totalPoints += $future->value();
}

printf('We had a total of %d points today %s', $totalPoints, PHP_EOL);
