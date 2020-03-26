# WordPress Child Plugin Tool

This tool allows you to modify a Wordpress plugin without losing the ability to update the plugin. This tool works like Wordpress child themes where you can override files that exist in the child plugin by creating a copy of the file and add files to the plugin by creating new files. You can also revert all the changes you made to the plugin. 

NOTE: This tool is used to modify a plugin but doesn't replace the plugin. You don't have to upload this tool to your WordPress site and don't enable this plugin from the Wordpress dashboard. It's highly recommended that you **DO NOT** leave this tool public on your live site. It's meant for development and updating plugins.

# Table of contents
* [Setup](#setup)
* [Modify Files](#modify-files)
* [Create Files](#create-files)
* [Reverting Changes](#reverting-changes)
* [Updating the Parent Plugin](#updating-the-parent-plugin)
* [Limitations](#limitations)

## Setup

First, we need to create a new folder with the same name prefixed with -child in the same directory as the plugin you want to modify. For example, if we are going to modify the plugin "user-role-editor" we will create a folder in the same directory called "user-role-editor".

After you created the child plugin folder copy and paste the content of this tool into that directory. That's all the setup needed. Now we can start modifying files. 

Your new folder should look like this 
```
/user-role-editor
/user-role-editor-child
    /modifications
    /original
    /src 
        /cache
            /newFiles
        core.php
        output.php
    apply.php
    revert.php
    README.md
```
You should not make changes to the files in the /src directory unless you want to modify the tool. 

## Modify Files

You're going to be working out of the modifications folder. If you want to modify a file that exists in the plugin copy the file from the parent plugin and paste it in the modifications folder. **You must create the same folder structure** otherwise the tool will think it's a new file. 

Now you can make changes to the copy of the file in your modifications folder. When you're done and want to move those changes to the parent plugin navigate to the apply.php file in your browser. Opening that file will create a copy of the original file and move it to the original folder and copy the modified file to the parent plugin folder. 

You can run apply.php as many times as you want and the tool will keep a copy of the original files, feel free to review those files if you wanted to know what was there before. NOTE: don't modify files in the original folder it will prevent the tool from properly reverting. 

EXAMPLE

Here's an example of modifying two files before we ran the apply.php
```
/user-role-editor-child
    /modifications
        /includes
            /classes
                view.php
        user-role-editor.php
```

Here's what will change after we run apply.php
```
/user-role-editor
    /modifications
        /includes
            /classes
                view.php (updated)
        user-role-editor.php (updated)
/user-role-editor-child
    /modifications
        /includes
            /classes
                view.php
        user-role-editor.php
    /original
        /includes
            /classes
                view.php (copy from parent)
        user-role-editor.php (copy from parent)
```

## Create Files

Please read "Modify Files" first as it explains how to use the modifications folder. 

You can create new files that don't exist in the parent plugin. Simply create the new file in the modifications folder with the folder structure you wish to create and navigate to apply.php in your browser. This will create the file in the parent plugin folder when you run revert.php it will delete that file from the parent plugin folder. 

EXAMPLE

Here's an example of creating two new files before running apply.php

```
/user-role-editor-child
    /modifications
        /new_folder
            new_file_1.php
        new_file_2.php
```

Here's what happens when you run apply.php

```
/user-role-editor
    /modifications
        /new_folder (new folder created)
            new_file_1.php (new file created)
        new_file_2.php (new file created)
/user-role-editor-child
    /modifications
        /new_folder
            new_file_1.php
        new_file_2.php
    /src
        /cache
            /newFiles
                new_file_1.php (copy of new file)
        new_file_2.php (copy of new file)
```

NOTE: the files in src/cache/newFiles are for the tool, you shouldn't need to update or view these files. This allows the tool to know which files to delete. 

# Reverting Changes

If you want to revert the changes you made to the parent plugin all you need to do is navigate to revert.php in your browser and the tool will copy the original files back and delete new files that were created. 

You need to do this when you're going to update the parent plugin but it's useful for when testing your changes, sometimes we break stuff, so it's nice to have an undo button!

NOTE: The plugin will leave behind folders if you created them in the modifications folder. This shouldn't affect the plugin but it's important to note that they will be there.

# Updating the Parent Plugin 

When the time comes and you need to update the parent plugin you're in luck. You went through all this hassle to make this process easy!

1) Navigate to revert.php in your browser.
2) Update the parent plugin.
3) Navigate to apply.php in your browser.
4) Pour yourself a beer. 

That's it! 

## Limitations

Currently, this tool doesn't provide a way to delete files in the parent plugin directory. If you really need this feature please create a comment and I'll add that feature in the near future!

This tool doesn't replace your existing plugin, you don't need to active this tool inside of Wordpress and you shouldn't leave this tool on your live site. 

If you already modified a plugin you will need to use a diff tool to manually extract those changes into the modifications folder. The great thing about this tool is that it doesn't matter if you're already using the plugin, Wordpress won't know that you updated it so you don't have to make any changes in the Dashboard. 

You should always update the version number of your plugin your updating so that you know it's modified. 
If you have any feature requests feel free to add comments! Enjoy!