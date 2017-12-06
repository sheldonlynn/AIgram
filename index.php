<?php
if (isset($_POST['submit'])) {
    $file = $_FILES["fileToUpload"]["tmp_name"];

    $path = pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);

    $maxsize    = 4097152;

    $fileSize = filesize($file);

    if($fileSize >= $maxsize || $fileSize == 0) {
        echo 'File too large. File must be less than 4mB.';
        die();
    }

    $data = base64_encode(file_get_contents( $file ));
}
?>

<html>
<head>
    <script
            src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.4/lodash.min.js"></script>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<div id="content">
    <h1>
        AIgram
    </h1>
    <div id="upload">
        <div id="caption">
            Harness the power of Artificial Intelligence to caption your photos.
        </div>
        <form name="submit-pho" id="submit-photo" method="post" action="index.php" enctype="multipart/form-data">
            <label id="selectFile">
                <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">
            </label>
            <input type="submit" value="Get Tags!" name="submit">
        </form>
    </div>
    <div id="display">
        <div id="displayImageContainer">
            <img id="displayImage" src=" " />
        </div>
        <div id="displayTags">
        </div>

    </div>
</div>
<script>
    var data = <?php echo (isset($data) && strlen($data) > 0) ? json_encode($data) : "null" ?>;
    var path = <?php echo (isset($path) && strlen($path) > 0) ? json_encode($path) : "null" ?>;

    if (data) {
        var apiKey = 'AIzaSyDpM70NiH2mfIa2jMx2G9JHmJXjx2UUAxw';
        var request = {
            "requests":[
                {
                    "image":{
                        "content": data
                    },
                    "features":[
                        {
                            "type":"LABEL_DETECTION",
                            "maxResults": 30
                        }
                    ]
                }
            ]
        };

        //set imageSource
        $('#displayImage').attr("src", 'data:img/' + path + ';base64,' + data);

        $.ajax({
            type: 'POST',
            url: 'https://vision.googleapis.com/v1/images:annotate?key=' + apiKey,
            dataType: 'json',
            data: JSON.stringify(request),
            headers: {
                "Content-Type": "application/json"
            },
            success: function(data, textStatus, jqXHR) {
//                console.log(data, "data");
                _.map(data.responses[0].labelAnnotations, function(label) {
                   $('#displayTags').append("#" + label.description.replace(/ /g,'') + " ");
                });
//                $('#displayTags').text(JSON.stringify(data));
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Errors: ' + textStatus + ' ' + errorThrown);
            }


        });
    }
</script>
</body>
</html>

