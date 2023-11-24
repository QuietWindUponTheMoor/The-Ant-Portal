<?php

class SystemChecks {
    public $db;
    public int $userID;
    public bool $isLoggedIn;
    public bool $isAdmin;
    public bool $isModerator;
    public string $username;
    public string $userPFP;
    private string $profileImageColumnName;
    public string $dbName;
    public $badging;

    /* RANKS:
    2 = admin
    1 = moderator
    0 = user */

    public function __construct($database, $profileImageColumnName, $dbName, $dbhost="localhost", $dbuser="root", $dbpass="") {
        $this->db = new Database($dbhost, $dbuser, $dbpass, $database);
        $this->profileImageColumnName = $profileImageColumnName;
        $this->dbName = $dbName;

        if (isset($_SESSION["userID"])) {
            $this->userID = $_SESSION["userID"];
            $this->isLoggedIn = $this->isLoggedIn();
            $this->isAdmin = $this->isAdmin();
            $this->isModerator = $this->isModerator();
            $this->username = $this->fetchUsername($this->userID);
            $this->fetchProfileImage(); // Assign $userPFP property
            // Do badging
            $ownedBadgesTable = "owned_badges";
            $dbRes = $this->db->selectAll("SHOW TABLES LIKE '$ownedBadgesTable';");
            if ($dbRes->num_rows > 0) {
                $this->badging = new Badging($this->db, $this->userID);
            }
        } else {
            $this->isLoggedIn = false;
            $this->isAdmin = false;
            $this->isModerator = false;
        }
    }

