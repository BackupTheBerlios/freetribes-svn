<?php
// Globally useful functions in the GUI

function help_page_link ()
{
	ereg("([^/]*)php", $_SERVER['PHP_SELF'], $fname);
        if( $_SESSION[username] ) 
        {
	    echo "<a href=\"helper.php?query=1&amp;type=page&amp;value=$fname[0]\" target=ts_helper>Help&nbsp;For&nbsp;This&nbsp;Page</a>";
        }
}


function help_link ($link_text, $id, $type, $value, $title)
{
	$help_refs="";

	if ($id<>"")
	{
		$help_refs.="&amp;id=$id";
	}
	if ($type<>"")
	{
		$help_refs.="&amp;type=$type";
	}
	if ($value<>"")
	{
		$help_refs.="&amp;value=$value";
	}
	if ($id<>"")
	{
		$help_refs.="&amp;htitle=$title";
	}

	echo "<a href=\"helper.php?query=1$help_refs\" target=ts_helper>$link_text</a>";
}


function bigtitle()
{
  global $title;

}


function page_header($t)
{
	global $page_title, $game_root;
	
	$page_title = $t;
	$GLOBALS['page_title'] = $t;
	include($game_root."header.php");
}

function page_footer()
{
	global $game_root;
	include($game_root."footer.php");
}

function TEXT_GOTOMAIN()
{
  echo "Click <a href=main.php>here</A> to return to the main menu.";
}

function TEXT_GOTOLOGIN()
{
  echo "Click <a href=index.php>here</a> to log in.";
}

function TEXT_JAVASCRIPT_BEGIN()
{
  echo "\n<SCRIPT LANGUAGE=\"JavaScript\">\n";
  echo "<!--\n";
}

function TEXT_JAVASCRIPT_END()
{
  echo "\n// -->\n";
  echo "</SCRIPT>\n";
}

function COMBAT($target)
{
   header("location:combat.php?target=$target");
}


?>
