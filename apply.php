<?php 
require 'src/core.php';

$output->operation = "apply";

//First delete the delete_me.txt files
if(file_exists($childPluginDir . "/modifications/delete_me.txt"))
    unlink($childPluginDir . "/modifications/delete_me.txt");
if(file_exists($childPluginDir . "/original/delete_me.txt"))
    unlink($childPluginDir . "/original/delete_me.txt");
if(file_exists($childPluginDir . "/src/cache/newFiles/delete_me.txt"))
    unlink($childPluginDir . "/src/cache/newFiles/delete_me.txt");

//first we must revert the changes so that we can track new files and deleted files 
revert(false); //false turns off loggings so we don't output the revert operaton 

//loop through the files we modfied and apply those changes to the plugin directory
foreach(get_file_list($modifiedDir, true) as $file){
    //ignore directory listing
    if($file['type'] == 'dir')
        continue;
    
    //define the working files
    $modFile = $file['name'];
    $oldFile = str_replace("-child/modifications", "", $modFile);
    $newFile = str_replace("-child/modifications", "-child/src/cache/newFiles", $modFile);
    $originalFile = str_replace($parentPluginDir, $originalDir, $oldFile);
    //stub out the ouput item models
    $originalOutput = new OuputItemModel();
    $originalOutput->fullPath = $originalFile;
    $modifiedOuput = new OuputItemModel();
    $modifiedOuput->fullPath = $oldFile;

    //determine what we are doing
    $operation = "update"; //update create delete
    if(!file_exists($oldFile))
        $operation = "create";

    //move the orginal file for backups 
    //note: this will ignore files that already exists in the original directory
    if($operation == "update"){
        if(!file_exists($originalFile)){
            //make sure all the folders exists
            $dir = pathinfo($originalFile, PATHINFO_DIRNAME);
            if(!is_dir($dir))
                mkdir($dir, 0777, true);
    
            //move the original file
            rename($oldFile, $originalFile);
            $originalOutput->msg = "Moved file to original folder.";
        }else{
            $originalOutput->msg = "File already exists in original folder.";
        }
        //update the output model
        array_push($output->originalItems, $originalOutput);
    }

    //make sure all the folders exists
    $dir = pathinfo($oldFile, PATHINFO_DIRNAME);
    if(!is_dir($dir))
        mkdir($dir, 0777, true);

    //now copy the modified file to the plugin 
    copy($modFile, $oldFile);
    $modifiedOuput->msg = "File ". (($operation == "update") ? "updated in" : "added to") ." parent directory";

    //if we are creating a new file add it to the cache so that the revert logic knows to delete it
    if($operation == "create"){
        //make sure all the folders exists
        $dir = pathinfo($newFile, PATHINFO_DIRNAME);
        if(!is_dir($dir))
            mkdir($dir, 0777, true);
            
        copy($modFile, $newFile);
    }

    //update ouput model
    array_push($output->modfiedItems, $modifiedOuput);
}

//display the results
require 'src/output.php';