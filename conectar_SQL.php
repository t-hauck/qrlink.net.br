<?php

class Conectar {
    public static function sql (){ // centralizando as configurações do banco de dados no arquivo ".env"
        $env = (parse_ini_file('.env')) ? parse_ini_file('.env') : getenv();
        $connectionType = $env["databaseType"];
        $server = $env["server"];
        $database = $env["database"];
        $user = $env["user"];
        $pass = $env["pass"];
        
        try {                  
            if ($connectionType === "mysql"){
                    $databaseURL = "host=$server;dbname=$database";
            } else  $databaseURL = $database;
            
            return new PDO("$connectionType:$databaseURL", $user, $pass,
            array(
                PDO::ATTR_TIMEOUT => 10, // em segundos, timeout máximo para conexão com o banco
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // } catch (PDOException $e) {
                )
            );
        } catch (PDOException $e) { // } catch (Exception $e) {
            $SQL_error = $e->getMessage();
            echo "<b>$SQL_error</b> <br>";
        }

    } // function
} // class
