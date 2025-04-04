<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserOwnerMapping extends Model
{
    public function getOwnerClients($owner_id){
        $map = $this->newQuery();
        $clients = $map->join('users as u','user_owner_mappings.user_id','=','u.id')
         ->where(['user_owner_mappings.owner_id'=>$owner_id])->get();
        // $clients = $map->where(['owner_id'=>$owner_id])->get();
        return $clients;
    }

    public function getOwnerClientsByType($client_type_id, $clinet_type){
        $map = $this->newQuery();
        $clients = $map->join('users as u','user_owner_mappings.user_id','=','u.id')
         	->where(['u.user_type_id'=>$client_type_id, 'client_type' => $clinet_type])
            ->paginate(Config('constants.adminDefaultPerPage'));
        return $clients;
    }
}
