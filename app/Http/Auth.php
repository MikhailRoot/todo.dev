<?php

namespace App\Http;


class Auth
{
    protected $_request;

    protected $_user=null;

    protected $Users;

    protected $Tokens;

    public function __construct(Request $request)
    {
        $this->_request=$request;

        $this->Users=[
            'admin'=>[
                'name'=>'admin',
                'password'=>'123'
            ]
        ];

        $this->Tokens=[];

        foreach($this->Users as &$user){

            $token=$this->calculateAuthTokenForUser($user['name']);
            $this->Tokens[$token]=$user;
        }

        $this->checkIfAuthorized();

    }

    private function checkIfAuthorized(){

        $authUserName = $this->_request->getServerVar('PHP_AUTH_USER');
        $authPassword = $this->_request->getServerVar('PHP_AUTH_PW');

        $authToken    = $this->_request->getAuthToken();

        if($authToken && array_key_exists($authToken, $this->Tokens))
        {
            $this->_user = $this->Tokens[$authToken]['name'];
            return true;
        }

        if(is_string($authPassword) && is_string($authUserName) ){

            if( array_key_exists($authUserName, $this->Users) &&  $this->Users[$authUserName]['password'] === $authPassword)
            {
                $this->_user=$this->Users[$authUserName]['name'];
                return true;
            }

        }
        return false;
    }

    public function makeRequestAuthorizationResponse():Response
    {
        $headers=[
            'WWW-Authenticate'=>'Basic realm="'.date('Y-m-d').'"',
        ];

        $basicAuthResponse=new Response('Please Login to Access',401,$headers);

        $basicAuthResponse->removeAuthTokenCookie();

        return $basicAuthResponse;
    }

    public function getAuthorizedUser()
    {
        return $this->_user;
    }

    public function isAuthorized()
    {
        return $this->checkIfAuthorized();
    }

    public function calculateAuthTokenForUser($username)
    {
        $password=$this->Users[$username]['password'];

        return sha1($username.':'.$password.':'.date('Y-m-d'));
    }


}