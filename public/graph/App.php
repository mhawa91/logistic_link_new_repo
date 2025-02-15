<?php
    ini_set('memory_limit','256M');

    $Config = json_decode(
        file_get_contents( __DIR__ . '/config.json')
    , true);

    $Data = [
        'WIDTH'                  => $_GET['width'],
        'HEIGHT'                 => $_GET['height'],
        'SIZING_FOLDER'          => null,
        'STORING_FOLDER'         => null,
        'FILE_TO_SAVE'           => $_GET['file'],
        'REQUESTED_FILE'         => __DIR__ . '/uploads/original/' . $_GET['file'],
        'PARSE_FILE'             => pathinfo($_GET['file']),
        'DIR_TO_MAKE'            => false,
        'DEFAULT_FILE_TO_RETURN' => false,
    ];
    $Data['SIZING_FOLDER']  = $Data['WIDTH']  .  'x'  .       $Data['HEIGHT'];
    $Data['STORING_FOLDER'] = __DIR__ . '/uploads/'   .       $Data['SIZING_FOLDER'];
    $Data['FILE_TO_SAVE']   = $Data['STORING_FOLDER'] . '/' . $Data['FILE_TO_SAVE'];
    $Data['DIR_TO_MAKE']    = $Data['PARSE_FILE']['dirname'] == '.' 
    ? $Data['STORING_FOLDER'] 
    : $Data['STORING_FOLDER'] . '/' . $Data['PARSE_FILE']['dirname'];

    if (isset( $Config['SUPPORTED_FOLDERS_RATIOS'][ $Data['PARSE_FILE']['dirname'] ] )) {
        if(! in_array($Data['SIZING_FOLDER'], $Config['SUPPORTED_FOLDERS_RATIOS'][ $Data['PARSE_FILE']['dirname'] ])){
            echo 'Bad Request';
            exit;
        }
    } 
    else if(! in_array($Data['SIZING_FOLDER'], $Config['SUPPORTED_RATIOS'])){
        echo 'Bad Request';
        exit;
    }

    if(! file_exists( $Data['REQUESTED_FILE'] ) ) {
        if (isset( $Config['DEFAULT_FOLDERS_FILES'][ $Data['PARSE_FILE']['dirname'] ] )) {
            $Data['FILE_TO_SAVE'] = _DIR_ . '/' . $Config['SUPPORTED_FOLDERS_RATIOS'][ $Data['PARSE_FILE']['dirname'] ];
        } 
        // else if (isset( $Config['SUPPORTED_RATIOS'][ $Data['SIZING_FOLDER'] ] )) {
        //     $Data['FILE_TO_SAVE'] = _DIR_ . '/' . $Config['SUPPORTED_FOLDERS_RATIOS'][ $Data['PARSE_FILE']['dirname'] ];
        // }
        else {
            echo 'Bad Request';
            exit;
        }
    }

    function ApplicationExecute ($Data)
    {
        require (__DIR__ . '/vendor/autoload.php');

        if (! is_dir( $Data['DIR_TO_MAKE'] ) ) {
            mkdir( $Data['DIR_TO_MAKE'], 0755, true );
        }
        try {
            \Intervention\Image\ImageManagerStatic::configure(['driver' => 'gd']);
            // \Intervention\Image\ImageManagerStatic::make( $Data['REQUESTED_FILE'] )->orientate()->save( $Data['REQUESTED_FILE'] );
            echo 
            \Intervention\Image\ImageManagerStatic::make( $Data['REQUESTED_FILE'] )
            ->fit($Data['WIDTH'], $Data['HEIGHT'])
            ->save( $Data['FILE_TO_SAVE'] )
            ->response();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    ApplicationExecute( $Data );