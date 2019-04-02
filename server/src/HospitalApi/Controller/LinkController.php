<?php
namespace HospitalApi\Controller;

use HospitalApi\Model\LinkModel;

/**
 * <b>LinkController</b>
 */
class LinkController extends ControllerAbstract
{
    public function __construct() {
        parent::__construct(new LinkModel());
    }

    public function uploadFileAction($req, $res, $args) {
		$files = $req->getUploadedFiles();
		$prefix = $req->getParam('prefix');
		$name = $req->getParam('name');

		if(array_key_exists('file', $files)) {
			$file = $files['file'];

			$fileName = $this->_getFileName($file, $name);
			$this->_moveToDestiny($file, $prefix, $fileName);
			
			$response = $fileName;
		
		} else {
			$res->withCode(401);
			$response = "File Not Found";
		}

		return $res->withJson($response);
    }
    
    public function changeStatusAction($req, $res, $args) {
        $id = $req->getParam('id');

        return $this->getModel()->changeStatus($id);
	}
	
	private function _getFileName($file, $fileName) {
		$name = $fileName ? $fileName : $file->getClientFilename();
		preg_match_all('/(\.\w{3})$/m', $name, $matches, PREG_SET_ORDER, 0);
		if( isset($matches[0]) ) {
			$fileName = $name;
		} else {
			$extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
			$fileName = "$name.$extension";			
		}
		$fileArray = explode('.', $fileName);
		$name = \Helper\SlugHelper::get( $fileArray[0] );
		$extension = $fileArray[1];
		$fileName = "$name.$extension";
		return $fileName;
	}

	private function _moveToDestiny($file, $prefix, $fileName) {
		$clientPath = PATH."../../../client";
        $imagePath = "static/img/links/";

		if($prefix) {
			$imagePath .= "$prefix/";
		}
		$imagePath .= $fileName;
		
		$destiny = "$clientPath/$imagePath";
		$file->moveTo("$destiny");

		if( $this->isProduction() ) {
			$savedImagePath = $destiny;
			$destiny = "$clientPath/dist/$imagePath";
			copy($savedImagePath, $destiny);
		}

		return $destiny;
	}

}