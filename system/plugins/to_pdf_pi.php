<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  
/** 
 * Try increasing memory available, mostly for PDF generation 
 */  
//ini_set("memory_limit","32M");  


function pdf_create($html, $filename, $stream=TRUE)  
{  
	$CI =& get_instance();
	require_once(BASEPATH."plugins/dompdf/dompdf_config.inc.php");  
	$path=$CI->config->item('pdf_files');
	$dompdf = new DOMPDF();  
	$dompdf->set_paper("a4", "portrait");  
	$dompdf->load_html($html);  
	$dompdf->render(); 
	$dompdf->get_canvas()->get_cpdf()->setEncryption($filename, $filename, array('print'));
	$file = $path.$filename.".pdf";
	$filename=$filename.".pdf";
	@file_put_contents($path.$filename, $dompdf->output());
	chmod($path.$filename,0777);
	
}  
function pdf_create1($html, $filename, $stream=TRUE)  
{  
	$CI =& get_instance();
	require_once(BASEPATH."plugins/dompdf/dompdf_config.inc.php");  
	$path=$CI->config->item('pdf_files');
	$dompdf = new DOMPDF();  
	$dompdf->set_paper("a4", "portrait");  
	$dompdf->load_html($html);  
	$dompdf->render(); 
	$file = $path.$filename.".pdf";
	$dompdf->get_canvas()->get_cpdf()->setEncryption($filename, $filename, array('print'));
	$dompdf->stream($filename.".pdf");  
	

}  
/*  end of pdf plugin */
