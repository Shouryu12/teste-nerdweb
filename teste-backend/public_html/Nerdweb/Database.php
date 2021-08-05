<?php
/**
 * Nerdpress - CRM Nerdweb
 * PHP Version 7.2
 *
 * @package    Nerdweb
 * @author     Rafael Rotelok rotelok@nerdweb.com.br
 * @author     Junior Neves junior@nerdweb.com.br
 * @author     Adriano Buba adriano.buba@nerdweb.com.br
 * @author     Hiago Klapowsko hiago.kalpowsko@nerdweb.com.br
 * @copyright  2012-2020 Extreme Hosting Servicos de Internet LTDA
 * @license    https://nerdpress.com.br/license.txt
 * @version    Release: 2.5.0
 * @revision   2020-02-05
*/
namespace Nerdweb {
    use PDO;
    use PDOException;
    use PDOStatement;

    /**
     * Class DataBase
     */
    class Database {
        /** @var PDO */
        private $pdo;
        /**  @var array */
        private $args = [];
        /** @var int */
        private $lastInsertId = 0;
        /** @var int */
        private $rowCount = 0;

        /**
         * Load database configuration from the file config.php
         * or from the parameters
         *
         * Database constructor.
         *
         * @param array $args
         */
        public function __construct(array $args = []) {
            $this->args = $args;
            $this->connect();
        }

        /**
         * Establish the connection to the database
         */
        protected function connect() {
            // Locking the charset to utf8mb4, damm you fucking emojis
            $charset = 'utf8mb4';
            // Creating the connection String
            $dsn = "mysql:host=" . $this->args["host"] . ";dbname=" . $this->args["database"] . ";charset=$charset";
            $opt = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => FALSE,
            ];
            try {
                $this->pdo = new PDO($dsn, $this->args["user"], $this->args["password"], $opt);
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                exit;
            }
        }


        /**
         * Query the Database with the $sql passed, the call should be properly parametricized
         * the parameters are an array of $condValues, you can discard the results with $fetchResult = False
         *
         * @param string $sql
         * @param array  $condValues
         * @param bool   $fetchResult
         *
         * @return array
         */
        protected function preparedQuery($sql, array $condValues = [], $fetchResult = TRUE) {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($sql);
            return $this->executeQuery($sql, $condValues, $fetchResult, $stmt);
        }


        /**
         *  Common code used in the selectPrepared and updatePrepared functions
         *
         * @param array $condFields
         * @param array $condValues
         *
         * @return array
         */
        private function prepareFields(array $condFields, &$condValues) {
            $i = 0;
            $condicoes = [];
            foreach ($condFields as $aux) {
                if ($condValues[$i] === NULL) {
                    $condicoes[] = $aux . " is ?";
                }
                elseif ($condValues[$i] === "NOT NULL") {
                    $condicoes[] = $aux . " is not ?";
                    $condValues[$i] = NULL;
                }
                else {
                    $condicoes[] = $aux . "=?";
                }
                $i++;
            }
            return $condicoes;
        }


        /**
         * @return mixed
         */
        public function returnRowCount() {
            return $this->rowCount;
        }


