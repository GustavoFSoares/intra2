<?php
use Symfony\Component\Yaml\Yaml;

// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

loadConfigurations();

function loadConfigurations() {
    $file = getFileConfig();

    $file = file_get_contents("$file/configuration.yml");
    $yaml = Yaml::parse($file);

    switch (getUser()) {
        case 'dev':
            $yaml = $yaml['DEVELOPER'];
            break;

        case 'homo':
            $yaml = $yaml['HOMOLOG'];
            break;

        case 'prod':
            $yaml = $yaml['PRODUCTION'];
            break;

        default:
            die('NO USER');
            break;
    }

    foreach ($yaml as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $key => $value) {
                define($key, $value);
            }
        } else {
            define($key, $value);
        }
    }

}

function getFileConfig(){
    return __DIR__ . "/../../config";
}

function getUser(){
    $file = getFileConfig();
    $user = file("$file/.env");
    return $user[0];
}