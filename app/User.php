<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;

use Zoha\Metable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, Metable;

    const ACTIVE = '1';
    const INACTIVE = '2';

    const OLIVE = '1';
    const MISSGUIDED = '2';
    const NORMAL = '3';
    const SHOPIFY = '4';
    const JADED = '5';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name', 'email', 'password', 'phone', 'user_type_id', 'token', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key-value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAssignUserByTypeId($user_type_id){
        $user = $this->newQuery();
        $users = $user->join('user_owner_mappings as um','users.id', '=', 'um.user_id')
            ->join('users as owner','owner.id','=','um.owner_id')
            ->select('users.name as user_name','owner.name as owner_name','um.id','um.user_id','um.owner_id')
            ->where(['users.user_type_id'=>$user_type_id,'users.status'=>1])
            ->paginate(Config('constants.adminDefaultPerPage'));
        return $users;
    }

    public function getClientUser($user_type_id){
        $user = $this->newQuery();

        $user->where(['users.user_type_id'=>$user_type_id]);
        
        $user->orderBy('users.id','desc');
        return $user->paginate(Config('constants.adminDefaultPerPage'));        
    }

    public function getUserWithOwnerByTypeId($user_type_id,$owner_id=NUll, $request = null){
        $user = $this->newQuery();
        $user->leftJoin('user_owner_mappings as um','users.id', '=', 'um.user_id')
            ->leftJoin('users as owner','owner.id','=','um.owner_id')
            ->select('users.*','owner.name as owner_name','um.owner_id');
        if(!empty($user_type_id) && !empty($owner_id)){
            $user->where(['users.user_type_id'=>$user_type_id,'um.owner_id'=>$owner_id]);
        }else{
            if(empty($owner_id)){
                $user->where(['users.user_type_id'=>$user_type_id]);
            }
            
            if(!empty($owner_id)){
                $user->where(['um.owner_id'=>$owner_id]);
            }
        }

        if(!empty($request)){
            if($request->has('name') && !empty($request->name)){
                $user->where('users.name' , 'like' , '%'.$request->name.'%');
            }

            if($request->has('email') && !empty($request->email)){
                $user->where('users.email' , 'like' , '%'.$request->email.'%');
            }
        }
        
        $user->orderBy('users.id','desc');
        
        return $user->paginate(Config('constants.adminDefaultPerPage'));
        
    }

    public function getOwnerByUserId($user_id){
        $user = $this->newQuery();
        $user->join('user_owner_mappings as um','users.id', '=', 'um.user_id')
            ->join('users as owner','owner.id','=','um.owner_id')
            ->select('users.*','owner.name as owner_name','um.owner_id');
        $user->where(['um.user_id'=>$user_id]);
        $user->orderBy('users.id','desc');
        return $user->first();
        
    }

    public function getWarehouse(){
        return $this->hasMany('App\Models\Warehouse');
    }

    public function addresses()
    {
        return $this->hasMany(Models\Address::class);
    }
}
