<?php

include './vendor/autoload.php';


$filename = './data/source.txt';
$source = file_get_contents($filename);

$summarizer = new Summarizer\Summarizer();
$sentences = $summarizer->summarize($source);

header('Content-Type', 'application/json');
echo json_encode($sentences);
