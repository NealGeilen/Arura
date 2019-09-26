<?php


use NG\Settings\Application;
use NG\Settings\System;

require_once __DIR__ . '/_app/autoload.php';

if (!isset($_GET['sat']) && $_GET['sat'] !== '324792834792374'){
    header('Location: /');
    exit;
}
$commands = [
    'git reset --hard',
    'git pull',
    'php composer.phar update'
];
// Run the commands for output
$output = '';
foreach($commands as $command){
    // Run it
    $tmp = shell_exec($command);
    // Output
    $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>" . '<br/>';
    $output .= htmlentities(trim($tmp)) . "<br/>";
}
\NG\Mailer\Notify::Notify([
    'message' => $output,
    'recipients' => [System::get('email', 'webmaster')],
    'subject' => 'Arura ' . Application::get('webiste', 'name').' pull notification'
]);
exit;
?>
