<?php
session_start();
header("Cache-control: private");
$admincheck = $_SESSION['username'];
//here we should query the db to get admin's username or id or some such to auth
if($admincheck !== 'admin')
{
die("You Do Not	Have permissions to view this page!");
}
include("config.php");
page_header("Admin - File Upload");
include("game_time.php");

connectdb();

$username = $_SESSION['username'];
$admin = $db->Execute("SELECT * FROM $dbtables[chiefs] WHERE username = '$username'");
$admininfo = $admin->fields;

if(!$admininfo[admin] >= $privilege['adm_dev'])
{
	echo "You must be an administrator to use this tool.<BR>\n";
	page_footer();
}


require("fileupload-class.php");

#--------------------------------#
# Variables
#--------------------------------#

// The path to the directory where you want the 
// uploaded files to be saved. This MUST end with a 
// trailing slash unless you use $path = ""; to 
// upload to the current directory. Whatever directory
// you choose, please chmod 777 that directory.

//$path = "./";
	if (!ISSET($_REQUEST['directory']))
	{
		chdir(".\\dev\\");
		$_REQUEST['directory'] = ".\\dev";
	}
	elseif (ISSET($_REQUEST['change_dir']))
	{
		chdir(".\\".$_REQUEST['directory']);
	}
	
    $path = getcwd();

// The name of the file field in your form.

	$upload_file_name = "userfile";

// ACCEPT mode - if you only want to accept
// a certain type of file.
// possible file types that PHP recognizes includes:
//
// OPTIONS INCLUDE:
//  text/plain
//  image/gif
//  image/jpeg
//  image/png
	
	// Accept ONLY gifs's
	#$acceptable_file_types = "image/gifs";
	
	// Accept GIF and JPEG files
	#$acceptable_file_types = "image/gif|image/jpeg|image/pjpeg";
	
	// Accept ALL files
	$acceptable_file_types = "";

// If no extension is supplied, and the browser or PHP
// can not figure out what type of file it is, you can
// add a default extension - like ".jpg" or ".txt"

	$default_extension = "";

// MODE: if your are attempting to upload
// a file with the same name as another file in the
// $path directory
//
// OPTIONS:
//   1 = overwrite mode
//   2 = create new with incremental extention
//   3 = do nothing if exists, highest protection

	$mode = 1;
	
#--------------------------------#
# PHP
#--------------------------------#
	if (isset($_REQUEST['submitted'])) {
		/* 
			A simpler way of handling the submitted upload form
			might look like this:
			
			$my_uploader = new uploader('en'); // errors in English
	
			$my_uploader->max_filesize(30000);
			$my_uploader->max_image_size(800, 800);
			$my_uploader->upload('userfile', 'image/gif', '.gif');
			$my_uploader->save_file('uploads/', 2);
			
			if ($my_uploader->error) {
				print($my_uploader->error . "<br><br>\n");
			} else {
				print("Thanks for uploading " . $my_uploader->file['name'] . "<br><br>\n");
			}
		*/
			
		// Create a new instance of the class
		$my_uploader = new uploader($_POST['language']); // for error messages in french, try: uploader('fr');
		
		// OPTIONAL: set the max filesize of uploadable files in bytes
		$my_uploader->max_filesize(15000000);
		
		// OPTIONAL: if you're uploading images, you can set the max pixel dimensions 
		$my_uploader->max_image_size(8000, 8000); // max_image_size($width, $height)
		
		// UPLOAD the file
		if ($my_uploader->upload($upload_file_name, $acceptable_file_types, $default_extension)) {
			$my_uploader->save_file($path, $mode);
		}
		
		if ($my_uploader->error) {
			echo $my_uploader->error . "<br><br>\n";
		} else {
			// Successful upload!
			print($my_uploader->file['name'] . " was successfully uploaded to $path! <a href=\"" . $_SERVER['PHP_SELF'] . "\">Try Again</a><br>");
			
			// Print all the array details...
			//print_r($my_uploader->file);
			
			// ...or print the file
			if(stristr($my_uploader->file['type'], "image")) {
                                $filename = $my_uploader->file['name'];
				echo "<img src=\"" . $path . $my_uploader->file['name'] . "\" border=\"0\" alt=\"\">";
                                $result2 = $db->Execute("INSERT INTO $dbtables[logs] "
                                                        ."VALUES("
                                                        ."'',"
                                                        ."'$month[count]',"
                                                        ."'$year[count]',"
                                                        ."'0000',"
                                                        ."'0000.00',"
                                                        ."'DEBUG',"
                                                        ."'$stamp',"
                                                        ."'FILE UPLOAD: $filename uploaded to $path by $_SESSION[username].')");
    if( !$result2 )
    {
        echo $db->ErrorMsg() . "<br>";
    }

			} else {
				$fp = fopen($path . $my_uploader->file['name'], "r");
                                $filename = $my_uploader->file['name'];
                                $result2 = $db->Execute("INSERT INTO $dbtables[logs] "
                                                        ."VALUES("
                                                        ."'',"
                                                        ."'$month[count]',"
                                                        ."'$year[count]',"
                                                        ."'0000',"
                                                        ."'0000.00',"
                                                        ."'DEBUG',"
                                                        ."'$stamp',"
                                                        ."'FILE UPLOAD: $filename uploaded to $path by $_SESSION[username].')");
    if( !$result2 )
    {
        echo $db->ErrorMsg() . "<br>";
    }

				while(!feof($fp)) {
					$line = fgets($fp, 255);
					echo $line;
				}
				if ($fp) { fclose($fp); }
			}
 		}
 	}




#--------------------------------#
# HTML FORM
#--------------------------------#
$dir = array();
$move_back = 1;
while ($move_back==1)
{
	$move_back=0;
	unset($dir);
	if ($handle = opendir('.'))
	{ 
		$file = readdir($handle);
		$i=0;
		while ( false !== ($file = readdir($handle)) ) 
		{
			
			if ($file <> "." && is_dir($file))
			{
				if ($file=="dev" && is_dir($file))
				{
					$move_back = 1;
					echo "Changing dir<br>";
				}
				else
				{
					$dir[$i] = $file;
					echo "$file<br>";
					$i++;
				}
			}
			$file = readdir($handle);
		}
	}
	closedir($handle); 
	if ($move_back==1)
	{
		chdir(".\dev");
	}
}


echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">"
	."<input type=\"hidden\" name=\"submitted\" value=\"true\">"
	."Upload this file:<br>"
	."<input CLASS=edit_area name=\"<?= $upload_file_name; ?>\" type=\"file\">"
	."<br><br>"
	."Select Directory:<BR>"
	."<select name=\"directory\">";

$cwd0 = getcwd();
ereg("([/\]tribe[/\])(.*)", $cwd0, $cwd1); // get the name of the file being viewed


foreach ($dir as $d)
{
	echo "<option value=\"$d\">$d</option>";
}

echo "</select>"
	."<br><br>"
	."<input type=\"submit\" name=change_dir value=\"Change Dir\">"
	."</form>"
	."<hr>";

echo "CWD: $cwd0<br>";
echo count($dir)."<BR>";
echo "<PRE>";
print_r ($cwd1);
print_r ($dir);
echo "</PRE>";

if (isset($acceptable_file_types) && trim($acceptable_file_types)) {
	print("This form only accepts <b>" . str_replace("|", " or ", $acceptable_file_types) . "</b> files\n");
}

page_footer();
?>

