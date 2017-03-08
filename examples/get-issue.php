<?php
require __DIR__ . '/../vendor/autoload.php';

$domain = $argv[1];
$username = $argv[2];
$password = $argv[3];
$issueKey = $argv[4];

$jira = \Bitsbybit\Jira\Session::create($domain, [
    'ssl' => true
]);
try {
    $jira->login($username, $password);
}catch(\Exception $e){
    echo "MESSAGE: ".$e->getMessage();
}


$res = $jira->getIssue($issueKey);
print_r($res);