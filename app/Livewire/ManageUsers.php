<?php

namespace App\Livewire;

use App\Livewire\Forms\UserForm;
use App\Models\Service;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ManageUsers extends Component
{

    use WithPagination;
    use Toast;

    public bool $userModal = false;
    public bool $editMode = false;
    public $search = '';


    public UserForm $form;

    // method to save user data

    public function save(){
 
        if($this->editMode){
            $this->form->update();
            $this->editMode = false;
            $this->success("Utilisateur modifié avec succès !");
        }else{
            $this->form->store();
            $this->success("Utilisateur crée avec succès !");
        }

        $this->userModal = false;
    }


    public function showModal(){
        $this->form->reset();
        $this->form->role_id = 2;
        $this->editMode = false;
        $this->userModal = true;
    }

    // method to edit user data

    public function edit($id){
        $user = User::find($id);
        $this->form->setUser($user);
        $this->editMode = true;
        $this->userModal = true;
    }

    // method to delete user

    public function delete($id):void{
        try{
            $user = User::findOrFail($id);
            if (Gate::denies('can-manage-users')) {
                abort(403, 'Unauthorized');
            }else{
                $user->delete();
                $this->error("Utilisateur supprimé");
            }
        } catch(Exception $e){
            $this->error($e->getMessage());
        }
        
    }


    public function render()
    {   
        $users = User::paginate(10);
            
        $headers = [
            ['key' => 'id', 'label' => 'Identifiant'],
            ['key' => 'name', 'label' => 'Nom'],
            ['key' => 'email', 'label' => 'E-mail'],
            ['key' => 'role.name', 'label' => 'Role'],
            ['key' => 'service', 'label' => 'Service'],
        ];

        return view('livewire.manage-users',[
            'users' => User::where('name', 'LIKE', '%' . $this->search . '%')->paginate(10),
            'headers' => $headers,
            'services' => Service::all()
        ]);
    }


}
