<?php
    namespace Hcode\Model;
    use \Hcode\DB\Sql;
    use \Hcode\Model;

    class Address extends Model{

        const SESSION_ERROR = "AddressError";
        
        public static function getCep($nrCep){

            $nrCep = str_replace("-","", $nrCep);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/$nrCep/json/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $data = json_decode(curl_exec($ch), true);

            curl_close($ch);

            return $data;
        }

        public function loadFromCep($nrCep){

            $data = Address::getCep($nrCep);

            if(isset($data["localidade"]) && $data["localidade"]){
                $this->setdesaddress($data["logradouro"]);
                $this->setdescomplement($data["complemento"]);
                $this->setdesdistrict($data["bairro"]);
                $this->setdescity($data["localidade"]);
                $this->setdesstate($data["uf"]);
                $this->setdescountry("Brasil");
                $this->setdeszipcode($nrCep);
            }
        }

        public function save(){
            $sql = new Sql();

            $results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :desnumber, :complement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", array(
                "idaddress"=>$this->getidaddress(),
                "idperson"=>$this->getidperson(),
                "desaddress"=>utf8_decode($this->getdesaddress()),
                "desnumber"=>$this->getdesnumber(),
                "complement"=>utf8_decode($this->getcomplement()),
                "descity"=>utf8_decode($this->getdescity()),
                "desstate"=>utf8_decode($this->getdesstate()),
                "descountry"=>utf8_decode($this->getdescountry()),
                "deszipcode"=>$this->getdeszipcode(),
                "desdistrict"=>utf8_decode($this->getdesdistrict())
            ));

            if($results > 0){
                $this->setData($results[0]);
            }
        }

        public static function setMsgError($msg){
            $_SESSION[Address::SESSION_ERROR] = $msg;
        }

        public static function getMsgError(){
            $msg =  (isset($_SESSION[Address::SESSION_ERROR])) ? $_SESSION[Address::SESSION_ERROR] : "";
            Address::clearMsgError();
            return $msg;
        }

        public static function clearMsgError(){
            $_SESSION[Address::SESSION_ERROR] = NULL;
        }
    }
?>