<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);

if (isset($_POST["status"])) {
    $s = new statuser();
    $s->statusChanger($_POST["status"]);
} else {
    header("location: http://localhost/chat proto/");
}

class statuser {
    private $status;
    private $conn;

    final public function statusChanger($status) {
        $this->status = $status;

        require("includes/databases/chat.php");
        $chat = new db_connect();
        $conn = $chat->connect();
        $this->conn = $conn;

        $prepared = $this->conn->prepare("UPDATE user_statuses SET Status = ?, StatusChangeTime = ? WHERE User = ?");
        if (!$prepared) {
            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
        }
        $prepared->bind_param("sss", $this->status, $currentTime, $uuid);
        $uuid = hex2bin($_COOKIE["uuid"]);
        $currentTime = date("Y-m-d H:i:s");
        $prepared->execute();
        if ($this->status == "Offline") {
            setcookie("uuid", "", time()-3600, "/");
            die("Deleted");
        }
    }
}
?>