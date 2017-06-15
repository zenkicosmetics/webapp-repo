<?php

/**
 * Created by PhpStorm.
 * User: thain
 * Date: 5/9/2017
 * Time: 14:13
 */
require_once(APPPATH . 'libraries/Dropbox/autoload.php');

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Exceptions\DropboxClientException;

class DropboxV2
{
    protected $dropboxApi;

    public function __construct($setting)
    {
        $this->dropboxApi = new DropboxApi($setting);
    }

    public function __call($method_name, $args)
    {
        try {
            return call_user_func_array([$this->dropboxApi, $method_name], $args);
        } catch (DropboxClientException $e) {
            log_audit_message('error', $e->getMessage(), true, 'Dropbox Error');
            return null;
        }
    }
}

class DropboxApi
{
    protected $app;
    protected $dropbox;
    protected $app_key = '';
    protected $app_secret = '';
    protected $access_token = '';
    protected $callback_url = '';
    protected $folder_name = '';

    /**
     * Dropboxtest constructor.
     * Constructor need app_key, app_secret to init dropbox object
     * If access_token was provided, dropbox object can work without authentication
     * @param array $setting
     */
    public function __construct($setting)
    {
        session_start();

        $this->app_key = Settings::get(APConstants::DROPBOX_APP_KEY);
        $this->app_secret = Settings::get(APConstants::DROPBOX_APP_SECRET);
        $this->callback_url = Settings::get(APConstants::DROPBOX_CALLBACK_URL);

        $this->access_token = !empty($setting['access_token']) ? $setting['access_token'] : '';
        $this->folder_name = !empty($setting['folder_name']) ? $setting['folder_name'] : '';

        $this->initDropbox();
    }

    public function initDropbox()
    {
        $this->app = new DropboxApp($this->app_key, $this->app_secret, $this->access_token);
        $this->dropbox = new Dropbox($this->app);
    }

    //<editor-fold desc="Getter and Setter for fields">
    /**
     * @return mixed
     */
    public function getAppKey()
    {
        return $this->app_key;
    }

    /**
     * @param mixed $app_key
     */
    public function setAppKey($app_key)
    {
        $this->app_key = $app_key;
        $this->initDropbox();
    }

    /**
     * @return mixed
     */
    public function getFolderName()
    {
        return $this->folder_name;
    }

    /**
     * @param mixed $folder_name
     */
    public function setFolderName($folder_name)
    {
        $this->folder_name = $folder_name;
    }

    /**
     * @return mixed
     */
    public function getAppSecret()
    {
        return $this->app_secret;
    }

    /**
     * @param mixed $app_secret
     */
    public function setAppSecret($app_secret)
    {
        $this->app_secret = $app_secret;
        $this->initDropbox();
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @param mixed $access_token
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
        $this->initDropbox();
    }

    /**
     * @return mixed
     */
    public function getSetting()
    {
        $setting = [
            'app_key' => $this->app_key,
            'app_secret' => $this->app_secret,
            'access_token' => $this->access_token,
            'callback_url' => $this->callback_url,
            'folder_name' => $this->folder_name,
        ];
        return $setting;
    }

    //</editor-fold>
    /**
     * request access token to dropbox
     */
    public function get_request_token()
    {
        $auth_helper = $this->dropbox->getAuthHelper();
        $authUrl = $auth_helper->getAuthUrl($this->callback_url);
        redirect($authUrl);
    }

    /**
     * verify after request access token
     * @return null|string
     */
    public function verify_callback()
    {
        if (isset($_GET['code']) && isset($_GET['state'])) {
            $code = $_GET['code'];
            $state = $_GET['state'];

            $auth_helper = $this->dropbox->getAuthHelper();
            $accessToken = $auth_helper->getAccessToken($code, $state, $this->callback_url);
            $this->access_token = $accessToken->getToken();
            return $this->access_token;
        }
        return null;
    }

    /**
     * create folder on dropbox
     * @param $folder_name
     * @param $sub : if TRUE => create subfolder in folder_name
     */
    public function create_folder($folder_name)
    {
        $this->dropbox->createFolder("$folder_name");
    }

    /**
     * add file to path
     * @param $path : path to update dropbox
     * @param $filename : path to file upload (local file)
     * @param array $options
     */
    public function add($path, $filename, $options = [])
    {
        $options['autorename'] = true;
        $fname = basename($filename);
        $dropboxFile = new DropboxFile($filename);
        $this->dropbox->upload($dropboxFile, "$path/$fname", $options);
    }

    /**
     * get meta data of file or folder
     * @param $path
     * @param array $options
     * @return \Kunnu\Dropbox\Models\FileMetadata|\Kunnu\Dropbox\Models\FolderMetadata
     */
    public function getMetadata($path, $options = [])
    {
        $metadata = $this->dropbox->getMetadata($path, $options);
        return $metadata;
    }

    /**
     * Return content (files, subfolder) in dropbox folder path
     * @param $path
     * @param string $type
     * @return array
     */
    public function getList($path, $type = '')
    {
        $listFolderContents = $this->dropbox->listFolder($path);
        $items = $listFolderContents->getItems();
        $all = $items->all();
        $result = [];
        foreach ($all as $item) {
            $data = $item->getData();
            if ($data['.tag'] == $type) {
                $result[] = $data;
            }
        }
        return $result;
    }
}
