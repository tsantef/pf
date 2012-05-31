<?php
class PHPFog
{
    public $phpfog;
    public $username = null;
    public $api_auth_token = null;
    public $session = null;
    private $session_path = null;

    public function __construct($show_login = true) {
        $this->session_path = HOME.".pf-command-session";
        $this->load_session();
        $this->phpfog = new \PestJSON((isset($_ENV['PHPFOG_URL']) && $_ENV['PHPFOG_URL'] != '') ? $_ENV['PHPFOG_URL'] : "https://www.phpfog.com");
        if ($show_login && $this->username() != '') {
            echo wrap("Running command as ".bwhite($this->username()));
        }
    }

    # --- Clouds --- #

    public function get_clouds() {
        $client = $this;
        $response = $this->api_call(function() use ($client) {
            return $client->phpfog->get("/dedicated_clouds", array("Api-Auth-Token: ".$client->api_auth_token()));
        });

        return $response;
    }

    # --- Apps --- #

    public function get_apps($cloud_id=null) {
        $request_url = ($cloud_id != null) ? "/clouds/".$cloud_id."/apps" : "/apps";
        $client = $this;
        $response = $this->api_call(function() use ($client, $request_url) {
            return $client->phpfog->get($request_url, array("Api-Auth-Token: ".$client->api_auth_token()));
        });

        return $response;
    }

    public function get_app($app_id) {
        $client = $this;
        $response = $this->api_call(function() use ($client, $app_id) {
            return $client->phpfog->get("/apps/".$app_id, array("Api-Auth-Token: ".$client->api_auth_token()));
        });

        return $response;
    }

    public function get_app_id_by_name($app_name) {
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

    public function delete_app($app_id) {
        $client = $this;
        $response = $this->api_call(function() use ($client, $app_id) {
            return $client->phpfog->delete("/apps/".$app_id, array("Api-Auth-Token: ".$client->api_auth_token()));
        });

        return $response;
    }

    # --- SSH Keys ---- #

    public function get_sshkeys() {
        $client = $this;
        $response = $this->api_call(function() use ($client) {
            return $client->phpfog->get("/ssh_keys", array("Api-Auth-Token: ".$client->api_auth_token()));
        });

        return $response;
    }

    public function new_sshkey($ssh_key_name, $ssh_key_key) {
        $client = $this;
        $payload = array('name' => $ssh_key_name, 'key' => $ssh_key_key );
        $response = $this->api_call(function() use ($client, $payload) {
            return $client->phpfog->post("/ssh_keys", $payload, array("Api-Auth-Token: ".$client->api_auth_token()));
        });

        return $response;
    }

    public function delete_sshkey($sshkey_id) {
        $client = $this;
        $response = $this->api_call(function() use ($client, $sshkey_id) {
            return $client->phpfog->delete("/ssh_keys/".$sshkey_id, array("Api-Auth-Token: ".$client->api_auth_token()));
        });

        return $response;
    }

    # ---

    public function login($username = null) {
        if (empty($username)) {
            $username = trim(prompt("PHP Fog Username: "));
        }
        $password = trim(prompt("PHP Fog Password: ", true));
        $payload = array('login' => $username, 'password' => $password);
        $response = $this->phpfog->post("/user_session", $payload);
        if ($this->phpfog->lastStatus() == 201) {
            $this->session['current_user'] = $username;
            $this->session[$this->username()] = array();
            $this->session[$this->username()]['api-auth-token'] = $response['api-auth-token'];
            $this->save_session();

            return true;
        }

        return false;
    }

    public function logout($username = null) {
        if ($username == null) {
            @unlink($this->session_path);

            return true;
        } else {
            if (array_key_exists($username, $this->session)) {
                if ($this->username() == $username) {
                    unset($this->session['current_user']);
                }
                unset($this->session[$username]);
                $this->save_session();

                return true;
            }
        }

        return false;
    }

    public function switch_user($username) {
        if (array_key_exists($username, $this->session)) {
            if (array_key_exists('api-auth-token', $this->session[$username])) {
                $this->session['current_user'] = $username;
                $this->save_session();

                return true;
            }
        }

        return false;
    }

    public function username() {
        if (isset($this->session['current_user'])) {
            return $this->session['current_user'];
        } else {
            return null;
        }
    }

    public function api_auth_token() {
        return $this->session[$this->username()]['api-auth-token'];
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
        } catch (PestJSON_Unauthorized $e) {
            try {
                $this->login();
                $result = is_object($block) ? $block() : $block;
            } catch (PestJSON_Unauthorized $e) {
                failure_message("Invalid login or password. Please try again.");
                exit(1);
            } catch (Exception $e) {
                failure_message("Error: ".$e->getMessage());
                exit(1);
            }
        } catch (PestJSON_BadRequest $e) {
            failure_message("There was a problem making that request. Please try again.");
            exit(1);
        }

        return $result;
    }

    public function get_api_error_message() {
        $resp = $this->last_response();
        $body = json_decode($resp['body']);

        return $body->message;
    }

    public function load_session() {
        $this->session = file_exists($this->session_path) ? json_decode(file_get_contents($this->session_path), true) : array();
    }

    public function save_session() {
        file_put_contents($this->session_path, json_encode($this->session));
    }
}
