<?php
/*
Copyright (c) <2021> Susilo Nurcahyo

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

namespace Controllers;

use Bantingan\Controller;

// sample of using class in modules directory
use Modules\Common\CSVFileHandler\CSVFileReader;
use Modules\Common\FileHandler\FileUpload;
use Modules\Common\ImageHandler\ImageResizer;

class SamplemoduleController extends Controller
{
	public function index()
	{    
		return $this->View();
	}

    public function fileuploader()
	{
        // form handler
        if (isset($_FILES) && count($_FILES) > 0) {
            $pathtosave = __DIR__.'/../uploads/';
            // make sure directory is available
            if (!is_dir($pathtosave)) {
                $old = umask(0);
                mkdir($pathtosave, 0775, true);
                umask($old);
            }

            // save uploaded file
            $uploadresult = FileUpload::Save($_FILES["fileUpload"], $pathtosave);
            // return result to view
            $this->viewBag->uploadMessage = $_FILES;
            $this->viewBag->uploadResult = $uploadresult;
        }
		return $this->View();
	}

    public function csvreader()
	{
        // form handler
        if (isset($_FILES) && count($_FILES) > 0) {
            $pathtosave = __DIR__.'/../uploads/';
            // make sure directory is available
            if (!is_dir($pathtosave)) {
                $old = umask(0);
                mkdir($pathtosave, 0775, true);
                umask($old);
            }

            // save uploaded csv file
            $uploadresult = FileUpload::Save($_FILES["fileUpload"], $pathtosave);            
            if ($uploadresult['success']) {
                $csvfilepath = $uploadresult["files"][0];
                // read csv file, and return the result to view
                $this->viewBag->csvResult = CSVFileReader::Read($csvfilepath, true);
            };
        }

        return $this->View();
	}

    public function imageresizer()
    {
        // form handler
        if (isset($_FILES) && count($_FILES) > 0) {
            $pathtosave = __DIR__.'/../uploads/images/';
            // make sure directory is available
            if (!is_dir($pathtosave)) {
                $old = umask(0);
                mkdir($pathtosave, 0775, true);
                umask($old);
            }
            // save uploaded file
            $uploadresult = FileUpload::Save($_FILES["fileUpload"], $pathtosave);
            if ($uploadresult['success']) {
                // original image path
                $imagefilepath = $uploadresult["files"][0];

                // resize options
                $option = 
                [
                    "small" => [ "maxheight" => 60],
                    "medium" => [ "maxheight" => 120],
                    "large" => [ "maxheight" => 300],
                ];

                // target resized image file path
                $smallimagepath = $pathtosave.'small-'.basename($imagefilepath);
                $mediumimagepath = $pathtosave.'medium-'.basename($imagefilepath);
                $largeimagepath = $pathtosave.'large-'.basename($imagefilepath);

                // save image to new file
                ImageResizer::Save($imagefilepath, $smallimagepath, $option["small"]);
                ImageResizer::Save($imagefilepath, $mediumimagepath, $option["medium"]);
                ImageResizer::Save($imagefilepath, $largeimagepath, $option["large"]);

                // return original image filename to view
                $this->viewBag->filename = basename($imagefilepath);         
            }
        }
		return $this->View();
    }

    public function imageviewer($filename, $size=null) 
    {         
        // sample to outputting resized image directly from original image source
        $imagefilepath = __DIR__.'/../uploads/images/'.$filename;
        $option = 
        [
            "small" => [ "maxheight" => 60],
            "medium" => [ "maxheight" => 120],
            "large" => [ "maxheight" => 300],
        ];

        return ImageResizer::View($imagefilepath, $option[$size]??[ "maxheight" => 600]);
    }
}