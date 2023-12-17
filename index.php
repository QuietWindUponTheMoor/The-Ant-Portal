<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");
?>

<body>
    <div class="container">
    
        

    </div>
</body>
<script type="text/javascript">
$.ajax({  
    type: "POST",  
    url: "http://127.0.0.1:81/", 
    data: {test: "Hello!"},
    processData: false,
    contentType: false,
    success: function (response) {
        console.log(response);
    }
});
</script>

<?php require($root."/includes/footer.php"); ?>
