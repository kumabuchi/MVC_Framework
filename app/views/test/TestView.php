<html>
<head>
<title>TEST PAGE</title>
</head>
<body>
<?php
print("
<ul>
<li>aaa</li>
<li>bbb</li>
<li>ccc</li>
</ul>
");
print('<h3>'.$this->get('tmp').'</h3>');
var_dump($this->get('tmp2'));
?>
</body>
</html>
