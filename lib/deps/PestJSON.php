<?php

/**
 *
 *  This class is a combination of the Pest and PestJSON classes to simplify our cause.
 *
 */

class PestJSON
{
    public $curl_opts = array(
        CURLOPT_RETURNTRANSFER => true,  # return result instead of echoing
        CURLOPT_SSL_VERIFYPEER => false, # stop cURL from verifying the peer's certificate
        CURLOPT_FOLLOWLOCATION => true,  # follow redirects, Location: headers
        CURLOPT_MAXREDIRS => 10          # but dont redirect more than 10 times
    );

    public $base_url;
    public $last_response;
    public $last_request;
    public $throw_exceptions = true;

    public function __construct($base_url) {
        if (!function_exists('curl_init')) {
            throw new Exception('CURL module not available! Pest requires CURL. See http://php.net/manual/en/book.curl.php');
        }

        $this->base_url = $base_url;
    }

    # $auth can be 'basic' or 'digest'
    public function setupAuth($user, $pass, $auth = 'basic') {
        $this->curl_opts[CURLOPT_HTTPAUTH] = constant('CURLAUTH_'.strtoupper($auth));
        $this->curl_opts[CURLOPT_USERPWD] = $user.':'.$pass;
    }

    public function get($url, $headers = array()) {
        $curl_opts = $this->curl_opts;
        $curl_opts[CURLOPT_HEADER] = false;
        $curl_opts[CURLOPT_HTTPHEADER] = $headers;

        return $this->processBody($this->doRequest($this->prepRequest($curl_opts, $url)));
    }

    public function post($url, $data, $headers = array()) {
        $data = json_encode($data);
        $data = (is_array($data)) ? http_build_query($data) : $data;

        $headers[] = 'Content-Length: '.strlen($data);

        $curl_opts = $this->curl_opts;
        $curl_opts[CURLOPT_CUSTOMREQUEST] = 'POST';
        $curl_opts[CURLOPT_HEADER] = false;
        $curl_opts[CURLOPT_HTTPHEADER] = $headers;
        $curl_opts[CURLOPT_RETURNTRANSFER] = true;

        $curl_opts[CURLOPT_POST] = true;
        $curl_opts[CURLOPT_POSTFIELDS] = $data;

        return $this->processBody($this->doRequest($this->prepRequest($curl_opts, $url)));
    }

    public function put($url, $data, $headers = array()) {
        $data = json_encode($data);
        $data = (is_array($data)) ? http_build_query($data) : $data;

        $headers[] = 'Content-Length: '.strlen($data);

        $curl_opts = $this->curl_opts;
        $curl_opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
        $curl_opts[CURLOPT_HTTPHEADER] = $headers;
        $curl_opts[CURLOPT_POSTFIELDS] = $data;

        return $this->processBody($this->doRequest($this->prepRequest($curl_opts, $url)));
    }

    public function delete($url, $headers = array()) {
        $curl_opts = $this->curl_opts;
        $curl_opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        $curl_opts[CURLOPT_HEADER] = false;
        $curl_opts[CURLOPT_HTTPHEADER] = $headers;

        return $this->processBody($this->doRequest($this->prepRequest($curl_opts, $url)));
    }

    public function lastBody() {
        return $this->last_response['body'];
    }

    public function lastStatus() {
        return $this->last_response['meta']['http_code'];
    }

    protected function processBody($body) {
        # The body of every GET/POST/PUT/DELETE response goes through
        # here prior to being returned.

        return json_decode($body, true);
    }

    protected function processError($body) {
        # The body of every erroneous (non-2xx/3xx) GET/POST/PUT/DELETE
        # response goes through here prior to being used as the 'message'
        # of the resulting PestJSON_Exception

        return $body;
    }


    protected function prepRequest($opts, $url) {
        $opts[CURLOPT_HTTPHEADER][] = 'Accept: application/json';
        $opts[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';

        if (strncmp($url, $this->base_url, strlen($this->base_url)) != 0) {
            $url = $this->base_url . $url;
        }
        $curl = curl_init($url);

        foreach ($opts as $opt => $val) {
            curl_setopt($curl, $opt, $val);
        }

        $this->last_request = array(
            'url' => $url
        );

        $this->last_request['method'] = (isset($opts[CURLOPT_CUSTOMREQUEST])) ? $opts[CURLOPT_CUSTOMREQUEST] : 'GET';

        if (isset($opts[CURLOPT_POSTFIELDS])) {
            $this->last_request['data'] = $opts[CURLOPT_POSTFIELDS];
        }

        return $curl;
    }

    private function doRequest($curl) {
        //curl_setopt($curl, CURLOPT_VERBOSE, true);
        $body = curl_exec($curl);

        $this->last_response = array(
            'body' => $body,
            'meta' => curl_getinfo($curl)
        );

        curl_close($curl);

        $this->checkLastResponseForError();

        return $body;
    }

    protected function checkLastResponseForError() {
        if (!$this->throw_exceptions) {
            return;
        }

        $meta = $this->last_response['meta'];
        $body = $this->last_response['body'];

        if (!$meta) {
            return;
        }

        switch ($meta['http_code']) {
            case 400:
                throw new PestJSON_BadRequest($this->processError($body));
                break;
            case 401:
                throw new PestJSON_Unauthorized($this->processError($body));
                break;
            case 403:
                throw new PestJSON_Forbidden($this->processError($body));
                break;
            case 404:
                throw new PestJSON_NotFound($this->processError($body));
                break;
            case 405:
                throw new PestJSON_MethodNotAllowed($this->processError($body));
                break;
            case 409:
                throw new PestJSON_Conflict($this->processError($body));
                break;
            case 410:
                throw new PestJSON_Gone($this->processError($body));
                break;
            case 422:
                # Unprocessable Entity -- see http://www.iana.org/assignments/http-status-codes
                # This is now commonly used (in Rails, at least) to indicate
                # a response to a request that is syntactically correct,
                # but semantically invalid (for example, when trying to
                # create a resource with some required fields missing)
                throw new PestJSON_InvalidRecord($this->processError($body));
                break;
            default:
                if ($meta['http_code'] >= 400 && $meta['http_code'] <= 499) {
                    throw new PestJSON_ClientError($this->processError($body));
                } elseif ($meta['http_code'] >= 500 && $meta['http_code'] <= 599) {
                    throw new PestJSON_ServerError($this->processError($body));
                } elseif (!$meta['http_code'] || $meta['http_code'] >= 600) {
                    throw new PestJSON_UnknownResponse($this->processError($body));
                }
        }
    }
}

class PestJSON_Exception extends \Exception {}
class PestJSON_UnknownResponse extends PestJSON_Exception {}
class PestJSON_ClientError extends PestJSON_Exception {}
class PestJSON_BadRequest extends PestJSON_ClientError {}
class PestJSON_Unauthorized extends PestJSON_ClientError {}
class PestJSON_Forbidden extends PestJSON_ClientError {}
class PestJSON_NotFound extends PestJSON_ClientError {}
class PestJSON_MethodNotAllowed extends PestJSON_ClientError {}
class PestJSON_Conflict extends PestJSON_ClientError {}
class PestJSON_Gone extends PestJSON_ClientError {}
class PestJSON_InvalidRecord extends PestJSON_ClientError {}
class PestJSON_ServerError extends PestJSON_Exception {}
