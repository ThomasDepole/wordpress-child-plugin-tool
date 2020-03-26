<?php 
require 'src/core.php';

$output->operation = "revert";

//call the core function to revert changes
revert(true);

require 'src/output.php';