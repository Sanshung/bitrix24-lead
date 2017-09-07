<?

// CRM server conection data
define('CRM_HOST', 'your_server.bitrix24.kz'); // your CRM domain name
define('CRM_PORT', '443'); // CRM server port
define('CRM_PATH', '/crm/configs/import/lead.php'); // CRM server REST service path

// CRM server authorization data
define('CRM_LOGIN', 'login'); // login of a CRM user able to manage leads
define('CRM_PASSWORD', 'password'); // password of a CRM user
// OR you can send special authorization hash which is sent by server after first successful connection with login and password
//define('CRM_AUTH', 'e54ec19f0c5f092ea11145b80f465e1a'); // authorization hash

/********************************************************************************************/

function bitrix24Lead($postData)
{


// append authorization data
    if (defined('CRM_AUTH')) {
        $postData['AUTH'] = CRM_AUTH;
    } else {
        $postData['LOGIN'] = CRM_LOGIN;
        $postData['PASSWORD'] = CRM_PASSWORD;
    }

// open socket to CRM
    $fp = fsockopen("ssl://" . CRM_HOST, CRM_PORT, $errno, $errstr, 30);
    if ($fp) {
        // prepare POST data
        $strPostData = '';
        foreach ($postData as $key => $value)
            $strPostData .= ($strPostData == '' ? '' : '&') . $key . '=' . urlencode($value);

        // prepare POST headers
        $str = "POST " . CRM_PATH . " HTTP/1.0\r\n";
        $str .= "Host: " . CRM_HOST . "\r\n";
        $str .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $str .= "Content-Length: " . strlen($strPostData) . "\r\n";
        $str .= "Connection: close\r\n\r\n";

        $str .= $strPostData;

        // send POST to CRM
        fwrite($fp, $str);

        // get CRM headers
        $result = '';
        while (!feof($fp)) {
            $result .= fgets($fp, 128);
        }
        fclose($fp);

        // cut response headers
        $response = explode("\r\n\r\n", $result);

        return '<pre>' . print_r($response[1], 1) . '</pre>';
    } else {
        return 'Connection Failed! ' . $errstr . ' (' . $errno . ')';
    }
}
?>
