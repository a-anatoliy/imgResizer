<?php
/**
 * Created by PhpStorm.
 * User: Tolya
 * Date: 07.01.2018
 * Time: 0:34
 * how to run it:
 * $ php MainImageResize.php '../Emeli/fabric/1/Gmail8'
 */

$split='-------------------------------';
$outIMGquality = 60;
$resizeWidth   = 1024;

require 'SimpleImage.php';

// Ignore notices
error_reporting(E_ALL & ~E_NOTICE);

if(isset ($argv[1])) {
    $images_dir = $argv[1];
    printf("Working directory: [%s]\n%s\n",$images_dir,$split);
    if (!is_dir($images_dir)) { printf("Can't read directory [%s]\n",$images_dir); exit; }

    $image_files = get_files($images_dir);
    $total = count($image_files);

    if($total) {
        printf("Found [%d] files to proceed.\n",$total);

        $thumbs_dir = $images_dir.'/out';
        if (!is_dir($thumbs_dir)) { 
            printf("Creating output directory [%s]\n",$thumbs_dir);
            mkdir($thumbs_dir);
        }

        $index = 0; $count=1;
        foreach($image_files as $index => $file) {
            $index++;
            $thumbnail_image = $thumbs_dir.'/'.$file;
            if(!file_exists($thumbnail_image)) {
                $extension = get_file_extension($thumbnail_image);
                if($extension) {
                    $src=$images_dir.'/'.$file;
                    make_thumb($src,$thumbnail_image);
                    printf("[%d] from [%d]..done -> [%s]\n",$count, $total, $file);
                    // echo "$count $src -> $thumbnail_image \n";
                    $count++;
                }
            }
    
        }
        --$count; 
        printf("%s\n[%d] files were converted.",$split,$count);
    } else { echo "No images found in $images_dir directory.\n"; }
} else { echo "Working directory is not set.\n"; exit; }

exit;

/* function:  generates thumbnail */
function make_thumb($src,$dest) {
    try {
        // Create a new SimpleImage object
        $image = new \claviska\SimpleImage();

        // load file
        $image->fromFile($src);
        // img getMimeType
        $mime = $image->getMimeType();
        $w = $image->getWidth();

        // Manipulate it
        // $image->bestFit(200, 300)      // proportionally resize to fit inside a 250x400 box
        // $image->flip('x')              // flip horizontally
        // $image->colorize('DarkGreen')  // tint dark green
        // $image->sharpen()
        // $image->border('darkgray', 1)  // add a 2 pixel black border
        // $image->overlay('flag.png', 'bottom right') // add a watermark image
        // $image->toScreen();                         // output to the screen
        if ($w > 1000) {
            $image->autoOrient();            // adjust orientation based on exif data
            // $image->resize($resizeWidth); // 1365
            // $image->resize(1024);         // 1365
            $image->resize(800);             // 1067
        }
        $image->toFile($dest,$mime,$outIMGquality);
        // echo "mime type: ".$mime;
    } catch(Exception $err) {
        // Handle errors
        echo $err->getMessage();
    }
}

/* function:  returns files from dir */
function get_files($images_dir,$exts = array('jpg')) {
    $files = array();
    if($handle = opendir($images_dir)) {
        while(false !== ($file = readdir($handle))) {
            $extension = strtolower(get_file_extension($file));
            if($extension && in_array($extension,$exts)) {
                $files[] = $file;
            }
        }
        closedir($handle);
    } else { echo "Unable to read $images_dir directory!";  }
    return $files;
}

/* function:  returns a file's extension */
function get_file_extension($file_name) {
    return substr(strrchr($file_name,'.'),1);
}











