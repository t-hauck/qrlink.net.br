<?php
$servername = 'localhost';
$username = 'root';
$password = "l=,m%hL*YUStEP+,0uVR";
$dbname = 'qrlink';
$base_url = $_SERVER["HTTP_HOST"]; 



if(isset($_POST['url']) && $_POST['url'] != "") {
    $url=urldecode($_POST['url']);
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        $slug=GetShortUrl($url);
        $conn->close(); // echo $base_url.$slug;


        // $result = ["link_curto" => $base_url . $slug ];
        // $result = [ $base_url . $slug ];
        header("Content-type: application/json");
        echo json_encode( $base_url . $slug );

        // echo json_encode($result);
    } 
    else  {
        die("$url is not a valid URL");
    }
} else {
    require_once '_HTML.php';
    exit();
}

if(isset($_GET['redirect']) && $_GET['redirect']!="") {
    $slug=urldecode($_GET['redirect']);
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $url= GetRedirectUrl($slug);
    $conn->close();
    header("location:".$url);
    exit;
}

function GetRedirectUrl($slug){
    global $conn;
    $query = "SELECT * FROM url_shorten WHERE short_code = '".addslashes($slug)."' "; 
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hits=$row['hits']+1;
        $sql = "update url_shorten set hits='".$hits."' where id='".$row['id']."' ";
        $conn->query($sql);
        return $row['url'];
    } else { 
        die("Invalid Link");
    }
}



function GetShortUrl($url) {
    global $conn;
    $query = "SELECT * FROM url_shorten WHERE url = '" . $url . "' "; 
    $result = $conn->query($query);
   
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['short_code'];
    } else {
        $short_code = generateUniqueID();
        $sql = "INSERT INTO url_shorten (url, short_code, hits)
        VALUES ('".$url."', '".$short_code."', '0')";
        if ($conn->query($sql) === TRUE) {
            return $short_code;
        } else { 
            die("Unknown Error Occured");
        }
   }
}


function generateUniqueID(){
    global $conn; 
    $token = substr(md5(uniqid(rand(), true)),0,6); $query = "SELECT * FROM url_shorten WHERE short_code = '" . $token . "' ";
    $result = $conn->query($query); 

    if ($result->num_rows > 0) {
        generateUniqueID();
    } else {
        return $token;
    }
}


