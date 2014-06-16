<?php
namespace Quina;

    /**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 2014/05/09
 * Time: 20:32
 */

class Quina extends Quina\Container{

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


    static public function setLogger($logger){
        if(is_callable($logger)){
            static::$logger = $logger;
        }else{
            throw new \Exception("uncallable logger injected");
        }
    }

    static public function version(){
        return static::getCoreConfig("version");
    }

    static public function getCoreConfig($key=null){
        $data = include __DIR__."/../../config/quina.php";
        return $data[$key];
    }


//    /**
//     * 配列からの生成
//     * @param $param
//     * @return static
//     */
//    static public function loadFromArray($param){
//        $start = microtime(true);
//        $obj = new static($param);
//        $obj->setMeta([
//            "type" => "array",
//            "path" => $param,
//            "start" => $start
//        ]);
//        return $obj;
//    }

    /**
     * 単純なYAMLの読み込み
     * @param $path フルパス or baseからの相対
     * @param string $ext
     * @return array [YAML展開結果,残りのドキュメント]
     * @throws \Exception
     */
//    static protected function loadYaml($path,$ext=".yml"){
//        $path = static::getConfigRequired("basePath").$path.$ext;
//        if(file_exists($path)){
//            $data = file_get_contents($path);
//            $data = preg_split("#^-{3}(.*)$#m",$data,2,PREG_SPLIT_NO_EMPTY);
//            list($yaml,$rest) = $data + ["",""];
//            $yaml = Yaml::parse($yaml);
//            foreach($yaml as $key => $value){
//                $yaml[$key] = static::parseParam($value);
//            }
//            if(isset($yaml["include"])){
//                $includeList = $yaml["include"];
//                $includeYaml = [];
//                $includeYamlKeys = [];
//                foreach($includeList as $include){
//                    $_yaml = static::loadYaml($include,"")[0];
//                    $includeYaml[] = $_yaml;
//                    $includeYamlKeys = array_merge($includeYamlKeys,array_keys($_yaml));
//                }
//                $includeYaml[] = $yaml;
//                $includeYamlKeys = array_merge($includeYamlKeys,array_keys($yaml));
//                unset($includeYamlKeys["include"]);
//                $yaml = call_user_func_array(function()use($includeYamlKeys){
//                    $rtn = [];
//                    $args = func_get_args();
//                    foreach($includeYamlKeys as $key){
//                        $_include = [];
//                        foreach($args as $_arg){
//                            $_include[] = isset($_arg[$key])?$_arg[$key]:[];
//                        }
//                        $rtn[$key] = call_user_func_array("array_merge",$_include);
//                    }
//                    return $rtn;
//                },$includeYaml);
//                unset($yaml["include"]);
//            }
//            return [$yaml,$rest];
//        }else{
//            throw new \Exception("file not found: $path");
//        }
//    }

    static public function parseParam($paramString){
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
}

class Exception extends \Exception{}