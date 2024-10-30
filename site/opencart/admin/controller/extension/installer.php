<?php
class ControllerExtensionInstaller extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/installer');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_upload'] = $this->language->get('entry_upload');
		$data['entry_overwrite'] = $this->language->get('entry_overwrite');
		$data['entry_progress'] = $this->language->get('entry_progress');

		$data['help_upload'] = $this->language->get('help_upload');

		$data['button_upload'] = $this->language->get('button_upload');
		$data['button_clear'] = $this->language->get('button_clear');
		$data['button_continue'] = $this->language->get('button_continue');
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/installer', 'token=' . $this->session->data['token'], 'SSL')
		);
		
		$data['token'] = $this->session->data['token'];

		$directories = glob(DIR_UPLOAD . 'temp-*', GLOB_ONLYDIR);

		if ($directories) {
			$data['error_warning'] = $this->language->get('error_temporary');
		} else {
			$data['error_warning'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/installer.tpl', $data));
	}

	public function upload() {
		$this->load->language('extension/installer');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			if (!empty($this->request->files['file']['name'])) {
				if ((strrchr($this->request->files['file']['name'], '.') != '.zip' && strrchr($this->request->files['file']['name'], '.') != '.xml') && (substr($this->request->files['file']['name'], -10) != '.ocmod.zip' && substr($this->request->files['file']['name'], -10) != '.ocmod.xml')) { #miwoshop-start
					$json['error'] = $this->language->get('error_filetype');
				}

				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		}

		if (!$json) {
			// If no temp directory exists create it
			$path = 'temp-' . md5(mt_rand());

			if (!is_dir(DIR_UPLOAD . $path)) {
				mkdir(DIR_UPLOAD . $path, 0777);
			}

			// Set the steps required for installation
			$json['step'] = array();
			$json['overwrite'] = array();

            #miwoshop start
            if (strrchr($this->request->files['file']['name'], '.') != '.zip') {
                $xml = file_get_contents($this->request->files['file']['tmp_name']);

                if ($xml) {
                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->loadXml($xml);
                    $code = $dom->getElementsByTagName('code')->item(0);
                }
            } else {
                $code = NULL;
            }
            #miwoshop finish
			if (strrchr($this->request->files['file']['name'], '.') == '.xml') {
				$file = DIR_UPLOAD . $path . '/install.xml';

                #miwoshop start
                if(empty($code)) {
                    $this->session->data['vqmod_file_name'] = $this->request->files['file']['name'];
                    $file = DIR_UPLOAD . $path . '/' . $this->request->files['file']['name'];

                    $_file = MPATH_MIWOSHOP_OC . '/vqmod/xml/' . $this->request->files['file']['name'];

                    $vqmodNameLen = strlen($this->request->files['file']['name']);
                    $vqmodNameLen = $vqmodNameLen + 11;

                    if (is_file($_file) && strtolower(substr($_file, -$vqmodNameLen)) == '/vqmod/xml/' . $this->request->files['file']['name']) {
                        $json['overwrite'][] = 'vqmod/xml/' . $this->request->files['file']['name'];
                    }
                }
                #miwoshop finish
				
				// If xml file copy it to the temporary directory
				move_uploaded_file($this->request->files['file']['tmp_name'], $file);

				if (file_exists($file)) {
					$json['step'][] = array(
						'text' => $this->language->get('text_xml'),
						'url'  => str_replace('&amp;', '&', $this->url->link('extension/installer/miwi_xml', 'format=raw&tmpl=component&token=' . $this->session->data['token'], 'SSL')),
						'path' => $path
					);

                    // Refresh Modification
                    $json['step'][] = array(
                        'text' => $this->language->get('text_modification'),
                        'url'  => str_replace('&amp;', '&', $this->url->link('extension/modification/refresh', 'format=raw&tmpl=component&token=' . $this->session->data['token'] . '&extensionInstaller=1', 'SSL')),
                        'path' => $path
                    );
					// Clear temporary files
					$json['step'][] = array(
						'text' => $this->language->get('text_remove'),
						'url'  => str_replace('&amp;', '&', $this->url->link('extension/installer/remove', 'format=raw&tmpl=component&token=' . $this->session->data['token'], 'SSL')),
						'path' => $path
					);
				} else {
					$json['error'] = $this->language->get('error_file');
				}
			}

			// If zip file copy it to the temp directory
			if (strrchr($this->request->files['file']['name'], '.') == '.zip') {
				$file = DIR_UPLOAD . $path . '/upload.zip';

				move_uploaded_file($this->request->files['file']['tmp_name'], $file);

				if (file_exists($file)) {
					$zip = zip_open($file);

					if ($zip) {
						// Zip
						$json['step'][] = array(
							'text' => $this->language->get('text_unzip'),
							'url'  => str_replace('&amp;', '&', $this->url->link('extension/installer/unzip', 'format=raw&tmpl=component&token=' . $this->session->data['token'], 'SSL')),
							'path' => $path
						);

						// FTP
						$json['step'][] = array(
							'text' => $this->language->get('text_ftp'),
							'url'  => str_replace('&amp;', '&', $this->url->link('extension/installer/miwi_ftp', 'format=raw&tmpl=component&token=' . $this->session->data['token'], 'SSL')),
							'path' => $path
						);
						
						// Send make and array of actions to carry out
						while ($entry = zip_read($zip)) {
							$zip_name = zip_entry_name($entry);

							// SQL
							if (substr($zip_name, 0, 11) == 'install.sql') {
								$json['step'][] = array(
									'text' => $this->language->get('text_sql'),
									'url'  => str_replace('&amp;', '&', $this->url->link('extension/installer/miwi_sql', 'format=raw&tmpl=component&token=' . $this->session->data['token'], 'SSL')),
									'path' => $path
								);
							}

							// XML
							if (substr($zip_name, 0, 11) == 'install.xml') {
								$json['step'][] = array(
									'text' => $this->language->get('text_xml'),
									'url'  => str_replace('&amp;', '&', $this->url->link('extension/installer/miwi_xml', 'format=raw&tmpl=component&token=' . $this->session->data['token'], 'SSL')),
									'path' => $path
								);
							} else if (substr($zip_name, -4) == '.xml'){ #miwoshop start
                                $_file = MPATH_MIWOSHOP_OC . '/vqmod/xml/' . $zip_name;

                                $vqmodNameLen = strlen($zip_name);
                                $vqmodNameLen = $vqmodNameLen + 11;

                                if (is_file($_file) && strtolower(substr($_file, -$vqmodNameLen)) == '/vqmod/xml/' . $zip_name) {
                                    $json['overwrite'][] = 'vqmod/xml/' . $zip_name;
                                }

                                $this->session->data['vqmod_file_name'] = $zip_name;
                                $json['step'][] = array(
                                    'text' => $this->language->get('text_xml'),
                                    'url'  => str_replace('&amp;', '&', $this->url->link('extension/installer/miwi_xml', 'format=raw&tmpl=component&token=' . $this->session->data['token'], 'SSL')),
                                    'path' => $path
                                );
                            }
							#miwoshop finish

							// PHP
							if (substr($zip_name, 0, 11) == 'install.php') {
								$json['step'][] = array(
									'text' => $this->language->get('text_php'),
									'url'  => str_replace('&amp;', '&', $this->url->link('extension/installer/php', 'format=raw&tmpl=component&token=' . $this->session->data['token'], 'SSL')),
									'path' => $path
								);
							}
							
							// Compare admin files
							$file = MPATH_MIWOSHOP_OC .'/'. substr($zip_name,7); #miwoshop-start
							if (is_file($file) && strtolower(substr($zip_name, 0, 13)) == 'upload/admin/'){ #miwoshop-start
								$json['overwrite'][] = substr($zip_name, 7);
							}

							// Compare catalog files
							$file = MPATH_MIWOSHOP_OC .'/'. substr($zip_name,7); #miwoshop-start

							if (is_file($file) && strtolower(substr($zip_name, 0, 15)) == 'upload/catalog/') { #miwoshop-start
								$json['overwrite'][] = substr($zip_name, 7);
							}

							// Compare image files
							$file = MPATH_MIWOSHOP_OC .'/'. substr($zip_name,7); #miwoshop-start
							if (is_file($file) && strtolower(substr($zip_name, 0, 13)) == 'upload/image/') { #miwoshop-start
								$json['overwrite'][] = substr($zip_name, 7);
							}

							// Compare system files
							$file = MPATH_MIWOSHOP_OC .'/'. substr($zip_name, 7);

							if (is_file($file) && strtolower(substr($zip_name, 0, 14)) == 'upload/system/') {
								$json['overwrite'][] = substr($zip_name, 7);
							}
                            #miwoshop start
							// Compare system files
							$file = MPATH_MIWOSHOP_OC .'/'. substr($zip_name, 7);

							if (is_file($file) && strtolower(substr($zip_name, 0, 13)) == 'upload/vqmod/') {
								$json['overwrite'][] = substr($zip_name, 7);
							}
                            #miwoshop finish
						}

                        // Refresh Modification
                        $json['step'][] = array(
                            'text' => $this->language->get('text_modification'),
                            'url'  => str_replace('&amp;', '&', $this->url->link('extension/modification/refresh', 'format=raw&tmpl=component&token=' . $this->session->data['token'] . '&extensionInstaller=1', 'SSL')),
                            'path' => $path
                        );
						// Clear temporary files
						$json['step'][] = array(
							'text' => $this->language->get('text_remove'),
							'url'  => str_replace('&amp;', '&', $this->url->link('extension/installer/remove', 'format=raw&tmpl=component&token=' . $this->session->data['token'], 'SSL')),
							'path' => $path
						);

						zip_close($zip);
					} else {
						$json['error'] = $this->language->get('error_unzip');
					}
				} else {
					$json['error'] = $this->language->get('error_file');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function unzip() {
		$this->load->language('extension/installer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Sanitize the filename
		$file = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/upload.zip';

		if (!file_exists($file)) {
			$json['error'] = $this->language->get('error_file');
		}

		if (!$json) {
			// Unzip the files
			$zip = new ZipArchive();

			if ($zip->open($file)) {
				$zip->extractTo(DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']));
				$zip->close();
			} else {
				$json['error'] = $this->language->get('error_unzip');
			}

			// Remove Zip
			unlink($file);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function miwi_ftp() {
		$this->load->language('extension/installer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

        #miwoshop-start
        $check_folder = 0;
        if ($folder = opendir(DIR_UPLOAD . $this->request->post['path'].'/')) {
            while (false !== ($sub_folder = readdir($folder))) {
                if ($sub_folder != "." && $sub_folder != "..") {
                    if (is_dir(DIR_UPLOAD . $this->request->post['path'] . '/' . $sub_folder)) {
                        $check_folder = 1 ;
                        break;
                    }
                }
            }
        }
        if(empty($check_folder)) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
        #miwoshop-finish
		$directory = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/upload/';
		#miwoshop-start
        if (!is_dir($directory)) {
			$directory = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/UPLOAD/';
		}
		if (!is_dir($directory)) {
			$directory = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/Upload/';
		}
		#miwoshop-finish
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json) {
			// Get a list of files ready to upload
			$files = array();

			$path = array($directory . '*');

			while (count($path) != 0) {
				$next = array_shift($path);

				foreach (glob($next) as $file) {
					if (is_dir($file)) {
						$path[] = $file . '/*';
					}
					
					#miwoshop-start
					$edit_page_url = explode("upload", strtolower($file));
					$edit_page_url = 'upload'.$edit_page_url[2];
					$this->permission_control($edit_page_url);
					if(defined('MPATH_MIWOSHOP_OC')){
						$this->editFile($file);
					}
					#miwoshop-finish
					
					$files[] = $file;
				}
			}

			#miwoshop-start
			$root = MPATH_WP_PLG.'/miwoshop/site/opencart/';

            foreach ($files as $file) {
                // Upload everything in the upload directory
                
                $destination = substr($file, strlen($directory));

                if (is_dir($file)) {
                    if (!is_dir($root.$destination)) {
                        if (!mkdir($root.$destination)) {
                            $json['error'] = sprintf($this->language->get('error_ftp_directory'), $destination);
                            exit();
                        }
                    }
                }	

                if (is_file($file)) {
                    if (!copy($file, $root.$destination)) {
                        $json['error'] = sprintf($this->language->get('error_ftp_file'), $file);
                    }
                }
            }		
			
			#miwoshop-finish
		$this->response->setOutput(json_encode($json));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	#miwoshop-start
	public function editFile($path){

        $page         = $path;
      //  $edit_page    = html_entity_decode(file_get_contents($page));
        $edit_page    = file_get_contents($page);

        if ($edit_page!=""){
            $source = '';
            $edit_page_url = explode("upload", strtolower($page));
            $edit_page_url = 'upload'.$edit_page_url[1];

                if (strpos($edit_page_url, 'upload/admin')!== false){
                    $source = 'admin';
                }elseif(strpos($edit_page_url, 'upload/catalog')!== false){
                    $source = 'site';
                }elseif(strpos($edit_page_url, 'module') !== false){
                    $source = 'module';
                }elseif(strpos($edit_page_url, 'upload/vqmod') !== false){
                    $source = 'vqmod';
                }
            
            $image_control = explode('.',$edit_page_url);
            if((($image_control[1]!='png') AND ($image_control[1]!='gif') AND ($image_control[1]!='jpg'))){
				if(in_array("js",$image_control)){$source = 'js';}
				$edit_page = $this->replace($source,$edit_page);
				
                file_put_contents($page, $edit_page);
				// Controllers page check permission for Show Administrator automaticaly..
                $this->permission_control($edit_page_url);
            }
        }
    }

    public function replace($source,$edit_page){

        $replace_output['JPATH_MIJOSHOP'] 		                                                                = 'MPATH_MIWOSHOP';

    if ($source == 'admin') {
        $replace_output['"../components/com_mijoshop/opencart/admin']                                           = "MPATH_MIWOSHOP_OC.".'/admin';
        $replace_output['\'../components/com_mijoshop/opencart/admin']                                          = "MPATH_MIWOSHOP_OC."."/admin";
        $replace_output['"components/com_mijoshop/opencart/admin']                                              = "MPATH_MIWOSHOP_OC.".'/admin';
        $replace_output['\'components/com_mijoshop/opencart/admin']                                             = "MPATH_MIWOSHOP_OC."."/admin";
        $replace_output['((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . \'index.php?token=\''] 				= '"admin.php?page=miwoshop&option=com_miwoshop&token="';
        $replace_output['index.php?route='] 																	= 'index.php?option=com_miwoshop&route=';
        $replace_output['index.php?token='] 																	= 'index.php?option=com_miwoshop&token=';
       // $replace_output['((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . \'index.php?token=\''] 				= 'admin.php?page=miwoshop&option=com_miwoshop&token=';
        $replace_output['"<link rel="stylesheet" type="text/css" href="index.php?option=com_miwoshop&'] 		= '<link rel="stylesheet" type="text/css" href="<?php echo admin_url()?>/admin-ajax.php?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&';
        $replace_output['\'<link rel="stylesheet" type="text/css" href="index.php?option=com_miwoshop&'] 		= '<link rel="stylesheet" type="text/css" href="<?php echo admin_url()?>/admin-ajax.php?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&';
        $replace_output['$this->document->addStyle(\'view/'] 								        	        = "MiwoShop::get('base')->addHeader(MPATH_MIWOSHOP_OC . '/admin/view/";
        $replace_output["HTTP_SERVER . 'admin/"] 							                                	= "HTTP_SERVER . MPATH_MIWOSHOP_OC."."'/admin/";
    }

    if ($source == 'site') {
        $replace_output['"../components/com_mijoshop/opencart/']                                                = "MPATH_MIWOSHOP_OC. \"";
        $replace_output['\'../components/com_mijoshop/opencart/']                                               = "MPATH_MIWOSHOP_OC. \'";
        $replace_output['"components/com_mijoshop/opencart/']                                                   = "MPATH_MIWOSHOP_OC. \"";
        $replace_output['\'components/com_mijoshop/opencart/']                                                  = "MPATH_MIWOSHOP_OC. \'";
        $replace_output['"components/com_mijoshop/opencart/']                                                   = "MPATH_MIWOSHOP_OC. \"";
        $replace_output['class="box"'] 												                            = 'class="box_oc"';
        $replace_output['class="button_oc"'] 										                            = 'class="'.MiwoShop::getButton().'"';
        $replace_output['class="button"'] 											                            = 'class="'.MiwoShop::getButton().'"';
        $replace_output['id="button"'] 												                            = 'class="'.MiwoShop::getButton().'"';
        $replace_output[' src="catalog/'] 											                            = ' src="'.MURL_MIWOSHOP.'/site/opencart/catalog/';
        $replace_output[' src="image/'] 											                            = ' src="'.MURL_MIWOSHOP.'/site/opencart/image/';
    }
	
	if ($source == 'vqmod'){
		$replace_output["'../'. 'components/com_mijoshop/opencart"]												= "MPATH_MIWOSHOP_OC.'";
		$replace_output["'../'.'components/com_mijoshop/opencart"]												= "MPATH_MIWOSHOP_OC.'";
		$replace_output['HTTPS_CATALOG."components/com_mijoshop/opencart']										= "MPATH_MIWOSHOP_OC.\"";
		$replace_output['HTTP_CATALOG."components/com_mijoshop/opencart']										= "MPATH_MIWOSHOP_OC.\"";
	}

    if ($source == 'js'){
        $replace_output[": 'index.php?route="] 										                            = ":miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&route=";
        $replace_output[': "index.php?route='] 												                    = ":miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&route=";
        $replace_output[":'index.php?route="] 											                        = ":miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&route=";
        $replace_output[':"index.php?route='] 												                    = ":miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&route=";
		$replace_output[": 'index.php?option=com_mijoshop&route="] 										        = ":miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&route=";
        $replace_output[': "index.php?option=com_mijoshop&route='] 												= ":miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&route=";
        $replace_output[":'index.php?option=com_mijoshop&route="] 											    = ":miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&route=";
        $replace_output[':"index.php?option=com_mijoshop&route='] 											    = ":miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&route=";
    }

    if ($source == 'admin' || $source == 'site') {
        $replace_output["jQuery.post('index.php?route="] 									                    = "jQuery.post('miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output["jQuery.post('index.php?option=com_mijoshop&route="] 				                    = "jQuery.post('miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output[".load('index.php?option=com_mijoshop&route="] 			        	                    = ".load(miwiajaxurl + '?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output[".load('index.php?route="] 									                            = ".load(miwiajaxurl + '?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output[": 'index.php?option=com_mijoshop&route="] 					                            = ":miwiajaxurl + '?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output[': "index.php?option=com_mijoshop&route='] 				                                = ':miwiajaxurl + "?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=';
        $replace_output[": 'index.php?route="] 										                            = ":miwiajaxurl + '?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output[': "index.php?route='] 										                            = ':miwiajaxurl + "?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=';
        $replace_output[": 'index.php?option=com_mijoshop&format=raw"] 				                            = ":miwiajaxurl + '?action=miwoshop&option=com_miwoshop&format=raw";
        $replace_output[': "index.php?option=com_mijoshop&format=raw'] 			        	                    = ':miwiajaxurl + "?action=miwoshop&option=com_miwoshop&format=raw';

        $replace_output["ajaxurl = 'index.php?option=com_miwoshop&route"] 				                        = "ajaxurl = miwiajaxurl + '?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output['ajaxurl = "index.php?option=com_miwoshop&route'] 				                        = "ajaxurl = miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output["ajaxurl = 'index.php?route="] 					                                        = "ajaxurl = miwiajaxurl + '?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output['ajaxurl = "index.php?route='] 					                                        = "ajaxurl = miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component&route=";
        $replace_output["ajaxurl = 'index.php?option=com_miwoshop&format=raw"] 				                    = "ajaxurl = miwiajaxurl + '?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component";
        $replace_output['ajaxurl = "index.php?option=com_miwoshop&format=raw'] 			                        = "ajaxurl = miwiajaxurl + \"?action=miwoshop&option=com_miwoshop&format=raw&tmpl=component";
    }

        $replace_output['JPATH_MIWOSHOP_OC'] 		    = 'MPATH_MIWOSHOP_OC';
        $replace_output['JPATH_MIWOSHOP_LIB'] 		    = 'MPATH_MIWOSHOP_LIB';
        $replace_output['JPATH_MIWOSHOP_SITE'] 		    = 'MPATH_MIWOSHOP_SITE';
        $replace_output['JPATH_MIWOSHOP_ADMIN'] 		= 'MPATH_MIWOSHOP_ADMIN';
        $replace_output['JConfig'] 		                = 'MConfig';
        $replace_output['JController'] 		            = 'MController';
        $replace_output['JRequest'] 		            = 'MRequest';
        $replace_output['JDatabase'] 		            = 'MDatabase';
        $replace_output['JDate'] 		                = 'MDate';
        $replace_output['JEditor'] 		                = 'MEditor';
        $replace_output['JFactory'] 		            = 'MFactory';
        $replace_output['JFile'] 		                = 'MFile';
        $replace_output['JPath'] 		                = 'MPath';
        $replace_output['JRegistry'] 		            = 'MRegistry';
        $replace_output['JRoute'] 		                = 'MRoute';
        $replace_output['jimport(\'joomla.'] 		    = 'mimport(\'framework.';
        $replace_output['jimport'] 		                = 'mimport';
        $replace_output['jexit'] 		                = 'mexit';
        $replace_output['Joomla.checkAll(this)'] 		= 'Miwi.checkAll(this)';
        $replace_output['JACTION'] 		                = 'MACTION';
        $replace_output['JAdministratorHelper'] 		= 'MAdministratorHelper';
        $replace_output['JApplication'] 		       	= 'MApplication';
        $replace_output['JArchive'] 		           	= 'MArchive';
        $replace_output['JArrayHelper'] 		       	= 'MArrayHelper';
        $replace_output['JBrowser'] 		           	= 'MBrowser';
        $replace_output['JCache'] 		               	= 'MCache';
        $replace_output['JDispatcher'] 		           	= 'MDispatcher';
        $replace_output['JDocument'] 		           	= 'MDocument';
        $replace_output['JError'] 		               	= 'MError';
        $replace_output['JFEATURED'] 		           	= 'MFEATURED';
        $replace_output['JTable'] 		               	= 'MTable';
        $replace_output['JText'] 		               	= 'MText';
        $replace_output['COM_MIWOSHOP'] 		        = 'COM_MIWOSHOP';

        $replace_output['JiwoShop'] 					= 'MiwoShop';
        $replace_output['jiwoshop'] 					= 'miwoshop';

        $replace_output_regex['~JPATH_(ROOT|SITE)\s*.\s*("|\')/components/com_mijo([a-zA-Z0-9_\.\-]+)~'] 		                 = 'MPATH_WP_PLG.$2/miwo$3/site';
        $replace_output_regex['~JPATH_(ROOT|ADMINISTRATOR|SITE)\s*.\s*("|\')/(administrator/|)components/com_mijoshop~'] 		 = 'MPATH_WP_PLG.$2/miwo$4/admin';
        $replace_output_regex['~J(Uri|URI)::root\((true|false|)\)\s*.\s*("|\')(/|)components/com_miwoshop~'] 	                 = 'MURL_MIWO$5.$3/site/$6$7';
        $replace_output_regex['~<\?php echo J(Uri|URI)::root\((true|false|)\)(\W+)\?>(/|)components/com_mijoshop~'] 		     = '<?php echo MURL_MIWO$5; ?>/site/$6';
        $replace_output_regex['~(addStyleSheet|addScript|stylesheet|script)\(("|\')components/com_mijoshop~'] 		             = '$1(MURL_MIWO$3.$2/site';
        $replace_output_regex['~(href|src)\s*=\s*("|\')(/|)components/com_mijoshop~'] 		                                     = '$1=$2<?php echo MURL_MIWO$4; ?>/site';
        $replace_output_regex['~J(Show|SHOW|HIDE|Hide)~'] 		                                                                 = 'M$1';
        $replace_output_regex['~(JURI|JUri|Juri)~'] 		                                                                     = 'MUri';

        foreach($replace_output_regex as $key => $value) {
            $edit_page = preg_replace($key, $value, $edit_page);
        }

        foreach($replace_output as $key => $value) {
            $edit_page = str_replace($key, $value, $edit_page);
        }

        return $edit_page;
    }
		
	public function permission_control($edit_page_url){
        $search_page = array(
            'product' 				=> 'product',
            'common' 				=> 'common',
            'design' 		    	=> 'design',
            'error' 				=> 'error',
            'extension' 			=> 'extension',
            'feed' 		        	=> 'feed',
            'localisation' 		    => 'localisation',
            'module' 	    		=> 'module',
            'payment' 				=> 'payment',
            'report'                => 'report',
            'sale' 				    => 'sale',
            'setting' 				=> 'setting',
            'shipping' 				=> 'shipping',
            'tool'   				=> 'tool',
            'total' 				=> 'total',
            'user' 				    => 'user'
        );
        foreach($search_page as $page) {
        if (strpos($edit_page_url, 'upload/admin/controller/'.$page)!== false){

            $permissions_page = explode($page,$edit_page_url);
            $permissions_page = $page.str_replace('.php','',$permissions_page[1]);

            $this->permission($permissions_page);

        }
        }
    }

    public function permission($page){
        $jdb = MiwoShop::get('db')->getDbo();

        //insert permission for support/support
        $jdb->setQuery("SELECT permission FROM `#__miwoshop_user_group` WHERE `user_group_id` = 1");
        $permission = $jdb->loadResult();
        $permission = unserialize($permission);

            if (!array_search($page, $permission['access'])){
                $permission['access'][] = $page;
                $permission['modify'][] = $page;
            }

        $permission = serialize($permission);

        $jdb->setQuery("UPDATE `#__miwoshop_user_group` SET `permission` = '".$permission."' WHERE `user_group_id` = 1");
        $jdb->query();
    }
	#miwoshop-finish
	
	public function miwi_sql() {
		$this->load->language('extension/installer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$file = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/install.sql';

		if (!file_exists($file)) {
			$json['error'] = $this->language->get('error_file');
		}

		if (!$json) {
			$lines = file($file);

			if ($lines) {
				try {
					$sql = '';

					foreach ($lines as $line) {
						if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
							$sql .= $line;

							if (preg_match('/;\s*$/', $line)) {
								$sql = str_replace(" `oc_", " `" . DB_PREFIX, $sql);

								$this->db->query($sql);

								$sql = '';
							}
						}
					}
				} catch(Exception $exception) {
					$json['error'] = sprintf($this->language->get('error_exception'), $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function miwi_xml() {
		$this->load->language('extension/installer');

        $isVqmod = 0; #miwoshop start
		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		#miwoshop start
        if(file_exists(DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/install.xml')){
            $file = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/install.xml';
        } else if (file_exists(DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/' . $this->session->data['vqmod_file_name'])){
            $file = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/' . $this->session->data['vqmod_file_name'];
        }
		#miwoshop finish
		
		if (!file_exists($file)) {
			$json['error'] = $this->language->get('error_file');
		}

		if (!$json) {
            $json['overwrite'] = array(); #miwoshop-start
			$this->load->model('extension/modification');

			// If xml file just put it straight into the DB
			$xml = file_get_contents($file);

			if ($xml) {
				try {
					$dom = new DOMDocument('1.0', 'UTF-8');
					$dom->loadXml($xml);
					
					$name = $dom->getElementsByTagName('name')->item(0);

					if ($name) {
						$name = $name->nodeValue;
					} else {
                        $id = $dom->getElementsByTagName('id')->item(0);

                        if (!empty($id)) {
                            $name = $id->nodeValue;
                        } else {
                            $name = '';
                        }
					}
					
					$code = $dom->getElementsByTagName('code')->item(0);
					if(!empty($code)) {
						if ($code) {
							$code = $code->nodeValue;
							
							// Check to see if the modification is already installed or not.
							$modification_info = $this->model_extension_modification->getModificationByCode($code);
							
							if ($modification_info) {
								$json['overwrite'][] =  $code . '.xml'; #miwoshop-start
								$json['error'] = sprintf($this->language->get('error_exists'), $modification_info['name']);
							}
						} else {
							$json['error'] = $this->language->get('error_code');
						}
					} else {
                        $_path = basename($file);
                        $_path = explode('.', $_path);
                        
                        if (empty($code) && !empty($_path[0])) {
                            $code = $_path[0];
                        }
                        
                        $isVqmod = 1 ;
                    }	

					$author = $dom->getElementsByTagName('author')->item(0);

					if ($author) {
						$author = $author->nodeValue;
					} else {
						$author = '';
					}

					$version = $dom->getElementsByTagName('version')->item(0);

					if ($version) {
						$version = $version->nodeValue;
					} else {
						$version = '';
					}

					$link = $dom->getElementsByTagName('link')->item(0);

					if ($link) {
						$link = $link->nodeValue;
					} else {
						$link = '';
					}

                    #Xml replace text '', Because It doesn't necessary MiwoShop
                    $xml = ''; #miwoshop-start
					
					$modification_data = array(
						'name'    => $name,
						'code'    => $code,
						'author'  => $author,
						'version' => $version,
						'link'    => $link,
						'xml'     => $xml,
						'status'  => 1
					);
					
					if (!$json['overwrite']) {
						$this->model_extension_modification->addModification($modification_data);
					}
					
                    #miwoshop-start
                    if($isVqmod) {
                        $file  = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/' . $this->session->data['vqmod_file_name'];
						$msmod = MPATH_MIWOSHOP_OC.'/vqmod/xml/'. str_replace("upload/vqmod/xml/","",$this->session->data['vqmod_file_name']);
                    } else {
                        if(is_file(DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']).'/install.xml')){
                            $file  = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']).'/install.xml';
                        } else{
                            $file = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/' . $this->session->data['vqmod_file_name'];
                        }
                        $msmod = DIR_SYSTEM.'xmls/'.$code.'.xml';
                    }

                    if (!copy($file, $msmod)) {
                        $json['error'] =   $this->language->get('error_copy_xmls_file');
                    }
                    #miwoshop-finish

				} catch(Exception $exception) {
					$json['error'] = sprintf($this->language->get('error_exception'), $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
				}
			}
		}

        unset($this->session->data['vqmod_file_name']);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function php() {
		$this->load->language('extension/installer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$file = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']) . '/install.php';

		if (!file_exists($file)) {
			$json['error'] = $this->language->get('error_file');
		}

		if (!$json) {
			try {
				include($file);
			} catch(Exception $exception) {
				$json['error'] = sprintf($this->language->get('error_exception'), $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function remove() {
		$this->load->language('extension/installer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$directory = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']);

		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json) {
			// Get a list of files ready to upload
			$files = array();

			$path = array($directory);

			while (count($path) != 0) {
				$next = array_shift($path);

				// We have to use scandir function because glob will not pick up dot files.
				foreach (array_diff(scandir($next), array('.', '..')) as $file) {
					$file = $next . '/' . $file;
					
					if (is_dir($file)) {
						$path[] = $file;
					}

					$files[] = $file;
				}
			}

			sort($files);
			rsort($files);

			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				} elseif (is_dir($file)) {
					rmdir($file);
				}
			}

			if (file_exists($directory)) {
				rmdir($directory);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function clear() {
		$this->load->language('extension/installer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$directories = glob(DIR_UPLOAD . 'temp-*', GLOB_ONLYDIR);

			foreach ($directories as $directory) {
				// Get a list of files ready to upload
				$files = array();

				$path = array($directory);

				while (count($path) != 0) {
					$next = array_shift($path);
					
					// We have to use scandir function because glob will not pick up dot files.
					foreach (array_diff(scandir($next), array('.', '..')) as $file) {
						$file = $next . '/' . $file;
						
						if (is_dir($file)) {
							$path[] = $file;
						}

						$files[] = $file;
					}
				}

				rsort($files);

				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					} elseif (is_dir($file)) {
						rmdir($file);
					}
				}

				if (file_exists($directory)) {
					rmdir($directory);
				}
			}

			$json['success'] = $this->language->get('text_clear');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
