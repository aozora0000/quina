<?php
namespace Quina;
use Symfony\Component\Yaml\Yaml;

    /**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/09
 * Time: 20:32
 */
//
//if ( ! function_exists('spyc_load'))
//{
//    import('spyc/spyc', 'vendor');
//}

class Quina {

    /**
     * @var \Quina\Config
     */
    static protected $config = null;

    /**
     * @var \Callable
     */
    static protected $logger = null;

    public static function __callStatic($name, $arguments)
    {
        $class = static::loadModule($name);
        return call_user_func_array($class."::callStatic",$arguments);
    }


    #region Config DI Section

    static public function init($configClass,$sitename=null){
        if((is_object($configClass)) && ($configClass instanceof Config))
        {
            static::$config = $configClass;
        }elseif((is_string($configClass))&& (is_subclass_of($configClass,"\\Quina\\Config")))
        {
            static::$config = $configClass::init($sitename);
        }else{
            throw new Exception("invalid argument");
        }
    }

    static public function getConfig($key,$default=null){
        if(static::$config){
            return static::$config->getItem($key,$default);
        }else{
            throw new Exception("call init method before use Quina");
        }
    }



    static public function getConfigRequired($key){
        $salt = sha1(mt_rand(10000000,90000000));
        $value = static::getConfig($key,$salt);
        if($value === $salt){
            throw new Exception("required config $key not set");
        }
        return $value;
    }

    #endregion

    #region Logger DI Section

    static public function setLogger($logger){
        if(is_callable($logger)){
            static::$logger = $logger;
        }else{
            throw new \Exception("uncallable logger injected");
        }
    }

    static public function log(){
        if(static::$logger){
            $callable = static::$logger;
            $callable(func_get_args());
        }
    }

    #endregion

    static public function version(){
        return static::getCoreConfig("version");
    }

    static public function getCoreConfig($key=null){
        $data = include __DIR__."/../../config/quina.php";
        return $data[$key];
    }

    /**
     * Yamlを読み込む
     * @param $path
     * @return static
     */
    static public function loadFromYaml($path){
        $start = microtime(true);
        list($yaml,$rest) = static::loadYaml($path);
        $obj = new static($yaml,$rest);
        $basePath = static::getConfigRequired("basePath");
        $obj->setMeta([
            "key" => $path,
            "type" => "yaml",
            "path" => $basePath.$path.".yml",
            "updatedAt" => filemtime($basePath.$path.".yml"),
            "createdAt" => filectime($basePath.$path.".yml"),
            "start" => $start
        ]);
        return $obj;
    }

    /**
     * 配列からの生成
     * @param $param
     * @return static
     */
    static public function loadFromArray($param){
        $start = microtime(true);
        $obj = new static($param);
        $obj->setMeta([
            "type" => "array",
            "path" => $param,
            "start" => $start
        ]);
        return $obj;
    }

    /**
     * 単純なYAMLの読み込み
     * @param $path フルパス or baseからの相対
     * @param string $ext
     * @return array [YAML展開結果,残りのドキュメント]
     * @throws \Exception
     */
    static protected function loadYaml($path,$ext=".yml"){
        $path = static::getConfigRequired("basePath").$path.$ext;
        if(file_exists($path)){
            $data = file_get_contents($path);
            $data = preg_split("#^-{3}(.*)$#m",$data,2,PREG_SPLIT_NO_EMPTY);
            list($yaml,$rest) = $data + ["",""];
            $yaml = Yaml::parse($yaml);
            foreach($yaml as $key => $value){
                $yaml[$key] = static::parseParam($value);
            }
            if(isset($yaml["include"])){
                $includeList = $yaml["include"];
                $includeYaml = [];
                $includeYamlKeys = [];
                foreach($includeList as $include){
                    $_yaml = static::loadYaml($include,"")[0];
                    $includeYaml[] = $_yaml;
                    $includeYamlKeys = array_merge($includeYamlKeys,array_keys($_yaml));
                }
                $includeYaml[] = $yaml;
                $includeYamlKeys = array_merge($includeYamlKeys,array_keys($yaml));
                unset($includeYamlKeys["include"]);
                $yaml = call_user_func_array(function()use($includeYamlKeys){
                    $rtn = [];
                    $args = func_get_args();
                    foreach($includeYamlKeys as $key){
                        $_include = [];
                        foreach($args as $_arg){
                            $_include[] = isset($_arg[$key])?$_arg[$key]:[];
                        }
                        $rtn[$key] = call_user_func_array("array_merge",$_include);
                    }
                    return $rtn;
                },$includeYaml);
                unset($yaml["include"]);
            }
            return [$yaml,$rest];
        }else{
            throw new \Exception("file not found: $path");
        }
    }

