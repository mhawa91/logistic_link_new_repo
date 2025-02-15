<?php

class GraphManager 
{
    protected $baseUrl = null;

    protected $allUplaodFolders = null;

    protected $config  = null;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        $this->allUplaodFolders = glob(__DIR__ . '/uploads/*', GLOB_ONLYDIR);

        $this->config = json_decode(
            file_get_contents( __DIR__ . '/config.json')
        , true);
    }

    public function remove($file)
    {
        if ( is_null( $file ) ) return false;

        foreach ($this->allUplaodFolders as $key => $folderBySize) {
            $this->ForceDelete( $folderBySize . '/' . $file );
        }
        return true;
    }

    protected function ForceDelete ( $file )
    {
        if ( file_exists( $file ) && ! is_dir( $file ) ) {
            if ( ! unlink( $file ) ) {
                return $this->ForceDelete( $file );
            }
        }
    }

    public function src($size, $folder, $file)
    {
        if (empty($file)) return $this->defaultSrc($size, $folder);

        return $this->baseUrl 
        . 'graph/uploads/' 
        . $size 
        . '/' 
        . $file;
    }

    public function defaultSrc($size, $folder)
    {
        return $this->baseUrl 
        . 'graph/uploads/' 
        . $size . '/' 
        . $this->config['DEFAULT_FOLDERS_FILES'][$folder];
    }
}