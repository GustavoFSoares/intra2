<?php
namespace HospitalApi;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\Tools\Setup;
class BasicApplicationAbstract
{

    private static $_app;
    
    public function __construct($app) {
        if (isset(self::$_app)) {
            return $this;
        }
        self::$_app = $app;
    }

    public function isDebug(){
        return DEBUG_ACTIVE;
    }

    public function getEmailDefault(){
        return [
            'name' => EMAIL_DEFAULT_NAME,
            'email' => EMAIL_DEFAULT_EMAIL,
            'password' => EMAIL_DEFAULT_PASSWORD,
            'host' => EMAIL_DEFAULT_HOST,
        ];
    }
    
    public function getDebugEmail(){
        return DEBUG_EMAIL;
    }

    public function ADAllowed(){
        return AD;
    }

    public function getApp() {
        return self::$_app;
    }

    public function getContainer() {
        return self::$_app->getContainer();
    }

    public function getEntityManager() {
        return self::$_app->getContainer()['em'];
    }
    
    public function getSession() {
        return self::$_app->getContainer()['session']->get();
    }

    public function getSpecialDataPermissions() {
       
        $file = file_get_contents(PATH.'/HospitalApi/SpecialDataPermissions.yml');
        $yaml = Yaml::parse($file);
        
        return $yaml;
    }

    public function isProduction() {
        $env = file_get_contents( getFileConfig()."/.env" );

        if($env == 'prod' || $env == 'homo') {
            return true;
        } else {
            return false;
        }
    }

    public static function loadDoctrineConfigurations($path, $devMode) {
        $config = Setup::createAnnotationMetadataConfiguration($path, $devMode);

        $file = file_get_contents(PATH . '/HospitalApi/ExtensionFunctionsDoctrine.yml');
        $yaml = Yaml::parse($file);

        $functions = $yaml['FUNCTIONS'];
        $sgbdFunctions = $yaml[strtoupper(DB_SYSTEM)];
        
        foreach ($functions as $key => $addFunction) {
            foreach ($sgbdFunctions[$key] as $key => $class) {
                $config->$addFunction($key, $class);
            }
        }
        // $config->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        // $config->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        return $config;
    }

}