<?php
namespace Webfitters\Pdf\Model\Api\Data;

interface PdfInterface {

	public function getId();
	public function setId();
	
	public function getFile();
	public function setFile();
	
	public function getLink();
	public function setLink();
	
	public function getCreatedAt();
	public function setCreatedAt();

	public function getUpdatedAt();
	public function setUpdatedAt();

}