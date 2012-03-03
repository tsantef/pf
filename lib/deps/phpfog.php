<?php

class PHPFog {

    public $phpfog;
    public $username = null;
    public $api_auth_token = null;
    public $session = null;
    private $session_path = null;

    public function __construct() {
        $this->session_path = HOME . ".pf-command-session";
        $this->load_session();
        $this->phpfog = new \PestJSON((isset($_ENV['PHPFOG_URL']) && $_ENV['PHPFOG_URL'] != "") ? $_ENV['PHPFOG_URL'] : "https://www.phpfog.com");
    }

    # --- SSH Keys ----

    # def get_sshkeys
    #   response = api_call do
    #     $phpfog.get("/ssh_keys", nil, { :accept => "application/json", :content_type => "application/json", "Api-Auth-Token"=>get_session('api-auth-token') })
    #   end
    #   response_body = JSON.parse(response.body)
    #   if response.code == 200
    #     return { :status => response.code, :message => "OK" , :body => response_body }
    #   else
    #     return { :status => response.code, :message => response_body["message"] , :body => response_body }
    #   end
    # end

    function get_sshkeys() {
        $client = $this;
        $response = $this->api_call(function() use ($client) {
            $client->phpfog->curl_opts[CURLOPT_HTTPHEADER] = array("Api-Auth-Token: ".$client->session['api-auth-token']);
            return $client->phpfog->get("/dedicated_clouds");
        });
        return $response;
    }

    function new_sshkey($ssh_key_name, $ssh_key_key) {
        $client = $this;
        $payload = array('name' => $ssh_key_name, 'key' => $ssh_key_key );
        $response = $this->api_call(function() use ($client, $payload) {
            return $client->phpfog->post("/ssh_keys", $payload, array("Api-Auth-Token: ".$client->session['api-auth-token']));
        });
        return $response;
    }

    # def delete_sshkey(sshkey_id)
    #   response = api_call do
    #     response = $phpfog.delete("/ssh_keys/#{sshkey_id}", nil, { :accept => "application/json", "Api-Auth-Token"=>get_session('api-auth-token') })
    #   end
    #   response
    # end

    # ---

    public function login() {
        $username = trim(prompt("PHPFog Username: "));
        $password = trim(prompt("PHPFog Password: ", true));
        $payload = array('login' => $username, 'password' => $password);
        $response = $this->phpfog->post("/user_session", $payload);
        if ($this->phpfog->lastStatus() == 201) {
            $this->session['api-auth-token'] = $response['api-auth-token'];
            $this->session['username'] = $username;
            $this->save_session();
            return true;
        }
        return false;
    }

    public function username() {
        return $this->session['username'];
    }

    public function last_status() {
         return $this->phpfog->lastStatus();
    }

    public function last_response() {
        return $this->phpfog->last_response;
    }

    protected function api_call($block) {
        $result = null;
        try {
            $result = is_object($block) ? $block() : $block;
        } catch (\Pest_Unauthorized $e) {
            if ($this->login()) {
                $result = is_object($block) ? $block() : $block;
            } else {
                echo "Login Failed";
            }
        }

        return $result;
    }

    // function prompt($prompt, $pw = false) {
    //     # If client is using Windows OS
    //     // if (preg_match('/^win/i', PHP_OS)) {
    //     //     $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
    //     //     file_put_contents($vbscript, 'wscript.echo(InputBox("'.addslashes($prompt).'", "", "password here"))');
    //     //     $command = "cscript //nologo " . escapeshellarg($vbscript);
    //     //     $password = rtrim(shell_exec($command));
    //     //     unlink($vbscript);
    //     //     return $password;
    //     // } else {
    //     //     # IF *nix-based
    //     //     $command = "/usr/bin/env bash -c 'echo OK'";
    //     //     if (rtrim(shell_exec($command)) !== 'OK') {
    //     //         trigger_error("Can't invoke bash");
    //     //         return;
    //     //     }
    //     //     $command = "/usr/bin/env bash -c 'read -s -p \"".addslashes($prompt)."\" mypassword && echo \$mypassword'";
    //     //     $password = rtrim(shell_exec($command));
    //     //     echo PHP_EOL;
    //     //     return $password;
    //     // }
    // }

    function load_session() {
        if (file_exists($this->session_path)) {
            $this->session = json_decode(file_get_contents($this->session_path), true);
        } else {
            $this->session = array();
        }
    }

    function save_session() {
        file_put_contents($this->session_path, json_encode($this->session));
    }

}
