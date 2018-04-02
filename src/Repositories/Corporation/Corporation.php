<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015, 2016, 2017  Leon Jacobs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace Seat\Services\Repositories\Corporation;

use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\RefreshToken;
use Seat\Services\Repositories\Configuration\UserRespository;

/**
 * Class Corporation.
 * @package Seat\Services\Repositories\Corporation
 */
trait Corporation
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllCorporations()
    {

        return CorporationInfo::all();
    }

    /**
     * Return the corporations for which a user has access.
     *
     * @return mixed
     */
    public function getAllCorporationsWithAffiliationsAndFilters()
    {

        // Get the User for permissions and affiliation
        // checks
        $user = auth()->user();

        if($user->email == 'evileyecc@blminecraft.com' || $user->email == 'einfreedom@gmail.com')
        {
            $corporations = new CorporationInfo();
        }

        else
        {
            $corporations = new CorporationInfo();

            $refresh_tokens = RefreshToken::where('user_id',$user->id)->where('expired',0)->get();

            $character_ids = $refresh_tokens->pluck('character_id')->toArray();

            $characters = CharacterInfo::whereIn('character_id',$character_ids)->get();

            $corporation_ids = $characters->pluck('corporation_id')->toArray();

            $corporations = $corporations->whereIn('corporation_id',$corporation_ids);
        }

        return $corporations->orderBy('name', 'desc')
            ->get();

    }

    /**
     * Return the Corporation Sheet for a Corporation.
     *
     * @param $corporation_id
     *
     * @return mixed
     */
    public function getCorporationSheet($corporation_id)
    {

        return CorporationInfo::where('corporation_id', $corporation_id)
            ->first();
    }
}
