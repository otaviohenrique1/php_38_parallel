<?php

require_once 'vendor/autoload.php';

use Alura\Threads\Activity\Activity;
use Alura\Threads\Student\InMemoryStudentRepository;
use Alura\Threads\Student\Student;
use \parallel\Runtime;
use \parallel\Channel;

$repository = new InMemoryStudentRepository();
$studentList = $repository->all();

$totalPoints = 0;
$runtimes = [];
$futures = [];
$channel = Channel::make('points');
foreach ($studentList as $i => $student) {
  $activities = $repository->activitiesInADay($student);
  $runtimes[$i] = new Runtime(__DIR__ . 'vendor/autoload.php');

  $futures[$i] = $runtimes[$i]->run(function($activities, Student $student, Channel $channel) {
    $points = array_reduce(
      $activities,
      fn(int $total, Activity $activity) => $total + $activity->points(),
      0
    );
    $channel->send($points);
    printf('%s made %d points today %s', $student->fullName(), $points, PHP_EOL);
    return $points;
  }, [$activities, $student, $channel]);
}

$totalPointsWithChannel = 0;
for ($i=0; $i < count($studentList); $i++) { 
  $totalPointsWithChannel += $channel->recv();
}
$channel->close();
foreach ($futures as $future) {
  $totalPoints += $future->value();
}

printf('We had a total of %d points today %s', $totalPointsWithChannel, PHP_EOL);
printf('We had a total of %d points today %s', $totalPoints, PHP_EOL);
