<?php

namespace App\Livewire;

use App\Livewire\Forms\ServiceForm;
use App\Models\Service;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ManageServices extends Component
{

    use Toast;
    use WithPagination;

    public bool $serviceModal = false;
    public bool $editMode = false;
    public array $users_multi_ids = [];
    public $clickedServiceId = null;
    public $search = '';


    public ServiceForm $form;


    public function assignServiceToUsers(int $serviceId, array $usersIdsArray){
        User::where('service_id', $serviceId)->update(['service_id' => null]); // Unassign all users first
        foreach($usersIdsArray as $userId){
            $user = User::findOrFail($userId);
            $user->update([
                'service_id' => $serviceId
            ]);
        }
    }

    public function save(){
    
        if($this->editMode){
            $this->form->update();
            $this->assignServiceToUsers($this->clickedServiceId, $this->users_multi_ids);
            $this->editMode = false;
            $this->success("Service modifié avec succès !");
        }else{
            $this->form->store();
            // Retrieve the newly created service ID
            $newServiceId = $this->form->newServiceId;
            $this->assignServiceToUsers($newServiceId, $this->users_multi_ids);
            $this->success("Service crée avec succès !");
        }

        $this->serviceModal = false;
    }

    // delete service
    public function delete($id):void{
        try{
            $service = Service::findOrFail($id);
            if (Gate::denies('can-manage-services')) {
                abort(403, 'Unauthorized');
            }else{
                $service->delete();
                $this->error("Service supprimé");
            }
        } catch(Exception $e){
            $this->error($e->getMessage());
        }
    }


    public function showModal(){
        $this->form->reset();
        $this->reset('users_multi_ids');
        $this->editMode = false;
        $this->serviceModal = true;
    }

    // method to edit user data

    public function edit($id){
        $this->clickedServiceId = $id;
        $service = Service::find($id);
        $members = $service->members()->pluck('id')->toArray();
        $this->users_multi_ids = $members;
        $this->form->setService($service);
        $this->editMode = true;
        $this->serviceModal = true;
    }

    public function render()
    {
        $headers = [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Nom de service'],
            ['key' => 'members', 'label' => 'Membres'],
            ['key' => 'n_documents', 'label' => 'Nombre de documents'],
        ];
        $availableUsers = User::all();
        return view('livewire.manage-services',[
            'headers' => $headers,
            'availableUsers' => $availableUsers,
            'services' => Service::where('name', 'LIKE', '%' . $this->search . '%')->paginate(10),
        ]);
    }
}
