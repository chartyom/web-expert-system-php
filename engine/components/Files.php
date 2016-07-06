<?php
namespace engine\components;

class Files {

    /**
     * Полностью удаляет всю директорию вместе со всеми файлами
     *
     * @param $dir
     */
    public static function removeDirectory($dir) {
        if ($objs = glob($dir.'/*')) {
            foreach($objs as $obj) {
                is_dir($obj) ? self::removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    /**
     * Полностью удаляет всё, что находится внутри директории
     *
     * Зависимость removeDirectory($dir)
     *
     * @param $dir
     */
    public static function removeDirectoryInner($dir) {
        if ($objs = glob($dir.'/*')) {
            foreach($objs as $obj) {
                is_dir($obj) ? self::removeDirectory($obj) : unlink($obj);
            }
        }
    }


    /**
     *
     * Создает дирректории по указанному пути
     *
     * @param $dir
     *
     * @return bool
     */
    public static function createDirectory($dir){
        if(!file_exists($dir)){
           return mkdir($dir, 0777, true);
        }
        return false;
    }

    /**
     * Копирует всю внутри директории dir в newdir
     *
     * @param $dir
     */
    public static function copyDirectory($dir,$newdir){
        self::createDirectory($newdir);
        if ($objs = glob($dir.'/*')) {
            foreach($objs as $obj) {
                if(is_dir($obj)){
                    $dirname = basename($obj);
                    self::copyDirectory($obj,$newdir.'/'.$dirname);
                }else{
                    $filename = basename($obj);
                    copy($obj, $newdir . '/' . $filename);
                }
            }
        }
    }

    /**
     * Копирует всё что внутри директории dir в newdir
     *
     * @param $dir
     */
    public static function copyDirectoryInner($dir,$newdir){

        if ($objs = glob($dir.'/*')) {
            foreach($objs as $obj) {
                if(is_dir($obj)){
                    $dirname = basename($obj);
                    self::copyDirectory($obj,$newdir.'/'.$dirname);
                }else{
                    $filename = basename($obj);
                    if(file_exists($newdir)){
                        copy($obj, $newdir.'/'.$filename);
                    }
                }
            }
        }
    }

}