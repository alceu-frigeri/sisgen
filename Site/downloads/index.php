
<?php

function listDir($directoryName)
{
	$directory = opendir($directoryName);
	$files = array();
	while ($elem = readdir($directory))
	{
		if ($elem != '.' && $elem != '..' && $elem != '.htaccess')
		{
			$files[] = $elem;
		}
	}
	closedir();
	if (!empty($directory))
	{
		sort($files);
		echo "<h2>Call for papers </h1>";
		echo "<ul>";
		foreach ($files as $file)
		{
			$filePath = $directoryName.'/'.$file;
			echo "<form method='post' action=".htmlentities($_SERVER['PHP_SELF']).">";
			echo "<li>";
			echo "<input type='hidden' name='$file' value='$filePath'>";
			echo " <button type='submit' name='getFile'>'$file'</button> ";
			echo "$file</li>";
			echo "</form>";
		}
		echo "</ul>";
	}
} 

class Item
{
	public $name;
	public $fullPath;
	public $updated;	
}

function mountForm(){
	
	$items = array();

	$item = new Item();
	$item->name = "Folder (.pdf)";
	$item->fullPath = "./files/folder.pdf";
	$item->updated = "09/02/2016";
	$items[] = $item;

	$item = new Item();
	$item->name = "ASCII (.txt)";
	$item->fullPath = "./files/text.txt";
	$item->updated = "09/02/2016";
	$items[] = $item;

	$item = new Item();
	$item->name = "HTML (.html)";
	$item->fullPath = "./files/cfp.html";
	$item->updated = "09/02/2016";
	$items[] = $item;

	echo "<h2>Call for papers </h1>";
	echo "<table cellpadding='10' class='itemstable'>";
	echo "<tr><th>Link</th><th>Filename</th><th>update</th></tr>";
	foreach ($items as $i)
	{		
		echo "<form method='post' action=".htmlentities($_SERVER['PHP_SELF']).">";
		echo "<tr>";
		echo "<input type='hidden' name='$i->name' value='$i->fullPath'>";
		echo "<td><button type='submit' name='getFile'>Download</button></td>";
		echo "<td>$i->name</td>";
		echo "<td>$i->updated</td>";
		echo "</tr>";
		echo "</form>";
	}
	echo "</table>";

}

function getFile($file)
{
	if (file_exists($file))
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		exit;
	}
}

if (isset($_POST['getFile']))
{
	$keys = array_keys($_POST);
	$file = $_POST[$keys[0]];
	getFile($file);
}
mountForm();

?> 


<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-75883469-1', 'auto');
  ga('send', 'pageview');

</script>