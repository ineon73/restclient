<?php
    namespace App\Processors\Support;
      
    class DictionaryDB {
        static $host      = 'localhost';
        
        static $db;   //      = config('database.connections.mysql.database'); //'processor';
        static $user; //      = config('database.connections.mysql.username');
        static $pass;  //     = config('database.connections.mysql.password');
        static $charset; //   = config('database.connections.mysql.charset');

        static $opt;
        
        public function __construct($arParams = null) {
             self::$host = config('database.connections.mysql.host'); 
             self::$db         = config('database.connections.mysql.database'); //'processor';
             self::$user       = config('database.connections.mysql.username');
             self::$pass       = config('database.connections.mysql.password');
             self::$charset    = config('database.connections.mysql.charset');
             
             echo '!!!!'.config('database.connections.mysql.charset');
             
              $opt = [
                 \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                 \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                 \PDO::ATTR_EMULATE_PREPARES => false,
             ];
              echo self::$charset;
            ;}
            
            
        public static function connect() {
            echo "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;
            return new \PDO("mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset, self::$user, self::$pass, self::$opt);
        }
    }