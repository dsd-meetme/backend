<?php

namespace plunner\Http\Middleware;

use Doctrine\Common\Util\Debug;
use Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class GetUserAndRefresh extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     * If an user mode is set I don't check custom
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  String $custom custom claims that must be equals (format: key1-ele1;key2-ele2)
     * @return mixed
     */
    public function handle($request, \Closure $next, $custom = '')
    {
        $custom = $this->convertToArray($custom);
        Debug::info('headers: '.implode('-',$request->all()));
        if($token = $this->auth->setRequest($request)->getToken()) {
        }else if ($this->auth->getUserModel()){
            $token = $this->auth->fromUser($this->auth->getUserModel(), $custom);
        }else {
            return $this->respond('tymon.jwt.absent', 'token_not_provided', 401);
        }

        try {
            $user = $this->auth->authenticate($token, $custom);
        } catch (TokenExpiredException $e) {
            return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
        } catch(InvalidClaimException $e) {
            return $this->respond('tymon.jwt.invalid', 'claim_invalid', $e->getStatusCode(), [$e]);
        } catch (JWTException $e) {
            return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
        }

        if (! $user) {
            return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
        }

        /**
         * refresh
         */

        $response = $next($request);


        $this->events->fire('tymon.jwt.valid', $user);

        try {
            $newToken = $this->auth->refresh($token, $custom);
        } catch (TokenExpiredException $e) {
            return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
        } catch (JWTException $e) {
            return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
        }

        // send the refreshed token back to the client
        $response->headers->set('Authorization', 'Bearer ' . $newToken);

        return $response;
    }
}
