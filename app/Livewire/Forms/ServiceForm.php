<?php

namespace App\Livewire\Forms;

use App\Models\Service;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ServiceForm extends Form
{
    public ?Service $service;

    #[Validate('required|min:3|max:255|string')] 
    public $name;
    public $newServiceId;

    public function setService(Service $service){
        $this->service = $service;
        $this->name = $service->name;
    }

    public function store(){
        
        // validate form
        $this->validate();

        // check if user type is "admin" before allowing him to create a service
        if (Gate::denies('can-manage-services')) {
            abort(403, 'Unauthorized');
        }
        else{
            $service = Service::create([
                'name' => $this->name,
            ]);
            // reset form fields
            $this->reset();
            $this->newServiceId = $service->id;
        }
    }

    public function update(){

        // validate form
        $this->validate();

        // check if user type is "admin" before allowing him to update a service
        if (Gate::denies('can-manage-services')) {
            abort(403, 'Unauthorized');
        }
        else{
            $this->service->update([
                'name' => $this->name,
            ]);
            // reset form fields
            $this->reset();
        } 
    }


}
