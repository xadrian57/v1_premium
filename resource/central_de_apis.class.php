<?php

    class CentralDeApis{

        const TIMEOUT = 10;

        private $apis;


        public function __construct()
        {
            // TODA NOVA INTEGRAÇÃO DE API DEVE ADICIONAR O NOME A ESSA LISTA, E AO ARQUIVO ASSYNC
            $this->apis = ['octadesk', 'lahar', 'slack', 'pipedrive'];
            
        }





        /*
            $dados = array(
                'nome' => , //nome da loja
                'email' => , //email administrativo
                'plataforma' => , //plataforma
            );
		*/
        public function octadesk ($dados, $sync = true){

            if(!$sync){
                $this->rodaAssync('octadesk', $dados);
                return;
            }

            $url = "https://api.octadesk.services/persons";

            $dados2 = array(
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'isLocked' => true,
                'permissionType' => 0,
                'roleType' => 5,
                'type' => 2,
                'customField' => array(
                    'Plataforma' => $dados['plataforma'],
                    'Plano' => 'Trial',
                    'Loja' => $dados['nome']
                    )
                );

            $headers = array(
				"Content-Type: application/json",
				"Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWJkb21haW4iOiJyb2loZXJvIiwiaXNzdWVkYXRlIjoiMjAxOC0wMS0yNFQxOToxODoyNC4wNzJaIiwiaXNzIjoiYXBpLm9jdGFkZXNrLnNlcnZpY2VzIiwicm9sZSI6MiwiZW1haWwiOiJkYW5pbG8ucHJhZG9Acm9paGVyby5jb20uYnIiLCJuYW1lIjoiRGFuaWxvIFByYWRvIiwidHlwZSI6MSwiaWQiOiJiOTc5MzBmMS02ZGE4LTRlMDItOWIzMi0wYjUyMWQ4OGJmNjYiLCJyb2xlVHlwZSI6MiwicGVybWlzc2lvblR5cGUiOjEsInBlcm1pc3Npb25WaWV3IjoxLCJpYXQiOjE1MTY4MjE1MDR9.Xgq-TaWdhIShkc_pubMfuKriCRJRZXFqrkfbfn2bu0k"
			);

            return $this->conexao ($url, $headers, $dados2, 'post', $sync);

        }




        /*
            $dados = array(
			    'email_contato' => $email, // Texto, 100 caracteres, email, OBRIGATÓRIO
			    'nome_contato' => $nomeResponsavel, // Texto, 100 caracteres
			    'nome_empresa' => $nome, // Texto, 100 caracteres
			    'site_empresa' => $site, // Texto, 100 caracteres
			    'tel_empresa' => $telefone, // Numérico (pode receber número formatado, ex. (14) 3222-1415)
                'email_empresa' => $email, // Texto, 100 caracteres, email
                -> inserido na classe 'token_api_lahar => , //token lahar
                'nome_formulario' => "cadastro" ou ________,
                'url_origem' => https://www.roihero.com.br/
			 );
        */
        public function lahar ($dados, $sync = true){

            if(!$sync){
                $this->rodaAssync('lahar', $dados);
                return;
            }

            $token = "roihero1ctS81KM5D9EXC7SEa2dNZ7Y7TKmAcrur1EKA2CG9568UoPCibOkSIaw";

            $url = 'https://app.lahar.com.br/api/conversions';
            
            $dados['token_api_lahar'] = $token;

            $headers = [];

            return $this->conexao ($url, $headers, $dados, 'post', $sync);

        }




        /*
            $dados = array(
                'text' =>  ,  //texto a ser informado. Atualmente usamos:
                    "Nome do Cliente: Nome\nPlataforma: Plat\nE-mail: email@g.com\nTelefone: 15555\nSite: site.com\nNome Respons\u00e1vel: Resp"
                'username' =>  , //nome de usuário. Atualmente usamos "novo_cadastro"
            );
        */
        public function slack ($dados, $sync = true){
            if(!$sync){
                $this->rodaAssync('slack', $dados);
                return;
            }

            $url = 'https://hooks.slack.com/services/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';

            $headers = ["Content-Type: application/x-www-form-urlencoded"];

            return $this->conexao ($url, $headers, $dados, 'post', $sync);

        }




        /*
            $dados = array(
                'nome' =>, 
                'email' => , 
                'telefone' =>, 
                'nomeResponsavel' => 
            );
        */
        public function pipedrive ($dadosEmpresa, $sync = true){

            if(!$sync){
                $this->rodaAssync('pipedrive', $dadosEmpresa);
                return;
            }

            $headers = ["Content-Type: application/json"];
            $token = "678b0d0abd5976bd462a65aa80dfd56295972124";


            //para postar organização
            $dados = array(
                'name' => $dadosEmpresa['nome']
            );
            $url = "https://roihero.pipedrive.com/v1/organizations?api_token=".$token;

            $retorno = $this->conexao ($url, $headers, $dados, 'post', true);

            if($retorno['status'] == 200) //se a inclusão deu certo, continua e recupera o id da org inserida
				$idOrg = $retorno['resposta']['data']['id'];
			else //se não teve sucesso, retorna nesse ponto com o erro
                return $retorno;
                


            //para postar pessoa
            $dados = array(
                "name" => $dadosEmpresa['nomeResponsavel'],
                "email" => $dadosEmpresa['email'],
                "phone" => $dadosEmpresa['telefone'],
                "org_id" => $idOrg
            );
            $url = "https://roihero.pipedrive.com/v1/persons?api_token=".$token;

            $retorno = $this->conexao ($url, $headers, $dados, 'post', true);

            if($retorno['status'] == 200) //se a inclusão deu certo, continua e recupera o id da person inserida
				$idCliPipe = $retorno['resposta']['data']['id'];
			else //se não teve sucesso, retorna nesse ponto com o erro
                return $retorno;
                


            //para postar Deal
            $dados = array(
                "title" => $dadosEmpresa['nome'],
                "person_id" => $idCliPipe,
                "stage_id" => 38,
                "org_id" => $idOrg
            );
            $url = "https://roihero.pipedrive.com/v1/deals?api_token=".$token;

            $retorno = $this->conexao ($url, $headers, $dados, 'post', true);

            if($retorno['status'] == 200) //se a inclusão deu certo, continua e recupera o id do deal inserido
				$idDeal = $retorno['resposta']['data']['id'];
			else //se não teve sucesso, retorna nesse ponto com o erro
                return $retorno;



            $hoje = date("Y-m-d");
            $dados = array(
                "subject" => "Ligar",
                "type" => "call",
                "due_date" => $hoje,
                "deal_id" => $idDeal,
                "person_id" => $idCliPipe,
                "org_id" => $idOrg
            );
            $url = "https://roihero.pipedrive.com/v1/activities?api_token=".$token;

            return $this->conexao ($url, $headers, $dados, 'post', true);

                

        }


        private function rodaAssync($api, $dados){

            if(in_array($api, $this->apis)){
                $dados = json_encode($dados);
                $dados = str_replace('"', '\"', $dados);
                $execString = "php api_assync_exec.php " . $api . ' "' .  $dados . '" > /dev/null &';
                exec($execString);
            }

            //exec('php processo-longo.php funciona > /dev/null &');
        }


        private function conexao ($url, $headers, $dados, $metodo){

            try{
                
                //json encode data
                $json_data = json_encode($dados);
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 0);
                curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                switch ($metodo) {
                    case 'delete': 
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                        break;
                    case 'get': 
                        $url .= '?'.http_build_query($dados);
                        break;
                    case 'post':     
                        curl_setopt($ch, CURLOPT_POST, TRUE);                
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                        break;
                    default:
                        break;
                }

                //add headers
                if(count($headers) > 0)
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                
                curl_setopt($ch, CURLOPT_URL, $url);
                
                
                $http_response = curl_exec($ch);
                $error       = curl_error($ch);
                $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $resposta = array(
                    'status' => $http_code,
                    'resposta' => $http_response
                );

                return $resposta;

            } catch (Exception $e) {
                $retorno = array(
                    'status' => 'erro',
                    'resposta' => array(
                        'error' => array(
                            'code' => 404,
                            'message' => 'Erro imprevisto da api.'
                        )
                    )
                );

                return $retorno;
            } 
        }
    }

?>