    static protected function parseParam($paramString){
        if(is_array($paramString)){
            return $paramString;
        }
        $rtn = [];
        $params = array_filter(explode(" ",$paramString));
        foreach($params as $p){
            if(($p = explode("=",$p)) && count($p) === 2){
                $rtn[$p[0]] = $p[1];
            }else{
                $rtn[]= $p[0];
            }
        }
        return $rtn;
    }

    /**
     * モジュールのクラス名を返す。
     * @param $className
     * @return string
     * @throws \Exception
     */
    static protected function loadModule($moduleName){
        $className = null;
        $moduleMap = array_merge(static::getCoreConfig("moduleMap"),static::getConfig("moduleMap",[]));
        if(isset($moduleMap[$moduleName])){
            $className = $moduleMap[$moduleName];
        }elseif(class_exists(ucfirst($moduleName))){
            $className = ucfirst($moduleName);
        }else{
            foreach(static::getConfig("moduleNS",[]) as $ns){
                if(class_exists("{$ns}$moduleName")){
                    $className = "{$ns}$moduleName";
                    break;
                }
            }
        }

//        if($moduleName==="apple"){
//            var_dump($className);
//            exit;
//        }

        if($className && is_subclass_of($className,"\\Quina\\Driver")){
            return $className;
        }else{
            throw new \Exception("module $moduleName not found");
        }
    }


    public $modules = [];

    protected $restContent = null;

    public function __construct($param,$rest=null){
        $this->restContent = $rest;

//        foreach(static::getConfig("before",[]) as $moduleName){
//            if(isset($param[$moduleName])){
//                $this->registerModule($moduleName,static::parseParam($param[$moduleName]));
//                unset($param[$moduleName]);
//            }
//        }
        foreach($param as $moduleName => $p){
            $this->registerModule($moduleName,static::parseParam($p));
        }
    }

    public function registerModule($moduleName,$params){
        $moduleClass = static::loadModule($moduleName);
        $this->modules[$moduleName] = $moduleClass::loadModule($this,$params);
    }

    public function setMeta($param){
        $this->registerModule("meta",$param);
    }

    public function __get($name){
        return $this->get($name);
    }

    public function __set($name,$value){
        return $this->set($name,$value);
    }

    function __call($name, $arguments)
    {
        try{
            if( ($module = $this->getModule($name)) && is_callable($module)){
                return call_user_func_array($module,$arguments);
            }else{
            }

        }catch (\Exception $e){

        }
        // TODO: Implement __call() method.
    }


    public function get($name){
        $vars = $this->getModule("vars");
        return $vars->$name;
    }


    public function set($name,$value){
        $vars = $this->getModule("vars");
        return $vars->$name = $value;
    }

    public function getModule($name){
        if(isset($this->modules[$name])){
            return $this->modules[$name];
        }else{
            throw new \Exception("the module $name dont exist");
        }
    }

    public function getRestContent(){
        return $this->restContent;
    }

    public function setRestContent($data){
        $this->restContent = $data;
    }

//    public function url($segment=""){
//        $url = static::getConfig("url");
//        if($url instanceof \Closure){
//            $url = $url($this);
//        }
//        $segment = $segment?:$this->meta["key"];
//        return $url . $segment;
//    }

    public function toArray($includeModules = true){
        $vars = $this->getModule("vars")->toArray();

        if($includeModules){
            $modules = [];
            foreach($this->modules as $moduleName => $moduleObject){
                if(!$moduleObject->arrayFilter()){
                    $modules[$moduleName] = $moduleObject->getParams();
                }
            }
            $vars["_module"] = $modules;
        }

        return $vars;
    }
}

class Exception extends \Exception{}