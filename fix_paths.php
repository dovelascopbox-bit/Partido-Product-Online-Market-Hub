<?php
/**
 * Path Fix Script - Remove / from all file paths
 * Run this script in your local XAMPP environment before uploading to InfinityFree
 */

$rootDir = __DIR__;
$count = 0;

// File extensions to process
$extensions = ['php', 'htaccess', 'css', 'js', 'html', 'md', 'sql'];

function processDirectory($dir, $extensions, &$count) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($files as $file) {
        if ($file->isFile()) {
            $ext = $file->getExtension();
            if (in_array($ext, $extensions)) {
                $filepath = $file->getPathname();
                $content = file_get_contents($filepath);
                
                // Replace / with / in all references
                $newContent = str_replace('/', '/', $content);
                
                if ($content !== $newContent) {
                    file_put_contents($filepath, $newContent);
                    $count++;
                    echo "Updated: " . str_replace(__DIR__ . '\\', '', $filepath) . "\n";
                }
            }
        }
    }
}

echo "Starting path fix...\n";
echo "========================\n";
processDirectory($rootDir, $extensions, $count);
echo "========================\n";
echo "Total files updated: $count\n";
echo "Done! You can now upload the files to InfinityFree.\n";
?>
