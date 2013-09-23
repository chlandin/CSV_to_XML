<?php

/**
 * Create_xml.php
 *
 * Create XML file in /xml_files/ from an uploaded CSV file
 */
class Create_xml extends XmlWriter
{
    public $filename;
    public $inputFile;
    public $outputFile;


    /**
     * Constructor
     * @param array $inputFile. From $_FILES['inputFile']
     * @return null
     */
    public function __construct($inputFile)
    {
        // Prevent csv file to be read as one whole line
        ini_set('auto_detect_line_endings', true);

        // Extract file name array
        $this->filename = pathinfo($inputFile['name']);

        // If file is set and extension is csv
        if(isset($this->filename['extension']) && $this->filename['extension'] == 'csv')  {

            $this->readCSV();

            $this->createOutputFile();

            $this->writeXML();

            unset($_FILES);

        } else {
            echo "Oops! Did you really select a .csv file? Try again.";
            echo "<br /><a href='/'>Back</a>";
        }
    }

    /**
     * Creates a SplFileObject 
     * @param null
     * @return null
     */
    public function readCSV() {
        $this->inputFile = new SplFileObject($this->inputFile["tmp_name"] . $this->filename["basename"]);
        // Read lines as CSV rows
        $this->inputFile->setFlags(SplFileObject::READ_CSV);
        // Set the delimiter and enclosure character for CSV
        $this->inputFile->setCsvControl(';', '"');
    }

    /**
     * Sets the path for the XML output file
     * @param null
     * @return null
     */
    public function createOutputFile() {
        $this->outputFile = $_SERVER['DOCUMENT_ROOT'] . 'xml_files/' . $this->filename['filename'] . '.xml';
        // Check if directory 'xml-files/' exists
        $dirname = dirname($this->outputFile);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
        // Check if file already exists
        if(file_exists($this->outputFile)) {
            // Add time stamp to file name
            $this->outputFile = $_SERVER['DOCUMENT_ROOT'] . 'xml_files/' . $this->filename['filename'] . '_' . time() . '.xml';
        }
    }

    /**
     * Writes the XML output file
     * @param null
     * @return null
     */
    public function writeXML() {
        $this->openUri($this->outputFile);
        $this->startDocument('1.0', 'utf-8');
        $this->startElement('products');
        $this->setIndent(true);
        $this->setIndentString(' ');

        // Get the headers
        $tags = $this->inputFile->fgetcsv();

        // Run through rest of csv file
        while (($row = $this->inputFile->fgetcsv()) != NULL) {

            // Each csv row is an xml 'product'
            $this->startElement('product');

            // Run through row columns
            foreach ($tags as $key => $tag) {

                $this->startElement($tag);

                // If csv cell is empty, write empty string
                $this->text((isset($row[$key]) ? $row[$key] : ""));

                $this->endElement();
            }
            $this->endElement();
        }
        $this->endElement();
        $this->endDocument();
        $this->flush();

        echo "New XML file created: <br /><strong>$this->outputFile</strong>";
        echo "<br /><a href='/'>Done</a>";
    }
}

?>

