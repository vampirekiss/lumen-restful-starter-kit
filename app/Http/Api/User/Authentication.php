<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Http\Api\User;

use App\Models\User\User;
use App\Restful\ApiController;
use App\Restful\RestfulRequest;
use Illuminate\Http\Response;

class Authentication extends ApiController
{
    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function doAction(RestfulRequest $request)
    {
        $rules = [
            'client_id' => 'required',
            'account'  => 'required',
            'password' => 'required'
        ];

        return $this->actionValidatePassOrFail('GET', $request, $rules, function () use ($request) {
            list($clientId, $account, $password, $remember) = [
                $request->input->get('client_id', ''),
                $request->input->get('account', ''),
                $request->input->get('password', ''),
                $request->input->getBoolean('remember', false)
            ];
            list($uid, $token) = $this->_authorize($clientId, $account, $password, $remember);
            if ($uid === null) {
                return $this->actionResultBuilder()->setStatusCode(Response::HTTP_UNAUTHORIZED)
                    ->build();
            }

            return $this->actionResultBuilder()->setResource([
                'uid' => $uid, 'token' => $token
            ])->build();
        });
    }

    /**
     * @param string $clientId
     * @param string $account
     * @param string $password
     * @param bool   $remember
     *
     * @return array [uid, token]
     */
    private function _authorize($clientId, $account, $password, $remember)
    {
        /** @var User $user */
        $user = User::ofAccount($account)->first();

        if (!$user || !$user->passwordIsCorrected($password)) {
            return [null, null];
        }

        return [$user->getAttribute('id'), uniqid()];
    }

}