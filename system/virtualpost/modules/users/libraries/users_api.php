<?php defined('BASEPATH') or exit('No direct script access allowed');

class users_api
{
    public function __construct() {
        
        ci()->load->model(array(
            'users/ion_auth_model',
            'users/user_m',
            'addresses/location_users_m' 
        ));
        
        ci()->load->library(array(
            'users/Ion_auth'
        ));
        
    }

    public static function updatePassword($new_pass, $user_meta)
    {
        $password = ci()->ion_auth_model->hash_password_db($user_meta->id, $new_pass);

        $resultRow = ci()->user_m->update($user_meta->id, array(
            "password" => $password,
            "forgotten_password_code" => null,
            "forgotten_password_time" => 0
        ));

        return $resultRow;
    }

    /**
     * @param $email
     * @return mixed
     */
    public static function getUser($email)
    {
        ci()->load->model('users/user_m');

        $param['email'] = $email;

        $user = ci()->user_m->get($param);

        return $user;
    }

    public static function forgotten_password($email)
    {
        ci()->load->model('users/user_m');

        $key = ci()->user_m->forgotten_password($email);

        return $key;
    }

    public static function check_login ($email, $password, $remember_me)
    {
        ci()->load->model('instances/supper_admin_m');
        ci()->load->model('users/user_m');
        ci()->load->library('users/Ion_auth');

        $response["status"] = false;
        $response["message"] = "";


        $supper_admin = ci()->supper_admin_m->get_by_many(array(
                "user_name" => $email
        ));

        if ($supper_admin) {

            if ($supper_admin->password == md5($password)) {
                $this->session->set_userdata(APConstants::SESSION_SUPPERUSERADMIN_KEY, $supper_admin);
                $response["status"] = true;
                return $response;
            }
        }
        
        if (ci()->ion_auth->login($email, $password, $remember_me)) {
            // Get user admin
            $user = ci()->user_m->get_user_info(array(
                    "email" => $email
            ));
            // Check empty user
            if (empty($user)) {

                $response["status"] = false;
                $response["message"] = "User name or password is invalid.";
                return $response;
            }
            
            // Check delete_flag
            if ($user->delete_flag != APConstants::ON_FLAG) {
                ci()->session->set_userdata(APConstants::SESSION_USERADMIN_KEY, $user);
                $response["status"] = true;
                return $response;
            }
        }

        $response["status"] = false;
        $response["message"] = ci()->ion_auth->errors();
        return $response;
    }

    public static function set_group_user($user_login){

        ci()->load->model('users/group_user_m');
        
        $group_users = ci()->group_user_m->get_many_by_many(array(
            "user_id" => $user_login->id
        ));
        
        $group_id = array();
       
        foreach($group_users as $g){
            $group_id[] = $g->group_id;
        }

        ci()->session->set_userdata(APConstants::SESSION_MOBILE_GROUP_USERS_ROLE, $group_id);
        ci()->session->set_userdata(APConstants::SESSION_GROUP_USERS_ROLE, $group_id);
        
        return true;
    }

    public static function request_reset_pass($email)
    {
        ci()->load->library('users/users_api');
        ci()->load->library('email/email_api');
        ci()->lang->load('users/user');
        $response = array(
            "status"  => false,
            "message" => ""
        );

        $user_meta = users_api::getUser($email);
        if (!empty($user_meta))
        {
            
            $from_email = '';
            $to_email = $user_meta->email;
            $slug = APConstants::user_reset_password;
            $email_template = email_api::getEmail($slug);

            if(empty($email_template)){
                
                $response["status"]  = false;
                $response["message"] = lang('forgot_pass_empty_email_template');
                return $response;
            }
            $key = users_api::forgotten_password($user_meta->email);
            $full_url = APContext::getAssetPath()."users/reset_pass_complete?email=".$user_meta->email."&key=".$key;
            $data = array(
                "slug" => APConstants::user_reset_password,
                "to_email" => $to_email,
                // Replace content
                "full_name" => ucfirst($user_meta->display_name),
                "email"     => $user_meta->email,
                "full_url"  => $full_url
            );
            // Send email
            MailUtils::sendEmailByTemplate($data);
            
            $response["status"]  = true;
            $response["message"] = lang('reset_pass_send_email_successful');
            return $response;
        }
        else
        {
            $response["status"] = false;
            $response["message"] = lang('forgot_pass_account_not_found');
            
            return $response;
        }
        
    }
    
    /**
     * Method for handling different form actions
     */
    public static function change_pass($user_id, $password) {
      
        $update_data = array();
        $update_data['password'] = $password;
        $ion_auth = new Ion_auth;
        $result = $ion_auth->update_user($user_id, $update_data);
        
        $data_response = array();
        
        if ($result) {
            
            $message = $ion_auth->messages();
            $data_response['status'] = true;
            $data_response['message'] = $message;
            return $data_response;
        }
        else {
            
            $message = $ion_auth->errors();
            $data_response['status'] = false;
            $data_response['message'] = $message;
            return $data_response;
            
        }
        
    }


}