<?php

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class S3GraphManager 
{
    protected $baseUrl = null;

    protected $allUplaodFolders = null;

    protected $config  = null;

    protected $diskConfig = null;

    protected $diskName   = null;

    public function __construct(string $disk, string $baseUrl, array $diskConfig = [])
    {
        $this->baseUrl    = $baseUrl;

        $this->diskConfig = $diskConfig;

        $this->diskName   = $disk;

        $this->config = json_decode(
            file_get_contents( __DIR__ . '/config.json')
        , true);

        $this->allUplaodFolders = array_keys($this->config['DEFAULT_FOLDERS_FILES']);
    }

    public function process($folder, $localTempFile)
    {
        ini_set('memory_limit','-1');

        $sizes = $this->getFolderSupportedSizes($folder);
        $path  = $this->getUploadFolder();
        $NAME = $localTempFile->hashName();

        $originalFile = Storage::disk($this->diskName)->put($path . 'original/' . $folder, $localTempFile, 'public');

        foreach ($sizes as $key => $size) {
            $WIDTH_HEIGHT = explode('x', $size);
            $FILE = Image::make($localTempFile)->fit($WIDTH_HEIGHT[0], $WIDTH_HEIGHT[1]);
            Storage::disk($this->diskName)->put($path . $size .'/' . $folder .'/' . $NAME, $FILE->stream(), 'public');
        }

        return $NAME;
    }

    public function getFolderSupportedSizes($folder)
    {
        return $this->config['SUPPORTED_FOLDERS_RATIOS'][$folder] ?? [];
    }

    public function getUploadFolder()
    {
        return $this->diskConfig['folder'] ? $this->diskConfig['folder'] . '/' : '/';
    }

    public function remove($file)
    {
        if ( is_null( $file ) ) return false;

        foreach ($this->allUplaodFolders as $key => $folderBySize) {
            Storage::disk($this->diskName)->delete($this->filePath('original', $folderBySize, $file));
            foreach ($this->getFolderSupportedSizes($folderBySize) as $key => $size) {
                Storage::disk($this->diskName)->delete($this->filePath($size, $folderBySize, $file));
            }
        }
        return true;
    }

    public function filePath($size, $folder, $file)
    {
        return $this->getUploadFolder() 
        . $size
        . '/' 
        . $folder 
        . '/' 
        . $file;
    }

    public function src($size, $folder, $file)
    {
        if (empty($file) && $file !== '-SQL-') return $this->defaultSrc($size, $folder);

        $file = str_replace('-SQL-', '', $file);

        return $this->baseUrl 
        . '/'
        . $this->filePath($size, $folder, $file);
    }

    public function defaultSrc($size, $folder)
    {
        return $this->baseUrl 
        . '/'
        . $this->getUploadFolder() 
        . $size
        . '/' 
        . $this->config['DEFAULT_FOLDERS_FILES'][$folder];

        // return $this->baseUrl 
        // . 'graph/uploads/' 
        // . $size . '/' 
        // . $this->config['DEFAULT_FOLDERS_FILES'][$folder];
    }
}