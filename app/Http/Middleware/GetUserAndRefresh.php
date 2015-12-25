<?php
/**
 * Created by PhpStorm.
 * User: Claudio Cardinale <cardi@thecsea.it>
 * Date: 24/12/15
 * Time: 22.53
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace plunner\Http\Middleware;


class GetUserAndRefresh extends \Tymon\JWTAuth\Middleware\GetUserAndRefresh
{
    public function handle($request, \Closure $next, $custom = '')
    {
        $remember = false;
        if($this->auth->setRequest($request)->getToken() && ($remember = $this->auth->getPayload()->get('remember')) &&
                $remember == 'true'){
            config(['jwt.ttl' =>'43200']); //30 days
        }

        //this to add the remember me mode field in the new token, but we have the custom check that is an useless
        //overhead
        $custom = $custom.';remember-'.$remember=='true'?'true':'false';
        return parent::handle($request, $next, $custom);
    }


}