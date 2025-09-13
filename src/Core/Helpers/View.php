<?php
/**
 * Becomes recursive if $__layout in child view is decalred
 * ob_start - store all the execute html and php code inside the memory under it
 * require - returns (end the program/function) all the html and php code the varibale of it still usable inside the function
 * ob_get_clean - gets the executed result of php and html as string 
 */
function view($location = "views", $data = [], &$__layout = null) {
    $allowedExt = ['php', 'html', 'blade.php'];
    $basePath = __DIR__ . "/../../Resources" . DIRECTORY_SEPARATOR;
    $dirArr = explode(".", $location);
    $viewFile = "";

    foreach ($dirArr as $dir) {
        $basePath .= $dir . DIRECTORY_SEPARATOR;
    }

    $basePath = rtrim($basePath, DIRECTORY_SEPARATOR);

    foreach ($allowedExt as $ext) {
        $filename = $basePath . "." . $ext;
        if (is_file($filename)) {
            $viewFile = $filename;
            break;
        } 
    }

    if ($viewFile === "") {
        echo "<h1 style='font-family: monospace; height: 75vh; display: flex; align-items: center; justify-content: center; font-size: 50px;'>View file \"<span style='color: red;'>$location</span>\" does not exist</h1>";
        exit;
    }

    ob_start();
    extract($data, EXTR_SKIP);
    require $viewFile;  
    $content = ob_get_clean();


    if ($__layout ?? false) {
        $contentAndData = array_merge($data, ['content' => $content]);
        return view($__layout, $contentAndData); // recursive call
    }

    echo $content;
}