        /**
         * @param string             $sql
         * @param array        $condValues
         * @param bool             $fetchResult
         * @param PDOStatement $stmt
         *
         * @return array
         */
        protected function executeQuery($sql, array $condValues, $fetchResult, $stmt) {
            $stmt->execute($condValues);
            $this->rowCount = $stmt->rowCount();
            if (stripos($sql, "INSERT") !== FALSE ||
                stripos($sql, "UPDATE") !== FALSE ||
                stripos($sql, "DELETE") !== FALSE
            ) {
                $fetchResult = FALSE;
                $this->lastInsertId = $this->pdo->lastInsertId();
            }
            $return = [];
            if ($fetchResult) {
                $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $this->pdo->commit();
            return $return;
        }


        /**
         * @param string $sql
         * @param array  $condValues
         * @param bool   $fetchResult
         *
         * @return array
         */
        public function customQueryPDO($sql, array $condValues = [], $fetchResult = TRUE) {
            return $this->preparedQuery($sql, $condValues, $fetchResult);
        }


        /**
         * Insert Data into the Database
         *
         * @param string $tblname
         * @param array  $dataFields
         * @param array  $dataValues
         *
         * @return bool
         */
        public function insertPrepared($tblname, array $dataFields, array $dataValues) {
            $valuesMAsk = implode(',', array_fill(0, count($dataFields), '?'));
            $sql = "INSERT INTO $tblname (" . implode(",", $dataFields) . ") VALUES (" . $valuesMAsk . ")";
            $this->preparedQuery($sql, $dataValues);
            return TRUE;
        }


        /**
         * @param string     $tblname
         * @param array      $condNames
         * @param array      $condValues
         * @param string     $fields
         * @param string     $orderByField
         * @param string|int $limitResults
         *
         * @return array
         */
        public function selectPrepared($tblname, array $condNames = [], array $condValues = [], $fields = "", $orderByField = "", $limitResults = "") {
            if ($fields === "") {
                $fields = "*";
            }
            $condicoes = $this->prepareFields($condNames, $condValues);
            $sufixQuery = " AND isUsed=1";
            if ($condNames === []) {
                $sufixQuery = "isUsed=1";
            }
            $conditions = implode(" AND ", $condicoes);
            $sql = "SELECT $fields FROM $tblname WHERE " . $conditions . $sufixQuery;

            if ($orderByField !== "") {
                $sql .= " ORDER BY " . $orderByField;
            }
            if ($limitResults !== "") {
                $sql .= " LIMIT " . $limitResults;
            }
            // Return the results of the query
            $return = $this->preparedQuery($sql, $condValues);
            if ($limitResults === 1 && isset($return[0])) {
                $return = $return[0];
            }

            return $return;
        }


        /**
         * @param string $tblname
         * @param array  $datafields
         * @param array  $updateValues
         * @param array  $condFields
         * @param array  $condValues
         *
         * @return array
         */
        public function updatePrepared($tblname, array $datafields, array $updateValues, array $condFields, array $condValues) {
            #TODO: Implementar funcao de update utilizando um esquema similar a funcao selectPrepared ou insertPrepared

            $condicoes = $this->prepareFields($condFields, $condValues);
            $conditions = implode(" AND ", $condicoes);

            $i = 0;
            $valores = [];
            foreach ($datafields as $data) {
                $valores[] = $data . "=:" . $updateValues[$i];
                $i++;
            }
            $values = implode(", ", $valores);

            $sql = "UPDATE $tblname SET $values WHERE " . $conditions;
            $return = $this->preparedQuery($sql, $condValues);

            return $return;
        }

        public function deletePrepared($tblname, array $condFields, array $condValues) {
            $valuesMAsk = implode(',', array_fill(0, count($condFields), '?'));
            $sql = "DELETE FROM $tblname WHERE (" . $valuesMAsk . ")";
            $this->preparedQuery($sql, $condValues);
            return TRUE;
        }

    }

    # 2) Criar as funções de CRUD para Notícias no namespace Nerdweb: os campos necessários pra uma notícia são [id, data, url_noticia, titulo, conteudo]; - 

    class NoticiasCRUD {
        /** @var int */
        private $id;
        /** @var Database */
        private $db;
        /** @var string */
        private $tablename;
        /** @var array */
        private $dataFields;

        public function __construct(){
            $this->db = new Database();
            $this->tablename = "noticias";
            $this->dataFields = ["id", "data", "url_noticia", "titulo", "conteudo"];
        }

        #id, data, url_noticia, titulo, conteudo

        public function adicionaNoticia($url_noticia, $titulo, $conteudo){
            $novaData = date("Y-m-d h:i:s");
            $novoId = md5(uniqid(strlen($titulo) . $novaData->format('Y-m-d h:i:s'), true));
            $dataValues = [$novoId, $novaData, $url_noticia, $titulo, $conteudo];
            $return = $this->db->insertPrepared($this->tblname, $this->dataFields, $dataValues);
            return $return;
        }

        #selectPrepared($tblname, array $condNames = [], array $condValues = [], $fields = "", $orderByField = "", $limitResults = "")

        public function selecionaNoticia($id, $fields, $orderByField, $limitResults){
            $condNames = ["id"];
            $condValues = [$id];
            $return = $this->db->selectPrepared($this->tblname, $condNames, $condValues, $fields, $orderByField, $limitResults);
            return $return;
        }

        #updatePrepared($tblname, array $datafields, array $updateValues, array $condFields, array $condValues)

        public function atualizaNoticia($id, $url_noticia, $titulo, $conteudo){
            $novaData = date("Y-m-d h:i:s");
            $datafields = [$this->dataFields[1],$this->dataFields[2],$this->dataFields[3],$this->dataFields[4]];
            $updateValues = [$novaData, $url_noticia, $titulo, $conteudo];
            $condFields = ["id"];
            $condValues = [$id];
            $return = $this->db->updatePrepared($this->tblname, $datafields, $updateValues, $condFields, $condValues);
            return $return;
        }

        #deletePrepared($tblname, array $dataFields, array $dataValues)
        public function removeNoticia($id){
            $condFields= ["id"];
            $condValues = [$id];
            $return = $this->db->deletePrepared($tblname, $condFields, $condValues);
            return $return;
        }
    }

}
