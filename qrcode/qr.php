<?php    
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
    
  //  echo "<h1>PHP QR Code</h1><hr/>";
    
    //set it to writable location, a place for temp generated PNG files
   
	//$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_TEMP_DIR = '/home/mcube/qrcode/temp/';
   // $PNG_TEMP_DIR = '/opt/lampp/htdocs/mcube/src/qrcode/temp/';

    include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
/*
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
*/
    
    
   // $filename = $PNG_TEMP_DIR.'dinesh.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);


    if (isset($_REQUEST['data'])) { 
		
		$imagename='Big'.md5($_REQUEST['data'].'|H|5').'.png';
		$filename = $PNG_TEMP_DIR.$imagename;
		if (file_exists($filename)) {
			chmod($filename,0777);
			unlink($filename);
		} 
       //exit;
        QRcode::png($_REQUEST['data'],$filename, 'H', '5', 2,'',$_REQUEST['color']);    
        
    }
       header("location:".$_REQUEST['url']."/".$imagename);
    //display generated file
   // echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" /><hr/>';  
    
    //config form
   
        
    // benchmark
  //  QRtools::timeBenchmark();    

    
