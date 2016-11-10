<?php

// Set include path
set_include_path(dirname(__FILE__) . DIRECTORY_SEPARATOR);

// Load core
require 'Core/OneCore.php';

//OneCore::Instance()->Configure();

OneCore::Instance()->Run('OneTrack');