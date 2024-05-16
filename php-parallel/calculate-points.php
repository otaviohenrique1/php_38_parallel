<?php

require_once 'vendor/autoload.php';

use Alura\Threads\Activity\Activity;
use Alura\Threads\Student\InMemoryStudentRepository;

$repository = new InMemoryStudentRepository();
$studentList = $repository->all();

$totalPoints = 0;
foreach ($studentList as $student) {
  $activities = $repository->activitiesInADay($student);
  $totalPoints += $points = array_reduce(
    $activities,
    fn(int $total, Activity $activity) => $total + $activity->points(),
    0
  );
  printf('%s made %d points today %s', $student->fullName(), $points, PHP_EOL);
}

printf('We had a total of %d points today %s', $totalPoints, PHP_EOL);
