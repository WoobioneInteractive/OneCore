<?php

// Set include path
set_include_path(dirname(__FILE__) . DIRECTORY_SEPARATOR);

// Load OneCore
require_once 'Core/OneCore.php';

// Configure from config file
OneCore::Instance()->Configure('config.php');

// Run application
OneCore::Instance()->Run();