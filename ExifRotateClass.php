<?PHP

/**
 * Document   : EXIF Image Rotate Class
 * Author     : josephtinsley
 * Description: PHP class that detects a JPEG image current orientation and rotates a image using the images EXIF data.
 * http://twitter.com/josephtinsley
*/

class RotateImage {
    
    /*
     * @param string $setFilename - Set the original image filename
     * @param array $exifData - Set the original image filename
     * @param string $savedFilename - Set the rotated image filename
     */
    
    private $setFilename    = "";
    private $exifData       = "";
    private $degrees        = "";
    
    public function __construct($setFilename) 
    {
        try{
            if(!file_exists($setFilename))
            {
                throw new Exception('File not found.');
            } 
            $this->setFilename = $setFilename;
        } catch (Exception $e ) {
            die($e->getMessage());
        } 
    }
    
    /*
     * EXTRACTS EXIF DATA FROM THE JPEG IMAGE
     */
    public function processExifData() 
    {
        $orientation = 0;

        $this->exifData = exif_read_data($this->setFilename);

        foreach($this->exifData as $key => $val)
        {
            if(strtolower($key) == "orientation" )
            {
                $orientation = $val;
                break;
            }
        }
        if( $orientation == 0 )
        {
            $this->_setOrientationDegree(1);
        }
        
        $this->_setOrientationDegree($orientation); 
    } 

    
    /*
     * DETECTS AND SETS THE IMAGE ORIENTATION
     * Orientation flag info  http://www.impulseadventure.com/photo/exif-orientation.html
     */
    private function _setOrientationDegree($orientation)
    {
       switch($orientation):
           case 1: 
               $this->degrees = 0;
               break;
           case 8:
               $this->degrees = 90;
               break;
           case 3:
               $this->degrees = 180;
               break;
           case 6:
               $this->degrees = 270;
               break;
       endswitch;
       
       $this->_rotateImage();
    }  
    
    
    /*
     * ROTATE THE IMAGE BASED ON THE IMAGE ORIENTATION
     */
    private function _rotateImage() 
    {
        if($this->degrees < 1 )
        {
            return FALSE;
        }
        $image_data = imagecreatefromjpeg($this->setFilename);
        return imagerotate($image_data, $this->degrees, 0);  
    } 
    
    
    /*
     * SAVE THE IMAGE WITH THE NEW FILENAME
     */
    public function savedFileName($savedFilename) 
    {
        if($this->degrees < 1 )
        {
            return false;   
        }
        
        $imageResource = $this->_rotateImage();
        if($imageResource == FALSE)
        {
            return false;   
        }
        
        imagejpeg($imageResource, $savedFilename);  
    } 


    

} //END CLASS

$imageFile = "IMG_1778.JPG";
$savedFile = "rotated_".$imageFile;

$rotate = new RotateImage($imageFile);

$rotate->processExifData();
$rotate->savedFileName($savedFile);

print '<img src="'.$imageFile.'" width="250"/>'."<br>";
print '<img src="'.$savedFile.'" width="250"/>'."<br>";


?>

