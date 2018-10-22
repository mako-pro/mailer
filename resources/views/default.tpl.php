<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Default Message</title>
</head>
<body>
<div>
    <h1>Default Message</h1>
    <h3>Hello, {{ $name }}!<h3>
    {% if(isset($cid_1)) %}
    <img src="cid:{{ $cid_1 }}">
    {% endif %}
</div>
</body>
</html>
