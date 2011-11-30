<?php
$upload_dir = '/images/avatar/temp/'; // Directory for file storing
$preview_url = 'http://localhost/tarefas/images/avatar/temp/';
$filename= '';
$result = 'ERROR';
$result_msg = '';
$allowed_image = array ('image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg','image/png');
define('PICTURE_SIZE_ALLOWED', 2242880); // bytes

if (isset($_FILES['picture']))  // file was send from browser
{
	if ($_FILES['picture']['error'] == UPLOAD_ERR_OK)  // no error
	{
		if (in_array($_FILES['picture']['type'], $allowed_image)) {
			if(filesize($_FILES['picture']['tmp_name']) <= PICTURE_SIZE_ALLOWED) // bytes
			{
				$filename = $_FILES['picture']['name'];
				move_uploaded_file($_FILES['picture']['tmp_name'], $upload_dir.$filename);
				
				//phpclamav clamscan for scanning viruses
				//passthru('clamscan -d /var/lib/clamav --no-summary '.$upload_dir.$filename, $virus_msg); //scan virus
				$virus_msg = 'OK'; //assume clamav returing OK.
				if ($virus_msg != 'OK') {
					unlink($upload_dir.$filename);
					$result_msg = $filename." : ".FILE_VIRUS_AFFECTED;
					$result_msg = '<font color=red>'.$result_msg.'</font>';
					$filename = '';
				}else {
					// main action -- move uploaded file to $upload_dir
					$result = 'OK';
				}
			}else {
				$filesize = filesize($_FILES['picture']['tmp_name']);// or $_FILES['picture']['size']
				$filetype = $_FILES['picture']['type'];
				$result_msg = PICTURE_SIZE;
			}
		}else {
			$result_msg = SELECT_IMAGE;
		}
	}
	elseif ($_FILES['picture']['error'] == UPLOAD_ERR_INI_SIZE)
		$result_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
	else
		$result_msg = 'Unknown error';
}

// This is a PHP code outputing Javascript code.
echo '<script language="JavaScript" type="text/javascript">'."\n";
echo 'var parDoc = window.parent.document;';
if ($result == 'OK') {
	echo 'parDoc.getElementById("picture_error").innerHTML =  "";';
}
else {
	echo "parDoc.getElementById('picture_error').innerHTML = '".$result_msg."';";
}

if($filename != '') {
	echo "parDoc.getElementById('picture_preview').innerHTML = '<img src=\'$preview_url$filename\' id=\'preview_picture_tag\' name=\'preview_picture_tag\' />';";
}

echo "\n".'</script>';
exit(); // do not go futher

?>