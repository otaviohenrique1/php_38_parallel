<?php

require_once 'vendor/autoload.php';

use Alura\Threads\Student\InMemoryStudentRepository;
use Alura\Threads\Student\Student;
use \parallel\Runtime;

$repository = new InMemoryStudentRepository();
$studentList = $repository->all();

$runtimes = [];

foreach ($studentList as $i => $student) {
  echo 'Resizing ' . $student->fullName() . ' profile picture' . PHP_EOL;

  $runtimes[$i] = new Runtime(__DIR__ . 'vendor/autoload.php');

  $runtimes[$i]->run(function (Student $student) {
    $profilePicturePath = $student->profilePicturePath();
  
    [$width, $height] = getimagesize($profilePicturePath);

    $ratio = $height / $width;

    $newWidth = 200;
    $newHeight = 200 * $ratio;

    $sourceImage = imagecreatefromjpeg($profilePicturePath);
    $destinationImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($destinationImage, $sourceImage,0,0,0,0, $newWidth, $newHeight, $width, $height);

    imagejpeg($destinationImage, __DIR__ . '/storage/resized/' . basename($profilePicturePath));
  }, [$student]);

  // resizeTo200PixelsWidth($student->profilePicturePath());
  echo 'Finished resizing ' . $student->fullName() . ' profile picture' . PHP_EOL;
}

foreach ($runtimes as $runtime) {
  $runtime->close();
}

echo 'Finalizando a thread principal' . PHP_EOL;


// function resizeTo200PixelsWidth($imagePath) {
//   [$width, $height] = getimagesize($imagePath);

//   $ratio = $height / $width;

//   $newWidth = 200;
//   $newHeight = 200 * $ratio;

//   $sourceImage = imagecreatefromjpeg($imagePath);
//   $destinationImage = imagecreatetruecolor($newWidth, $newHeight);
//   imagecopyresampled($destinationImage, $sourceImage,0,0,0,0, $newWidth, $newHeight, $width, $height);

//   imagejpeg($destinationImage, __DIR__ . '/storage/resized/' . basename($imagePath));
// }
