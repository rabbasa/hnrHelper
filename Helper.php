<?php
/**
 * HNR Helper
 * @author hnr <harris@rabbasa.or.id>
 * @version 0.1 first init
 */


namespace common\components;

use Yii;

use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class Helper
 * author : harris[at]rabbasa.or.id
*/
class Helper
{
	/**
	 * fungsi untuk generate nama file untuk diletakkan di file system
	 * @param  string $value nama file uploadan
	 * @param  string $path  nama direktori
	 * @return string        format output NAMAFOLDER/yyyymmddhhiiss-namafiletanpaspasi
	 */
	public static function genFileNamePath($value='',$path="")
	{
		return "$path" . DIRECTORY_SEPARATOR . date("YmdHis") . "-" . Helper::secureRename($value);
	}

	/**
	 * Fungsi untuk me-rename File
	 */
	public static function secureRename($in)
	{
		$x=str_replace(" ", "_", $in);
		$x=str_replace(",", "_", $x);
		$x=str_replace("(", "_", $x);
		$x=str_replace(")", "_", $x);
		return $x;
	}

	/**
	 * hnr: 2014-12-15
	 *  update : 2015-08-08
	 * fungsi upload file, masukkan nama variabel di form html
	 * nama variabel dengan array files dan bisa multiple : default upl_file[]
	 * allowable_file_upload_type : array
	 * prefix : awalan nama file pada file-system
	 * folder : upload directory sconfig['folder_storage']/folder 
	 *	diambil dari params common\config\params.php
	 */
	public static function uploadFile($nama_var_post,$folder="berkas", $allowable_file_upload_type=[], $prefix="")
	{
		$folder_storage =  \Yii::$app->params['folder_storage'];

		$array_return=array();
		//<input id="MyRegistrant_files" type="file" value="" name="MyRegistrant[files][]" class="MultiFile-applied">
		$images=UploadedFile::getInstancesByName("$nama_var_post");

		// $folder_storage=Yii::app()->helper->sconfig['folder_storage'];

		// $allowable_file_upload_type=explode(",", Yii::app()->helper->sconfig['allowable_file_upload_type']);
		// image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document
		$tahun=date("Y");
		$bulan=date("m");

		$prefix=$prefix.time();

		foreach($images as $image){
			if($image && $image->size > 1){
				$var_image['content_type']=$image->type;
				if(!in_array($image->type, $allowable_file_upload_type))
					return false;
				$var_image['file_size']=$image->size;
				$var_image['nama_file_asli']=$image->name;
				$var_image['nama_file_mask']=$folder.DIRECTORY_SEPARATOR.$tahun.DIRECTORY_SEPARATOR.$bulan.DIRECTORY_SEPARATOR.(($prefix)?"$prefix" . "-" : "" ).self::secureRename($var_image['nama_file_asli']);
				$var_image['nama_file_full']=PATH_RELATIVE.DIRECTORY_SEPARATOR.$folder_storage.DIRECTORY_SEPARATOR.$var_image['nama_file_mask'];
				$dir=dirname($var_image['nama_file_full']);

				// var_dump($var_image);exit;
				if(!is_dir($dir)){
					if(!mkdir($dir,0777,true)) return false;
				}
				if(file_exists($var_image['nama_file_full'])){
					@unlink($var_image['nama_file_full']);
				}
				$ret=$image->saveAs($var_image['nama_file_full']);
				if(!$ret)return false;
			}
			$array_return[]=$var_image;
		}
		return $array_return;
	}

	/**
	 * hnr: 2014-12-16
	 *  update : 2015-08-08
	 * fungsi untuk delete file yang ada di folder storage
	 */
	public static function deleteFileInStorage($nama_file_mask)
	{	
		$folder_storage = \Yii::$app->params['folder_storage'];
		// $folder_storage =  Yii::app()->helper->sconfig['folder_storage'];
		$file=PATH_RELATIVE.DIRECTORY_SEPARATOR.$folder_storage.DIRECTORY_SEPARATOR.$nama_file_mask;
		if(file_exists($file) && 
			is_file($file)) return unlink($file);
			else return true;
		
	}
	
	public static function viewBerkas($nama_file_di_sistem, $file_size, $nama_file_asli, $content_type,$is_download=false)
	{
		$debug=(isset($_GET['debug']))?$_GET['debug']:false;
		if($debug){
			$nf=$ln="";
			headers_sent($nf,$ln);
			echo "$nf $ln";exit;
		}
			ob_end_clean();

		// $folder_storage=\Yii::$app->helper->sconfig['folder_storage'];
		// $nama_file_di_sistem = PATH_RELATIVE.DIRECTORY_SEPARATOR.$folder_storage.DIRECTORY_SEPARATOR . $nama_file_mask;
		if($nama_file_asli==""){
			$nama_file_asli = preg_replace('/(\d+)-(.*)/i', '$2', basename($nama_file_mask));
			if(!$nama_file_asli)$nama_file_asli=basename($nama_file_mask);
		}

		if(is_file($nama_file_di_sistem) && file_exists($nama_file_di_sistem)){
			if(!$file_size)$file_size = filesize($nama_file_di_sistem);
			$time_last_modification=filemtime($nama_file_di_sistem);

	    	if(!$content_type){
		    	$file_info = new finfo(FILEINFO_MIME);  // object oriented approach!
				$mime_type = $file_info->buffer(file_get_contents($nama_file_di_sistem));  // e.g. gives "image/jpeg"
			}

			header("content-length:".$file_size);
			// header("Cache-Control:public, must-revalidate, post-check=0, pre-check=0");
			$max_age = 60*60*24*2; #perintahkan browser untuk cache image selama 2 hari
			header("Cache-Control:public, max-age=".$max_age);
			header("Expires:".gmstrftime("%a, %d %b %Y %T %Z", time()+(60*60*24*7)));
			header("accept-ranges:bytes");
			header("last-modified:".gmstrftime("%a, %d %b %Y %T %Z", $time_last_modification) );
			header("pragma:private");
			if($is_download)
	    		header('Content-Disposition: attachment; filename="'.basename($nama_file_asli).'"');

	    	header('Content-Transfer-Encoding: binary');
	    	header("content-type:".$content_type);
			flush();
			readfile($nama_file_di_sistem);
			exit;
		}else{
			echo "file tidak dapat ditemukan";
			return false;
		}
		
	}

}