<?php
if(extension_loaded('imagick')) {
    echo 'Imagick Loaded';
} else {
    echo 'Imagick not Loaded';
}

print phpinfo();