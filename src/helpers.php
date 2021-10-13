<?php


use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

if (! function_exists('getModels')){
    function getModels($modelNamespace = null): Collection
    {
        $modelNamespace = $modelNamespace ?? 'Models';
        $appNamespace = Container::getInstance()
            ->getNamespace();

        return collect(File::allFiles(app_path($modelNamespace)))
            ->map(function ($item) use ($appNamespace, $modelNamespace) {
            $rel   = $item->getRelativePathName();
            $class = sprintf('\%s%s%s', $appNamespace, $modelNamespace ? $modelNamespace . '\\' : '',
                implode('\\', explode('/', substr($rel, 0, strrpos($rel, '.')))));
            return class_exists($class) ? $class : null;
        })->filter();
    }
}
