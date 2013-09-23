
<?php if(!isset($_FILES['inputFile'])) : ?>

    <h2>Choose file to convert to XML</h2>
    <p><i>File must be .csv</i></p>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <label for="inputFile"></label>
        <input type="file" name="inputFile" />
        <br />
        <input type="submit" value="Create XML" />
    </form>

<?php else : 

    require_once('create_xml.php');
    $init = new Create_xml($_FILES['inputFile']);

endif; ?>

