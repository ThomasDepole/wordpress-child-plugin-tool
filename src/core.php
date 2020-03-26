<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//determine the paths we need to work with
$currentDir = dirname(__FILE__);
$parentPluginDir = str_replace("-child/src", "", $currentDir);
$childPluginDir = str_replace("/src", "", $currentDir);
$modifiedDir = str_replace("/src", "/modifications", $currentDir);
$originalDir = str_replace("/src", "/original", $currentDir);
$removeDir = str_replace("/src", "/remove", $currentDir);
$newfilesDir = str_replace("/src", "/src/cache/newFiles", $currentDir);

//First delete the delete_me.txt files
if(file_exists($childPluginDir . "/modifications/delete_me.txt"))
    unlink($childPluginDir . "/modifications/delete_me.txt");
if(file_exists($childPluginDir . "/original/delete_me.txt"))
    unlink($childPluginDir . "/original/delete_me.txt");
if(file_exists($childPluginDir . "/src/cache/newFiles/delete_me.txt"))
    unlink($childPluginDir . "/src/cache/newFiles/delete_me.txt");

//make sure the core directories exists
if(!is_dir($childPluginDir . "/original"))
    mkdir($childPluginDir . "/original", 0777, true);
if(!is_dir($childPluginDir . "/modifications"))
    mkdir($childPluginDir . "/modifications", 0777, true);
if(!is_dir($childPluginDir . "/src/cache/newFiles"))
    mkdir($childPluginDir . "/src/cache/newFiles", 0777, true);
if(!is_dir($childPluginDir . "/remove"))
    mkdir($childPluginDir . "/remove", 0777, true);

//determine folder positions for cleaner path outputs
$currentPaths = explode("/", $currentDir);
$pluginFolder = $currentPaths[count($currentPaths) - 3];
$pluginFolderPos = strpos($currentDir, $pluginFolder);
$pluginFolderPos = $pluginFolderPos + strlen($pluginFolder);

//output result model
class OutputModel
{
  //set this to "apply" or "revert"
  public $operation;
  //store a list of OuputItemModel for modified items
  public $modfiedItems;
  //store a list of OuputItemModel for original files
  public $originalItems;
}
class OuputItemModel
{
  //full path for file
  public $fullPath;
  //message to display on the page
  public $msg;
  //status color. "black", "red", "green"
  public $color;
  //helper functions
  function get_filename(){
    return basename($fullPath);
  }

  function get_shortpath(){
    global $pluginFolderPos;
    return substr($this->fullPath, $pluginFolderPos);
  }
}
$output = new OutputModel();
$output->modfiedItems = [];
$output->originalItems = [];

//core functions 
function get_file_list($dir, $recurse = FALSE)
  {
    $retval = [];

    // add trailing slash if missing
    if(substr($dir, -1) != "/") {
      $dir .= "/";
    }

    // open pointer to directory and read list of files
    $d = @dir($dir) or die("getFileList: Failed opening directory {$dir} for reading");
    while(FALSE !== ($entry = $d->read())) {
      // skip hidden files
      if($entry{0} == ".") continue;
      if(is_dir("{$dir}{$entry}")) {
        $retval[] = [
          'name' => "{$dir}{$entry}/",
          'type' => filetype("{$dir}{$entry}"),
          'size' => 0,
          'lastmod' => filemtime("{$dir}{$entry}")
        ];
        if($recurse && is_readable("{$dir}{$entry}/")) {
          $retval = array_merge($retval, get_file_list("{$dir}{$entry}/", TRUE));
        }
      } elseif(is_readable("{$dir}{$entry}")) {
        $retval[] = [
          'name' => "{$dir}{$entry}",
          'type' => mime_content_type("{$dir}{$entry}"),
          'size' => filesize("{$dir}{$entry}"),
          'lastmod' => filemtime("{$dir}{$entry}")
        ];
      }
    }
    $d->close();

    return $retval;
  }

function remove_server_path($path){
  global $pluginFolderPos;
  return substr($path, $pluginFolderPos);
}

function revert($enableLogging = false){
  global $originalDir, $parentPluginDir, $newfilesDir, $output, $removeDir;

  //loop through the files we modfied and apply those changes to the plugin directory
  foreach(get_file_list($originalDir, true) as $file){
    //ignore directory listing
    if($file['type'] == 'dir')
        continue;
    
    //define the working files
    $modFile = $file['name'];
    $oldFile = str_replace("-child/original", "", $modFile);
    $originalFile = str_replace($parentPluginDir, $originalDir, $oldFile);
    //stub out the ouput item models
    $originalOutput = new OuputItemModel();
    $originalOutput->fullPath = $originalFile;
    $modifiedOuput = new OuputItemModel();
    $modifiedOuput->fullPath = $oldFile;

    //copy the original file back to the plugin
    copy($originalFile, $oldFile);
    $modifiedOuput->msg = "File reverted in parent directory";
  
    //delete the original file
    unlink($originalFile);
    $originalOutput->msg = "File deleted";

    //update ouput model
    if($enableLogging){
      array_push($output->modfiedItems, $modifiedOuput);
      array_push($output->originalItems, $originalOutput);
    }
  }

  //loop through the files that were added and remove them from the parent plugin
  foreach(get_file_list($newfilesDir, true) as $file){
    //ignore directory listing
    if($file['type'] == 'dir')
        continue;
    
    //define the working files
    $newFile = $file['name'];
    $oldFile = str_replace("-child/src/cache/newFiles", "", $newFile);
    $modFile = str_replace("-child/src/cache/newFiles", "-child/modifications", $newFile);
    
    $modifiedOuput = new OuputItemModel();
    $modifiedOuput->fullPath = $oldFile;

    //copy the original file back to the plugin
    unlink($oldFile);
    //delete the new file from the newFiles cache
    unlink($newFile);
    $modifiedOuput->msg = "File removed from parent directory";
  
    //update ouput model
    if($enableLogging)
      array_push($output->modfiedItems, $modifiedOuput);
  }

  //clear cache and original files justs in case
  /*
  array_map('unlink', glob("$originalDir/*.*"));
  array_map('unlink', glob("$newfilesDir/*.*"));
  rmdir($originalDir);
  rmdir($newfilesDir);
  mkdir($originalDir, 0777);
  mkdir($newfilesDir, 0777);*/
}