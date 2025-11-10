<?php
if ($_GET['enter'] != 'clear')
    exit;
    $fso = opendir("../agent/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../agent/temp_dc/" . $flist);
    }
    closedir($fso);
    
    $fso = opendir("../hide/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../hide/temp_dc/" . $flist);
    }
    closedir($fso);
    
    $fso = opendir("../mxj/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../mxj/temp_dc/" . $flist);
    }
    closedir($fso);
    
    $fso = opendir("../uxj/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../uxj/temp_dc/" . $flist);
    }
    closedir($fso);

    $fso = opendir("../man/temp_dc");
    
    while ($flist = readdir($fso)) {
        if (strrpos($flist, 'htm'))
            unlink("../man/temp_dc/" . $flist);
    }
    closedir($fso);


	echo 'ok';