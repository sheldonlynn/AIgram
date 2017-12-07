<?php
if (isset($_POST['submit-button'])) {
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
<!--    <link rel="stylesheet" href="reset.css" type="text/css">-->
    <link rel="stylesheet" href="style.css" type="text/css">
    <script>
        $(document).ready(function() {
            $('#fileToUpload').change(function() {
                $('#submit-photo').submit();
                $('displayImage').hide();
            });
        });
    </script>
</head>
<body>
<div id="content">
    <h1>
        aigram
    </h1>
    <div id="caption">
        Harness the power of Artificial Intelligence to caption your photos.
    </div>
    <img src="instagram-logo.png" id="logo"/>
    <div id="imageContainer">
        <img src="ripple.svg" id="spinner" />
        <div id="displayImage">
        </div>
        <div id="displayTags">
        </div>
    </div>
    <div id="upload">
        <form name="submit-photo" id="submit-photo" method="post" action="index.php" enctype="multipart/form-data">
            <label id="selectFile"> Upload Image
                <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">
            </label>
            <input type="hidden" name="submit-button" id="submit-button" value="submit-button" />
        </form>
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

        $('#upload').hide();

        $('#logo').hide();

        //set imageSource
        $('#displayImage').css("background-image", "url('data:img/" + path + ";base64," + data + "')");

        $('#imageContainer').css("display", "flex");
        $('#displayImage').css("opacity", "0.5");
        $('#spinner').show();
//        $('displayImageContainer').fadeIn('slow');

        $.ajax({
            type: 'POST',
            url: 'https://vision.googleapis.com/v1/images:annotate?key=' + apiKey,
            async : true,
            dataType: 'json',
            data: JSON.stringify(request),
            headers: {
                "Content-Type": "application/json"
            },
            success: function(data, textStatus, jqXHR) {
                _.map(data.responses[0].labelAnnotations, function(label) {
                   $('#displayTags').append("#" + label.description.replace(/ /g,'') + " ");
                   $('#displayTags').fadeIn("slow");
                   $('#displayImage').css("opacity", "1");
                   $('#spinner').hide();
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Errors: ' + textStatus + ' ' + errorThrown);
            }


        });
    }
</script>
</body>
</html>

