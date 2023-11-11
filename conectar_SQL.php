<?php

class Conectar {
    public static function sql (){
        $env = (parse_ini_file('.env')) ? parse_ini_file('.env') : getenv();
        $connectionType = $env["databaseType"];
        $database = $env["database"];
        
        try {                  
            if ($connectionType === "sqlite"){
                $databaseURL = $database;

                return new PDO("$connectionType:$databaseURL", null, null,
                array(
                    PDO::ATTR_TIMEOUT => 10, // em segundos, timeout máximo para conexão com o banco
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // } catch (PDOException $e) {
                    )
                );
       
            }else { // MySQL ..
                $server = $env["server"];
                $user = $env["user"];
                $pass = $env["pass"];

                $databaseURL = "host=$server;dbname=$database";

                return new PDO("$connectionType:$databaseURL", $user, $pass,
                array(
                    PDO::ATTR_TIMEOUT => 10, // em segundos, timeout máximo para conexão com o banco
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // } catch (PDOException $e) {
                    )
                );
            }
        } catch (PDOException $e) { // } catch (Exception $e) {
            $SQL_error = $e->getMessage();
            echo "<b>$SQL_error</b>";
            exit;
        }

    } // function sql
} // class
