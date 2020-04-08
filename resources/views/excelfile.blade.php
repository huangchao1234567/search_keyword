<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <script src="https://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
    <title></title>
</head>
<body>
<form id="uploadForm" enctype="multipart/form-data">
    文件:<input id="file" type="file" name="file"/>
</form>
    <button id="upload">上传文件</button>
</body>
<script type="text/javascript">
	$(function () {
		$("#upload").click(function () {
			var formData = new FormData($('#uploadForm')[0]);
			var host = window.location.host;

			$.ajax({
				type: 'post',
				url: "http://" + host + "/admin/excelBatchAddFile/",
				data: formData,
				cache: false,
				processData: false,
				contentType: false,
			}).success(function (data) {
				alert(data);
			}).error(function () {
				alert("上传失败");
			});
		});
	});
</script>
</html>