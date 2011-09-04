<html>
<head>
    <title>tEABAG_3D sandbox</title>

    <script type="text/javascript" src="/js/jquery-1.4.3.min.js"></script>
    <script type="text/javascript">

        function img_reload(conf) {
            $('#simage').src = 'getimage.php?conf=1';
            $('#out').append('<img id="sampleImage" />').css('display','inline');
            $('#sampleImage').attr('src','getimage.php?conf=' + conf);
            alert($('#sampleImage').attr('src'));
        }


        function realert() {
            alert('a');
        }
    </script>
</head>

<body>


<h2>Bells and whistles</h2>

<div>
    <form action="#">


    </form>
    <a href="#" onclick="javascript:realert();return false;">getImage</a>

    <a href="#" onclick="img_reload(Math.random(10));return false;">getImage</a>

</div>


<h2>Sample output</h2>

<img alt="" id="simage">


<img alt="" id="simage2" src="getimage.php?conf=1">

<div id="out">
    <ul id="sample_list" style="float:left;">
    </ul>
</div>


</body>
</html>