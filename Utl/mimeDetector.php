<?php

    class mimeDetector {
    
    // File format information table
    // Format: $mime => array(name => $formatName)
    protected static $formatInfoTable = array(
        'image/jpeg' => array('name' => 'JPEG/JIFF Image'),
        'image/png' => array('name' => 'Portable (Public) Network Graphic'),
        'video/mng' => array('name' => 'Multi-image Network Graphic Animation'),
        'image/gif' => array('name' => 'Graphic Interchange Format Image'),
        'image/tga' => array('name' => 'Truevision Targa Graphic'),
        'image/tif' => array('name' => 'Tagged Image Format File'),
        'image/bmp' => array('name' => 'Windows OS/2 Bitmap Graphic'),
        'image/svg+xml' => array('name' => 'Scalable Vector Graphic'),
        'image/photoshop' => array('name' => 'Photoshop Format Image'),
        'image/vnd.microsoft.icon' => array('name' => 'Windows Icon'),
        'application/ogg' => array('name' => 'Ogg Multimedia File'),
        'audio/wav' => array('name' => 'Waveform Audio File'),
        'audio/mid' => array('name' => 'MIDI-sequention Sound File'),
        'audio/mpeg' => array('name' => 'MPEG Audio Stream (Layer III) File'),
        'video/mpeg' => array('name' => 'MPEG System Stream File'),
        'video/3gpp' => array('name' => '3GPP Multimedia File'),
        'video/quicktime' => array('name' => 'QuickTime Video Clip'),
        'video/avi' => array('name' => 'Audio Video Interleave File'),
        'application/x-shockwave-flash' => array('name' => 'Macromedia Flash File'),
        'application/pdf' => array('name' => 'Acrobat Portable Document File'),
        'application/winhlp' => array('name' => 'Windows Help File'),
        'application/msword' => array('name' => 'Word Document'),
        'application/msexcel' => array('name' => 'Excel Worksheet'),
        'application/mspowerpoint' => array('name' => 'PowerPoint Presentation'),
        'application/x-zip-compressed' => array('name' => 'Compressed Archive'),
        'application/x-rar-compressed' => array('name' => 'WinRAR Compressed Archive'),
        'application/x-ace-compressed' => array('name' => 'WinAce Compressed Archive'),
        'application/x-7z-compressed' => array('name' => '7-Zip Compressed Archive'),
        'application/x-bzip' => array('name' => 'Bzip 2 UNIX Compressed Archive'),
        'application/x-gzip' => array('name' => 'Gzip Compressed Archive'),
        'application/x-tar' => array('name' => 'Tape Archive'),
        'application/java-archive' => array('name' => 'Java Archive'),
        'font/ttf' => array('name' => 'TrueType Font'),
        'font/otf' => array('name' => 'Open Type Font'),
        'text/plain' => array('name' => 'Text File'),
        'text/html' => array('name' => 'HyperText Markup Language File'),
        'application/xhtml+xml' => array('name' => 'Extensible HyperText Markup Language File'),
        'text/xml' => array('name' => 'Extensible Markup Language File'),
        'application/x-httpd-php' => array('name' => 'PHP Script'),
        'application/x-java-class' => array('name' => 'Java Bytecode'),
        'application/octet-stream' => array('name' => 'Executable File')
    );
    
    // Ident reference table
    // Format: array($byteOffset, $representation, $ident, $mime)
    // s => string, h => hexadecimal, r => regular expression pattern
    protected static $identRefTable = array(
        array(0, 'h', '504b0304140008000800', 'application/java-archive'),
        array(0, 'h', '89504e470d0a1a0a00', 'image/png'),
        array(0, 'h', '8a4d4e470d0a1a0a00', 'video/mng'),
        array(0, 'h', 'cafebabe', 'application/x-java-class'),
        array(0, 'h', '0001000000', 'font/ttf'),
        array(0, 'h', '4f54544f00', 'font/otf'),
        array(0, 'h', '4944330', 'audio/mpeg'),
        array(0, 'h', '000001b', 'video/mpeg'),
        array(0, 'h', '00000100', 'image/vnd.microsoft.icon'),
        array(0, 'h', '000000', 'video/3gpp'),
        array(0, 's', '8BPS', 'image/photoshop'),
        array(0, 's', 'MThd', 'audio/mid'),
        array(0, 's', 'OggS', 'application/ogg'),
        array(0, 's', '**ACE**', 'application/x-ace-compressed'),
        array(0, 's', 'Rar!', 'application/x-rar-compressed'),
        array(0, 's', 'PK', 'application/x-zip-compressed'),
        array(0, 's', 'BZh', 'application/x-bzip'),
        array(0, 'h', '1f8b08', 'application/x-gzip'),
        array(0, 's', '7z', 'application/x-7z-compressed'),
        array(257, 's', 'ustar', 'application/x-tar'),
        array(0, 'sr', 'RIFF....WAVE', 'audio/wav'),
        array(0, 'sr', 'RIFF....AVI', 'video/avi'),
        array(0, 's', 'GIF8', 'image/gif'),
        array(0, 's', 'MM.*', 'image/tif'),
        array(0, 's', 'II*', 'image/tif'),
        array(0, 'h', 'ffd8', 'image/jpeg'),
        array(0, 'h', '424d', 'image/bmp'),
        array(0, 's', 'MZ', 'application/octet-stream'),
        array(0, 's', '?_', 'application/winhlp'),
        array(0, 's', '%PDF', 'application/pdf'),
        array(0, 's', 'FWS', 'application/x-shockwave-flash'),
        array(0, 'h', '6d', 'video/quicktime'),
        array(508, 'h', 'ffffffffeca5c100', 'application/msword'),
        array(508, 'h', 'fffffffffdffffff1f', 'application/msexcel'),
        array(508, 'h', 'fffffffffdffffffc3', 'application/mspowerpoint'),
        array(0, 'h', 'efbbbf', 'text/plain'), // UTF-8
        array(0, 'h', 'fffe', 'text/plain'), // UTF-16 LE
        array(0, 'h', 'feff', 'text/plain'), // UTF-16 BE
        array(0, 'h', 'fffe0000', 'text/plain'), // UTF-32 LE
        array(0, 'h', '0000feff', 'text/plain'),  // UTF-32 BE
        array(0, 'h', '00', 'image/tga'),
        array(0, 'h', 'ff', 'audio/mpeg'),
        array(0, 's', '<?php', 'application/x-httpd-php'),
        array(0, 's', '<!DOCTYPE HTML', 'text/html'),
        array(0, 's', '<!DOCTYPE html', 'text/html'),
        array(0, 's', '<!doctype html', 'text/html'),
        array(0, 's', '<HTML', 'text/html'),
        array(0, 's', '<html', 'text/html'),
        array(0, 's', '<?xml', 'text/xml')
    );
 
    // Extension reference table
    // Format: $extension => $mime
    protected static $extRefTable = array(
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'png' => 'image/png',
        'mng' => 'video/mng',
        'gif' => 'image/gif',
        'tga' => 'image/tga',
        'tif' => 'image/tif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'psd' => 'image/photoshop',
        'avi' => 'video/avi',
        'wav' => 'audio/wav',
        'mid' => 'audio/mid',
        'midi' => 'audio/mid',
        'mp3' => 'audio/mpeg',
        'mpg' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'ogg' => 'application/ogg',
        'ogm' => 'application/ogg',
        'ogv' => 'application/ogg',
        'oga' => 'application/ogg',
        '3gp' => 'video/3gpp',
        '3g2' => 'video/3gpp',
        'mov' => 'video/quicktime',
        'swf' => 'application/x-shockwave-flash',
        'zip' => 'application/x-zip-compressed',
        'rar' => 'application/x-rar-compressed',
        'r01' => 'application/x-rar-compressed',
        'ace' => 'application/x-ace-compressed',
        '7z' => 'application/x-7z-compressed',
        'jar' => 'application/java-archive',
        'bz2' => 'application/x-bzip',
        'tbz2' => 'application/x-bzip',
        'tb2' => 'application/x-bzip',
        'gz' => 'application/x-gzip',
        'tar' => 'application/x-tar',
        'exe' => 'application/octet-stream',
        'com' => 'application/octet-stream',
        'dll' => 'application/octet-stream',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'xls' => 'application/msexcel',
        'ppt' => 'application/mspowerpoint',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'htm' => 'text/html',
        'html' => 'text/html',
        'xhtml' => 'text/html',
        'xht' => 'text/html',
        'xml' => 'text/xml',
        'svg' => 'image/svg+xml',
        'php' => 'application/x-httpd-php',
        'class' => 'application/x-java-class',
        'txt' => 'text/plain',
        'log' => 'text/plain',
        'msg' => 'text/plain',
        'rtf' => 'text/plain',
        'nfo' => 'text/plain'
    );
    
    // Maximum number of bytes to read from the file starting at offset 0
    protected static $maxLength;
 
    // Determine maximum length to read from files
    protected static function determineMaxLenght() {
        $maxLength = 0;
        foreach (self::$identRefTable as &$ident) {
            $len = strlen($ident[2]);
            if ($ident[1] == 'h') $len /= 2;
            $len += $ident[0];
            if ($len > $maxLength) $maxLength = $len;
        }
        self::$maxLength = $maxLength;
    }
 
    // Attempts to figure out file format by looking at the first few bytes of the file
    public static function fromHeader($target) {
 
        // Determine max lenght to read from file if it hasn't been done already
        if (!isset(self::$maxLength)) self::determineMaxLenght();
 
        // Make sure the target is a file we can work with before opening it
        if (!is_file($target)) throw new Exception("'$target' is not a valid file.");
        $handle = @fopen($target, 'r');
        if ($handle === false) throw new Exception("Could not open target file '$target'.");
        
        // Read data from target file
        $bin = fread($handle, self::$maxLength);
        if ($bin === false) throw new Exception("Unable to read data from '$target'.");
        fclose($handle);
 
        $len = strlen($bin);
 
        // Convert the header to a hexadecimal representation to work with
        $hex = bin2hex($bin);
 
        // Compare data with each entry in the ident table
        foreach (self::$identRefTable as $ident) {
            // Skip current if the data read isn't long enough
            if ($ident[0]> $len) continue;
            // Compare by string or hexadecimal representation?
            if ($ident[1][0] == 's') $cmp = substr($bin, $ident[0], strlen($ident[2]));
            else $cmp = substr($hex, $ident[0]*2, strlen($ident[2]));
            // Return the mime type associated with the ident if we have a match
            // Compare with regex if a second character in the representation variable is present (r)
            if ( (isset($ident[1][1]) && preg_match('~^'.$ident[2].'~', $cmp)) || $cmp == $ident[2] )
                return $ident[3];
        }
        
        // No match found in table
        return '';
 
    }
 
    public static function getMime($target, $name) {
	    $mime = self::fromHeader($target);
	    if( empty($mime))
		$mime = self::fromExtension($name);
	    if( empty($mime))
		return 'application/unknown';
	    return $mime;
    }

    // Tries to guess the file format by the targets file extension
    public static function fromExtension($target) {
        $ext = self::extractExtension($target);
        if (strlen($ext) < 1 || !isset(self::$extRefTable[$ext]))
            return '';
        return self::$extRefTable[$ext];
    }
 
    // Extracts file extension from the target path
    public static function extractExtension($target) {
        preg_match('~(?>\.([A-Za-z0-9_]+))?$~', $target, $targetParts);
        return strtolower($targetParts[1]);
    }
 
    // Returns info (from file format information table) for the specified mime type
    public static function infoFor($mime) {
        if (!isset(self::$formatInfoTable[$mime])) return array();
        return self::$formatInfoTable[$mime];
    }
 
    // Returns possible extensions for the specified mime type
    public static function extensionsFor($mime) {
        return array_keys(self::$extRefTable, $mime);
    }
 
}



?>