    // Private Methods
    private function isAdmin() {
        if ($this->isLoggedIn === true) {
            $rank = $this->fetchUserRank($this->userID);
            if ($rank == 2) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function isModerator() {
        if ($this->isLoggedIn === true) {
            $rank = $this->fetchUserRank($this->userID);
            if ($rank == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function isLoggedIn() {
        if (isset($_SESSION["userID"]) && $_SESSION["userID"] !== 0) {
            return true;
        } else {
            return false;
        }
    }

    private function fetchProfileImage() {
        $col = $this->profileImageColumnName;
        $res = $this->db->select("SELECT $col FROM users WHERE userID=?;", "i", $this->userID);
        if ($res->num_rows > 0) {
            $row = mysqli_fetch_assoc($res);
            $pfp = $row[$col];
            $this->userPFP = $pfp;
        } else {
            $this->userPFP = "Profile image not found or there was an error.";
        }
    }

    // Public Methods
    public function fetchUserRank($userID) {
        $result = $this->db->select("SELECT rank FROM users WHERE userID=?;", "i", $userID);
        if ($result->num_rows > 0) {
            return mysqli_fetch_assoc($result)["rank"];
        } else {
            die("The userID $userID is not valid, or there was an unresolved error. Please contact an administrator.");
        }
    }

    public function fetchUsername($userID) {
        if ($this->isLoggedIn === true) {
            $result = $this->db->select("SELECT username FROM users WHERE userID=?;", "i", $userID);
            if ($result->num_rows > 0) {
                return mysqli_fetch_assoc($result)["username"];
            }
        } else {
            return "Username not found";
        }
    }
}

class Database {
    private $SERVERNAME;
    private $host;
    private $userName;
    private $pass;
    private $database;
    public $conn;

    public function __construct($host, $databaseUsername, $databasePassword, $databaseName) {
        $this->host = $host;
        $this->userName = $databaseUsername;
        $this->pass = $databasePassword;
        $this->database = $databaseName;
        $this->SERVERNAME = $_SERVER['SERVER_NAME'];

        $host = $this->host;
        $userName = $this->userName;
        $pass = $this->pass;
        $database = $this->database;


        $this->conn = mysqli_connect($host, $userName, $pass, $database)
            or die('Could not connect to '.$host.' using database "'.$database.'" Using server: '.$SERVERNAME);

        return $this->conn;
    }

    public function createEvent($QUERY) {
        $sql = $QUERY;
        mysqli_query($this->conn, $sql)
            or die("Could not execute SQL sequence.");
        
        return true;
    }

    public function transferTableData($archiveTable, $sourceTable, $where) {
        // Transfer data
        $sql = "INSERT INTO $archiveTable SELECT * FROM $sourceTable WHERE $where;";
        $stmt = mysqli_stmt_init($this->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");

        // Delete old record
        $sql = "DELETE FROM $sourceTable WHERE $where;";
        $stmt = mysqli_stmt_init($this->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
        
        return true;
    }

    public function addOrModifyColumn($columnName, $dataType, $tableName) {
        $sql = "SELECT COUNT(*) as column_exists
                FROM information_schema.COLUMNS
                WHERE TABLE_NAME = ? AND COLUMN_NAME = ?";
        
        $stmt = mysqli_stmt_init($this->conn);
        mysqli_stmt_prepare($stmt, $sql);
        
        mysqli_stmt_bind_param($stmt, "ss", $tableName, $columnName);
        
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $columnExists);
        mysqli_stmt_fetch($stmt);
        
        mysqli_stmt_close($stmt);
    
        if ($columnExists == 0) {
            // The column does not exist, so add it
            $sql = "ALTER TABLE $tableName ADD $columnName $dataType";
        } else {
            // The column already exists, so modify it
            $sql = "ALTER TABLE $tableName MODIFY COLUMN $columnName $dataType";
        }
    
        $stmt = mysqli_stmt_init($this->conn);
        mysqli_stmt_prepare($stmt, $sql);
    
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function tableCreate($QUERY) {
        $sql = $QUERY;
        $stmt = mysqli_stmt_init($this->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
        
        return true;
    }

    public function modifyTable($QUERY) {
        $sql = $QUERY;
        $stmt = mysqli_stmt_init($this->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
        return true;
    }

    public function dropTable($tableToDrop) {
        $sql = "DROP TABLE IF EXISTS $tableToDrop";
        $stmt = mysqli_stmt_init($this->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
        
        return true;
    }

    public function insert($QUERY, ...$bind_data) {
        $sql = $QUERY;
        $stmt = mysqli_stmt_init($this->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_bind_param($stmt, ...$bind_data)
            or die("Could not bind SQL parameters.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
        
        return true;
    }

    public function select($QUERY, ...$bind_data) {
        $sql = $QUERY;
        $stmt = mysqli_stmt_init($this->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_bind_param($stmt, ...$bind_data)
            or die("Could not bind SQL parameters.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        $result = mysqli_stmt_get_result($stmt)
            or die("Could not retrieve data with query $QUERY.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
        
        return $result;
    }

    public function selectAll($QUERY) {
        $sql = $QUERY;
        $stmt = mysqli_stmt_init($this->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        $result = mysqli_stmt_get_result($stmt)
            or die("Could not retrieve data with query $QUERY.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
        
        return $result;
    }

    public function delete($QUERY) {
        $sql = $QUERY;
        $stmt = mysqli_stmt_init($this->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");

        return true;
    }

    public function __generateWhereValueExistsInString() {
        return "CONCAT(', ', membersArray, ',') LIKE CONCAT('%, ', ?, ',%')";
    }
}

class FileUpload {
    // Properties
    protected $upload_path;
    protected $new_file_name;
    protected $single_file_die_error;
    protected $multi_file_die_error;
    protected $inputNameAttr;

    // Methods
    public function __construct($inputNameAttr = "file", $upload_path = "", $new_file_name = "new_file", $single_file_die_error = "<p class='reg-error'>Something went wrong while uploading your file. Please try again or contact an administrator.</p>", $multi_file_die_error = "<p class='reg-error'>Something went wrong while uploading your files. Please try again or contact an administrator.</p>") {
        $this->inputNameAttr = $inputNameAttr;
        $this->upload_path = $upload_path;
        $this->new_file_name = $new_file_name;
        $this->single_file_die_error = $single_file_die_error;
        $this->multi_file_die_error = $multi_file_die_error;
    }

    public function uploadSingleFile() {
        if (!empty($_FILES["$this->inputNameAttr"]["tmp_name"])) {
            $fileName = $_FILES["$this->inputNameAttr"]["name"]; // Name of selected file
            $tempName = $_FILES["$this->inputNameAttr"]["tmp_name"]; // Server/temp name of file during processing
            $ext = pathinfo($fileName, PATHINFO_EXTENSION); //. Get file extension
            $randNumber = rand(9999, 9999999999);
            $new_file_name = $this->new_file_name."_".$randNumber; //File name without extension, add randnum string to end of name
            $target = $_SERVER["DOCUMENT_ROOT"].$this->upload_path."/".$new_file_name.".$ext"; // Final and total path of file
            $displayPath = $this->upload_path."/".$new_file_name.".$ext"; // Path to return upon successful file upload
            
            if (!file_exists($target)) {
                if (move_uploaded_file($tempName, $target)) {
                    return $displayPath;
                } else {
                    die($this->single_file_die_error);
                }
            }
        } else {
            echo null;
        }
    }
}

class Register {
    protected $username;
    protected $email;
    protected $password;
    protected $passwordrepeat;
    protected $db;
    protected $date;
    protected $time;
    
    // Generated
    protected $pfpPath;
    protected $hashedPass;

    public function __construct($username, $email, $pass, $passRpt, $date, $time) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $pass;
        $this->passwordrepeat = $passRpt;
        $this->date = $date;
        $this->time = $time;
        $this->db = new Database("localhost", "root", "", "main");
        $this->__register();
    }

    // Main method
    private function __register() {
        $this->uploadProfileImage();
        if ($this->verifyUsername()) {
            $result = true;
        } else {
            $result = false;
            die("<p class='reg-error'>Sorry, either your username is not valid or something went wrong. Your username should be purely alphanumeric characters [a-Z] & [0-9].</p>");
        }

        if ($this->verifyEmail()) {
            $result = true;
        } else {
            $result = false;
            die("<p class='reg-error'>Sorry, but your email is either not valid or something went wrong. Please try a different email address or contact an administrator.</p>");
        }

        if ($this->matchPasswords()) {
            $result = true;
        } else {
            $result = false;
            die("<p class='reg-error'>Uh oh! Your passwords do not match! Please try a different password or try again!</p>");
        }

        // Hash password
        if ($result == true) {
            $this->hashPassword();
        }
        if ($this->pfpPath === null) {
            $this->pfpPath = "/web_images/defaults/default_pfp.jpg";
        }
        $this->db->insert("INSERT INTO users (username, email, `password`, `image`, joined, time) VALUES (?, ?, ?, ?, ?, ?);", "ssssss", $this->username, $this->email, $this->hashedPass, $this->pfpPath, $this->date, $this->time);
        if ($result == true) {
            echo "<p class='reg-success'>You have successfully registered with us! You are free to sign in.</p>";
        } else {
            die("<p class='reg-error'>Something went wrong creating your account. Please try again, or contact an administrator.</p>");
        }
    }

    // Private methods
    private function verifyUsername() {
        $username_length = strlen($this->username);
        if ($username_length < 8) {
            $result = false;
        } else {
            $result = true;
        }

        // Check if username is alphanumeric
        $result = $this->isAlphaNumeric($this->username);

        $username = $this->username;
        $result = $this->db->select("SELECT username FROM users WHERE username=?;", "s", $username);
        if ($result->num_rows == 0) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
    private function verifyEmail() {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $result = true;
        } else {
            $result = false;
        }

        $email = $this->email;
        $result = $this->db->select("SELECT email FROM users WHERE email=?;", "s", $email);
        if ($result->num_rows == 0) {
            return true;
        } else {
            return false;
        }
    }
    private function matchPasswords() {
        $first = $this->password;
        $second = $this->passwordrepeat;
        if ($first === $second) {
            return true;
        } else {
            return false;
        }
    }
    private function uploadProfileImage() {
        $upload = new FileUpload("image", "/users/uploads/profile_images", $this->username."_user_profile_image");
        $this->pfpPath = $upload->uploadSingleFile();
    }
    private function hashPassword() {
        $this->hashedPass = password_hash($this->password, PASSWORD_DEFAULT);
    }

    // Helper methods
    private function isAlphaNumeric($value) {
        if (preg_match("/^[A-Za-z0-9]+$/", $value)) {
            return true;
        } else {
            return false;
        }
    }
}

class Login {
    protected $db;
    protected $username;
    protected $password;

    // Generated
    protected $userID;

    public function __construct($username, $pass) {
        $this->db = new Database("localhost", "root", "", "main");
        $this->username = $username;
        $this->password = $pass;
        $this->login();
    }

    // Main methods
    private function login() {
        if ($this->usernameIsValid()) {
            $result = true;
        } else {
            $result = false;
            die("<p class='reg-error'>Sorry, either your username is not valid or something went wrong. Your username should be purely alphanumeric characters [a-Z] & [0-9].</p>");
        }

        if ($this->passwordIsCorrect()) {
            $result = true;
        } else {
            $result = false;
            die("<p class='reg-error'>Sorry, your password is not correct! Please try again. If you think this was a mistake, please contact an administrator.</p>");
        }
        
        if ($result == true) {
            $this->fetchUserID();
            header("Location: ?setSession=".$this->userID);
        } else {
            die("<p class='reg-error'>Something went wrong signing you into your account. Please try again, or contact an administrator.</p>");
        }
    }

    // Private methods
    private function passwordIsCorrect() {
        $password = $this->password;
        $username = $this->username;
        $res = $this->db->select("SELECT `password` FROM users WHERE username=?;", "s", $username);
        if ($res->num_rows > 0) {
            $fetchedPass = mysqli_fetch_assoc($res)["password"];
            $result = password_verify($this->password, $fetchedPass);
        } else {
            $result = false;
        }
        return $result;
    }
    private function usernameIsValid() {
        $result = $this->isAlphaNumeric($this->username);

        $username = $this->username;
        $res = $this->db->select("SELECT username FROM users WHERE username=?;", "s", $username);
        if ($res->num_rows > 0) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
    private function fetchUserID() {
        $username = $this->username;
        $res = $this->db->select("SELECT userID FROM users WHERE username=?;", "s", $username);
        if ($res->num_rows > 0) {
            $this->userID = mysqli_fetch_assoc($res)["userID"];
        }
    }

    // Helper methods
    private function isAlphaNumeric($value) {
        if (preg_match("/^[A-Za-z0-9]+$/", $value)) {
            return true;
        } else {
            return false;
        }
    }
}

class TimeFormats {
    private string $defaultTimezone;
    public string $date;
    public string $timezone;
    public string $time;
    public string $formattedTime;

    public function __construct($select="f", $defaultTimezone=null) {
        if ($defaultTimezone === null) {
            $this->defaultTimezone = date_default_timezone_get();
        } else {
            $this->defaultTimezone = date_default_timezone_get($defaultTimezone);
        }
        $this->date = date("M d, Y");
        $timezone = new DateTime('now', new DateTimeZone($this->defaultTimezone));
        $this->timezone = $timezone->format('T');
        $this->time = date("h:ia");
        $this->formattedTime = "$this->time ($this->timezone)";

        echo $select;
        if ($select === "f") {
            $date = $this->date;
            $formattedTime = $this->formattedTime;
            return "$date @ $formattedTime";
        } else if ($select === "t") {
            return $this->time;
        } else if ($select === "t:timezome") {
            return $this->formattedTime;
        } else if ($select === "d") {
            return $this->date;
        } else if ($select === "tz:default") {
            return $this->defaultTimezone;
        } else if ($select === "*") {
            return [
                $this->defaultTimzone,
                $this->date,
                $this->timezone,
                $this->time,
                $this->formattedTime
            ];
        }
    }
}

class CreateNupFlight {
    private object $db;
    private int $userID;
    private string $species;
    private string $body;
    private string $flightDate;
    private string $flightTime;
    private string $temperature;
    private string $windSpeed;
    private string $moonCycle;
    private string $tags;
    private string $datetime;


    public function __construct() {
        // Get data "d" short for data
        $d = array_filter($_POST);
        $this->db = new Database($d["database"], "root", "", "main");
        $this->species = $d["data-species"];
        $this->body = $d["data-body"];
        $this->flightDate = $d["data-date"];
        $this->flightTime = $d["data-time"];
        $this->temperature = $d["data-temperature"]."°F";
        $this->windSpeed = $d["data-wind-speed"];
        $this->moonCycle = $d["data-moon-cycle"];
        $this->tags = $d["data-tags"];
        $this->userID = $d["data-user_id"];

        // Get datetime
        $date = date("M d, Y");
        $timezone = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
        $timezone = $timezone->format('T');
        $time = date("h:ia");
        $time = "$time ($timezone)";
        $this->datetime = "$date @ $time";
    }

    public function execute() {
        if ($this->db->insert("INSERT INTO nuptial_flights (userID, `text`, `date`, `time`, species, `datetime`, temperature, wind_speed, moon_cycle) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);", "issssssss", $this->userID, $this->body, $this->flightDate, $this->flightTime, $this->species, $this->datetime, $this->temperature, $this->windSpeed, $this->moonCycle)) {
            // If post was successfully inserted into database
            // Get file name
            $fileName = preg_replace("/[^A-Za-z0-9 ]/", '', $this->species);

            // Iterate over files, if exist
            // Initialize pathArray
            $pathArray = [];
            //print_r($_FILES);
            error_reporting(E_ALL);
            set_time_limit(0);
            ini_set('upload_max_filesize', '500M');
            ini_set('post_max_size', '500M');
            ini_set('max_input_time', 4000); // Play with the values
            ini_set('max_execution_time', 4000); // Play with the values
            if (!empty(array_filter($_FILES["images"]["tmp_name"]))) {
                // Count total files:
                $total = count($_FILES["images"]["name"]);
                for ($i = 0; $i < $total; $i++) {
                    // Get current file
                    $tmpFilePath = $_FILES["images"]["tmp_name"][$i];
                    // Get file extension
                    $ext = pathinfo($_FILES["images"]["name"][$i], PATHINFO_EXTENSION);
                    // Get path
                    $displayPath = "/users/uploads/posts/"."$i-".$fileName.".$ext";
                    // Get full path (target)
                    $target = $_SERVER["DOCUMENT_ROOT"].$displayPath;
                    // Upload file
                    if (!file_exists($target)) {
                        if (move_uploaded_file($tmpFilePath, $target)) {
                            array_push($pathArray, $displayPath);
                        } else {
                            die("SOMETHING WENT WRONG UPLOADING ONE OR MORE OF YOUR IMAGES!");
                        }
                    } else {
                        die("AN IMAGE ALREADY EXISTS BY THE NAME OF '$target'!!!");
                    }
                }
                // Upload paths to database (with UPDATE statement)
                $images = implode(", ", $pathArray);
                if ($this->db->insert("UPDATE nuptial_flights SET imageArray=? WHERE species=? AND userID=?;", "ssi", $images, $this->species, $this->userID)) {
                    // If image path(s) uploaded to database correctly and everything went smoothly up until this point
                    // Upload tags
                    if ($this->tagsUpload()) {
                        // Redirect user if all is successful
                        $this->redirectUser();
                    }
                }
            } else {
                // Upload tags
                if ($this->tagsUpload()) {
                    // Redirect user if all is successful
                    $this->redirectUser();
                }
            }
        } else {
            echo -1;
        }
    }

    private function tagsUpload() {
        $c = 1;
        foreach (explode(", ", $this->tags) as $tag) {
            if ($c === 1) {
                $col = "tagOne";
            } else if ($c === 2) {
                $col = "tagTwo";
            } else if ($c === 3) {
                $col = "tagThree";
            } else if ($c === 4) {
                $col = "tagFour";
            } else if ($c === 5) {
                $col = "tagFive";
            }
            $this->db->insert("UPDATE nuptial_flights SET $col=? WHERE species=? AND userID=?;", "ssi", $tag, $this->species, $this->userID);
            $c++;
        }
        return true;
    }

    private function redirectUser() {
        $res = $this->db->select("SELECT flightID FROM nuptial_flights WHERE userID=? ORDER BY flightID DESC LIMIT 1;", "i", $this->userID);
        if ($res->num_rows > 0) {
            $postID = mysqli_fetch_assoc($res)["flightID"];
            echo $postID;
        } else {
            echo -1;
        }
    }
}

class CreatePost {
    private object $db;
    private string $title;
    private string $body;
    private string $tags;
    private int $userID;
    private int $postType;
    private string $datetime;

    public function __construct() {
        // Get data "d" short for data
        $d = array_filter($_POST);
        $this->db = new Database($d["database"], "root", "", "main");
        $this->title = $d["title"];
        $this->tags = $d["final-tags"];
        $this->body = $d["data-body"];
        $this->userID = $d["user_id"];
        $this->postType = $d["postType"];

        // Get datetime
        $date = date("M d, Y");
        $timezone = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
        $timezone = $timezone->format('T');
        $time = date("h:ia");
        $time = "$time ($timezone)";
        $this->datetime = "$date @ $time";
    }

    public function execute() {
        if ($this->db->insert("INSERT INTO posts (userID, `type`, title, `text`, `datetime`) VALUES (?, ?, ?, ?, ?);", "iisss", $this->userID, $this->postType, $this->title, $this->body, $this->datetime)) {
            // If post was successfully inserted into database
            // Get file name
            $fileName = preg_replace("/[^A-Za-z0-9 ]/", '', $this->title);

            // Iterate over files, if exist
            // Initialize pathArray
            $pathArray = [];
            //print_r($_FILES);
            error_reporting(E_ALL);
            set_time_limit(0);
            ini_set('upload_max_filesize', '500M');
            ini_set('post_max_size', '500M');
            ini_set('max_input_time', 4000); // Play with the values
            ini_set('max_execution_time', 4000); // Play with the values
            if (!empty(array_filter($_FILES["images"]["tmp_name"]))) {
                // Count total files:
                $total = count($_FILES["images"]["name"]);
                for ($i = 0; $i < $total; $i++) {
                    // Get current file
                    $tmpFilePath = $_FILES["images"]["tmp_name"][$i];
                    // Get file extension
                    $ext = pathinfo($_FILES["images"]["name"][$i], PATHINFO_EXTENSION);
                    // Get path
                    $displayPath = "/users/uploads/posts/"."$i-".$fileName.".$ext";
                    // Get full path (target)
                    $target = $_SERVER["DOCUMENT_ROOT"].$displayPath;
                    // Upload file
                    if (!file_exists($target)) {
                        if (move_uploaded_file($tmpFilePath, $target)) {
                            array_push($pathArray, $displayPath);
                        } else {
                            die("SOMETHING WENT WRONG UPLOADING ONE OR MORE OF YOUR IMAGES!");
                        }
                    } else {
                        die("AN IMAGE ALREADY EXISTS BY THE NAME OF '$target'!!!");
                    }
                }
                // Upload paths to database (with UPDATE statement)
                $images = implode(", ", $pathArray);
                if ($this->db->insert("UPDATE posts SET imageArray=? WHERE title=?;", "ss", $images, $this->title)) {
                    // If image path(s) uploaded to database correctly and everything went smoothly up until this point
                    // Upload tags
                    if ($this->tagsUpload()) {
                        // Redirect user if all is successful
                        $this->redirectUser();
                    }
                }
            }
        }
    }

    private function tagsUpload() {
        $c = 1;
        foreach (explode(", ", $this->tags) as $tag) {
            if ($c === 1) {
                $col = "tagOne";
            } else if ($c === 2) {
                $col = "tagTwo";
            } else if ($c === 3) {
                $col = "tagThree";
            } else if ($c === 4) {
                $col = "tagFour";
            } else if ($c === 5) {
                $col = "tagFive";
            }
            $this->db->insert("UPDATE posts SET $col=? WHERE title=?;", "ss", $tag, $this->title);
            $c++;
        }
        return true;
    }

    private function redirectUser() {
        $res = $this->db->select("SELECT postID FROM posts WHERE title=? ORDER BY postID DESC LIMIT 1;", "s", $this->title);
        if ($res->num_rows > 0) {
            $postID = mysqli_fetch_assoc($res)["postID"];
            echo $postID;
        } else {
            echo -1;
        }
    }
}

class Feed {
    // Static
    private object $db;
    private int $item_limit = 10;

    // Static until set
    private int $page_count;
    private array $postsArray;
    private int $current_page;
    private int $item_count;

    public function __construct(object $db) {
        $this->db = $db;

        // Initialize vars
        $rows = [];
        $page_count = 1;
        $first = 1;
        $last = 2;
        $item_count = 0;
        $posts_item_count = 0;
        $nup_flights_item_count = 0;

        // Static vars
        $item_limit_before_new_page = 5;

        // Var control for queries:
        // Static variables:
        $post_type_string = "";

        // Static unless otherwise specified:
        $post_order = "ASC";
        $post_type = "1";

        // Check GET data
        // Order
        if (isset($_GET["order"]) && $_GET["order"] === "ASC") {
            $post_order = "ASC";
        } else if (isset($_GET["order"]) && $_GET["order"] === "DESC") {
            $post_order = "DESC";
        }
        // Type
        if (isset($_GET["type"]) && is_numeric($_GET["type"])) {
            $post_type = $_GET["type"];
            $post_type_string = "WHERE `type`=$post_type_string";
            /* TYPES:
            1 = question
            2 = sighting
            3 = general
            4 = nup_flight
            */
        }

        // Check GET data for the current page
        $this->current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;


        // Regular posts
        $postsRes = $this->db->selectAll("SELECT * FROM posts $post_type_string ORDER BY postID $post_order;");
        if ($postsRes->num_rows > 0) {
            // There is at least one page, probably more
            // Set count of items
            $item_count = $postsRes->num_rows;
            while ($row = mysqli_fetch_assoc($postsRes)) {
                // Fetch data from DB
                $postID = $row["postID"];
                $userID = $row["userID"];
                $type = $row["type"];
                $title = $row["title"];
                $text = $row["text"];
                $views = $row["views"];
                $upvotes = $row["upvotes"];
                $downvotes = $row["downvotes"];
                $datetime = $row["datetime"];
                $answers = $row["answers"];
                $replies = $row["replies"];
                $editedByUserID = $row["editedByUserID"];

                // Push to $rows array
                //$post = $this->generatePost($type, $userID, "/posts/?postID=$postID", $title, $text, $upvotes, $downvotes, $views, $answers, $replies, $editedByUserID, $datetime);
                array_push($rows, ["type" => $type, "userID" => $userID, "postHREF" => "/posts/?postID=$postID", "title" => $title, "text" => $text, "upvotes" => $upvotes, "downvotes" => $downvotes, "views" => $views, "answers" => $answers, "replies" => $replies, "editedByUserID" => $editedByUserID, "datetime" => $datetime]);
            }
        } else {
            // There is no data to display, so there's only one page and it's blank
            // Set count of items
            $posts_item_count = $postsRes->num_rows; // Should be 0
            // Do nothing else here
        }

        // nup_flight posts
        $postsRes = $this->db->selectAll("SELECT * FROM nuptial_flights ORDER BY flightID $post_order;");
        if ($postsRes->num_rows > 0) {
            // There is at least one page, probably more
            // Set count of items
            $item_count = $postsRes->num_rows;
            while ($row = mysqli_fetch_assoc($postsRes)) {
                // Fetch data from DB
                $postID = $row["flightID"];
                $userID = $row["userID"];
                $type = 4;
                $species = $row["species"];
                $text = $row["text"];
                $views = $row["views"];
                $upvotes = $row["upvotes"];
                $downvotes = $row["downvotes"];
                $datetime = $row["datetime"];
                $answers = $row["answers"];
                $replies = $row["replies"];
                $editedByUserID = $row["editedByUserID"];

                // Push to $rows array
                //$post = $this->generatePost($type, $userID, "/nup_flight/?flightID=$postID", "Nuptial Flight #$postID: $species", $text, $upvotes, $downvotes, $views, $answers, $replies, $editedByUserID, $datetime);
                array_push($rows, ["type" => $type, "userID" => $userID, "postHREF" => "/nup_flight/?flightID=$postID", "title" => "Nuptial Flight #$postID: $species", "text" => $text, "upvotes" => $upvotes, "downvotes" => $downvotes, "views" => $views, "answers" => $answers, "replies" => $replies, "editedByUserID" => $editedByUserID, "datetime" => $datetime]);
            }
        } else {
            // There is no data to display, so there's only one page and it's blank
            // Set count of items
            $nup_flights_item_count = $postsRes->num_rows; // Should be 0
            // Do nothing else here
        }

        // Set properties
        $this->page_count = $page_count;
        $this->postsArray = $this->sortArray(1, $rows);
        $this->item_count = count($this->postsArray);
    }

    public function feed() {
        // Initialize the final array
        $new_posts_array = [];

        // Sort the original array
        $posts = $this->sortArray(1, $this->postsArray);

        // Process the posts into a new array
        foreach ($posts as $post) {
            // Push the sorted array into the new array
            $new_item = $this->generatePost($post["type"], $post["userID"], $post["postHREF"], $post["title"], $post["text"], $post["upvotes"], $post["downvotes"], $post["views"], $post["answers"], $post["replies"], $post["editedByUserID"], $post["datetime"]);
            array_push($new_posts_array, $new_item);
        }

        // Item limit
        $per_page_limit = $this->item_limit;
        // Page number
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        // Offset
        $offset = ($current_page - 1) * $per_page_limit;

        $new_posts = array_slice($new_posts_array, $offset, $per_page_limit, false);
        
        // Display the items per page
        foreach ($new_posts as $post) {
            echo $post;
        }
    }
    public function displayPages(): void {
        // Easier vars
        $current_page = $this->current_page;

        // Get the current page count
        $page_count = ceil($this->item_count / $this->item_limit);

        // Get page limit before delimiter
        $page_limit_before_delimiter = 6;

        // Process
        echo '<a class="pag-button" href="?page=1">First</a>';
        for ($i = max(1, $current_page - $page_limit_before_delimiter); $i <= min($page_count, $current_page + $page_limit_before_delimiter); $i++) {
            echo '<a class="pag-button' . ($i == $current_page ? ' current' : '') . '" href="?page=' . $i . '">' . $i . '</a>';
        }
        if ($current_page + $page_limit_before_delimiter < $page_count) {
            echo '<p class="pag-button" id="delimiter">...</p>';
        }
        echo '<a class="pag-button" href="?page='.$page_count.'">Last</a>';
    }
    public function generatePost(int $type, int $postedByUserID, string $postHREF, string $title, string $body, int $upvotes, int $downvotes, int $views, int $answers, int $replies, int $editedByUserID, string $datetime): string {
        $rating = $upvotes - $downvotes;
        $userData = $this->getUserData($postedByUserID);
        $username = $userData[0];
        $userID = $userData[1];
        $userImage = $userData[2];
        if ($editedByUserID > 0) {
            $hasBeenEdited = "true";
        } else {
            $hasBeenEdited = "false";
        }
        if ($type === 1) {
            $postType = "Question";
        } else if ($type === 2) {
            $postType = "Sighting";
        } else if ($type === 3) {
            $postType = "General";
        } else if ($type === 4) {
            $postType = "Nuptial Flight";
        }
        return
        '
        <div class="feed-post">
            <div class="feed-post-section">
                <a class="feed-post-title" href="'.$postHREF.'">'.$title.'</a>
            </div>
            <div class="feed-post-section">
                <p class="feed-post-body twoline-ellipsis">'.$body.'</p>
            </div>
            <div class="feed-post-section">
                <p class="meta type">'.$postType.'</p>
            </div>
            <div class="feed-post-section feed-post-section-wrap feed-post-meta">
                <p class="meta">Rating: '.$rating.'</p>
                <p class="meta">Views: '.$views.'</p>
                <p class="meta">Answers: '.$answers.'</p>
                <p class="meta">Replies: '.$replies.'</p>
                <p class="meta">Edited: '.$hasBeenEdited.'</p>
            </div>
            <div class="feed-post-section feed-post-posted-by">
                <div class="posted-by">
                    <p class="text">Posted by</p>
                    <div class="user-image-container"><img class="user-image" src="'.$userImage.'"/></div>
                    <a class="text username" href="/users/user?userID='.$userID.'">'.$username.'</a>
                    <p class="text">on '.$datetime.'</p>
                </div>
            </div>
        </div>
        ';
    }
    public function getUserData(int $userID): array {
        $res = $this->db->select("SELECT * FROM users WHERE userID=?;", "i", $userID);
        if ($res->num_rows > 0) {
            $d = mysqli_fetch_assoc($res);
            $username = $d["username"];
            $image = $d["image"];
            return [$username, $userID, $image];
        }
    }
    public function sortArray(int $maxConsecutive, array $array): array {
        // Initialize grouper and columns arrays
        $grouper = [];
        $column = [];

        // Process items in the array
        foreach ($array as $row) {
            $column[] = $row["type"];
            $encountered[$row["type"]] ??= 0;
            $grouper[] = intdiv($encountered[$row["type"]]++, $maxConsecutive);
        }

        // Finally, sort the array
        array_multisort($grouper, $column, $array);

        return $array;
    }
}

class Badging {
    // Static
    private object $db;
    public array $badges;

    // Dynamic
    private int $userID;
    private int $seedsCount;
    private array $userBadges;

    public function __construct(object $db, int $userID=null) {
        $this->db = $db;
        $this->userID = $userID;
        $this->date = date("m/d/Y");
        require($_SERVER["DOCUMENT_ROOT"]."/php/settings/badges.php");
        $this->badges = $badges;
        // Get a count of user's seeds
        $seedsRes = $db->select("SELECT seeds FROM users WHERE userID=?;", "i", $userID);
        if ($seedsRes->num_rows > 0) {
            $this->seedsCount = mysqli_fetch_assoc($seedsRes)["seeds"];
        } else {
            $this->seedsCount = null;
        }
        // Get an array of user badges:
        $userBadges = []; // Initialize array
        $hasBadgeRes = $this->db->select("SELECT * FROM owned_badges WHERE userID=?;", "i", $userID);
        if ($hasBadgeRes->num_rows > 0) {
            while ($badges = mysqli_fetch_assoc($hasBadgeRes)) {
                array_push($userBadges, $badges["badge"]);
            }
        }
        // Set userBadges property
        $this->userBadges = $userBadges;

        // Do badges checks
        $this->automaticBadgesChecks();
    }

    private function automaticBadgesChecks(): bool {
        $userID = $this->userID;
        $badges = $this->badges;
        $seeds = $this->seedsCount;
        $userBadges = $this->userBadges;

        // Badge control
        return $this->badgeControl($badges, $userID, $seeds, $userBadges);
    }

    public function manualBadgesChecks(int $userID): bool {
        $badges = $this->badges;
        $seeds = $this->seedsCount;
        $userBadges = $this->userBadges;

        // Badge control
        return $this->badgeControl($badges, $userID, $seeds, $userBadges);
    }

    public function userHasSpecificBadge(string|int $badgeType, int $userID): bool {
        $res = $this->db->select("SELECT * count FROM owned_badges WHERE badge=? userID=?;", "ii", $badgeType, $userID);
        if ($res->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function badgeControl(array $badges, int $userID, int $seeds, array $userBadges): bool {
        foreach ($badges as $badge) {
            // If the user has enough seeds for this badge
            $requiredSeeds = $badge["cost"];
            if ($requiredSeeds <= $seeds) {
                // Check if user has this badge already or not
                $hasThisBadge = in_array($badge["id"], $userBadges);

                // Give the user this badge if they don't have it already
                if ($hasThisBadge === false) {
                    $this->db->insert("INSERT INTO owned_badges (userID, badge, earned_on_date) VALUES (?, ?, ?);", "iis", $userID, $badge["id"], $this->date);
                }
            }
        }

        return true;
    }
}