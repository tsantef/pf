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

    # --- Clouds --- #

    function get_clouds() {
        $client = $this;
        $response = $this->api_call(function() use ($client) {
            return $client->phpfog->get("/dedicated_clouds", array("Api-Auth-Token: ".$client->session['api-auth-token']));
        });
        return $response;
    }

    # --- Apps --- #

    function get_apps($cloud_id=null) {
        $request_url = ($cloud_id != null) ? "/clouds/$cloud_id/apps" : "/apps";
        $client = $this;
        $response = $this->api_call(function() use ($client, $request_url) {
            return $client->phpfog->get($request_url, array("Api-Auth-Token: ".$client->session['api-auth-token']));
        });
        return $response;
    }

    function get_app($app_id) {
        $client = $this;
        $response = $this->api_call(function() use ($client, $app_id) {
            return $client->phpfog->get("/apps/$app_id", array("Api-Auth-Token: ".$client->session['api-auth-token']));
        });
        return $response;
    }

    function get_app_id_by_name($app_name) {
        $app_id = null;
        $raw_app_name = strtolower($app_name);
        $apps = $this->get_apps();
        foreach ($apps as $app) {
            if ($app['name'] == $raw_app_name) {
                $app_id = $app['id'];
                break;
            }
        }
        return $app_id;
    }

    # --- SSH Keys ---- #

    function get_sshkeys() {
        $client = $this;
        $response = $this->api_call(function() use ($client) {
            return $client->phpfog->get("/ssh_keys", array("Api-Auth-Token: ".$client->session['api-auth-token']));
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
        $username = trim(prompt("PHP Fog Username: "));
        $password = trim(prompt("PHP Fog Password: ", true));
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

    public function logout() {
        unlink($this->session_path);
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
        } catch (\PestJSON_Unauthorized $e) {
            try {
                $this->login();
                $result = is_object($block) ? $block() : $block;
            } catch (Exception $e) {
                falure_message($this->get_api_error_message().PHP_EOL);
                exit(1);
            }
        }

        return $result;
    }

    function get_api_error_message() {
        $resp = $this->last_response();
        $body = json_decode($resp['body']);
        return $body->message;
    }

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
