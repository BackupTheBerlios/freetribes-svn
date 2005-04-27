<?

function google_user_email($email)
{
    global $db, $dbtables;
    $page = '';
    $readyemail = ereg_replace("@", "%40", $email );

    $url = "http://www.google.com/groups?q=$readyemail&ie=UTF-8&hl=en&btnG=Google+Search";
    $fh = fopen($url,"r");
    $p = 1;
    while( !feof( $fh ) )
    {
        while( $p < 100 )
        {
            $page .= fread( $fh, 10000 );
            $p++;
        }
    }
    if( preg_match( '/did not match any documents/' , $page ) )
    {
        echo "&nbsp;Lookup $email was not found on google news groups.<BR>";
    }
    elseif( preg_match( "Results", $page ) )
    {
        echo "&nbsp;Lookup $email found on google newsgroups!<BR>\n";
    }
    else 
    {
        echo "&nbsp;Looking up $email, Something is wrong <BR> $page";
    }
    fclose($fh);
    $fh = '';
    $page = '';
    $url = "http://groups.google.com/search?hl=en&lr=&ie=UTF-8&q=$readyemail&btnG=Search";
    $fh = fopen($url,"r");
    $p = 1;
    while( !feof( $fh ) )
    {
        while( $p < 100 )
        {
            $page .= fread( $fh, 10000 );
            $p++;
        }
    }
    if( preg_match( '/did not match any documents/' , $page ) )
    {
        echo "&nbsp;Lookup $email was not found on google web search.<BR>";
    }
    elseif( preg_match( "Results", $page ) )
    {
        echo "&nbsp;Lookup $email found on google web search!<BR>\n";
    }
    else
    {
        echo "&nbsp;Looking up $email, Something is wrong <BR> $page";
    }
    fclose($fh);

}

?>
