<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Mary\Traits\Toast;

class UserForm extends Form
{
    public ?User $user;

    // form fields

    #[Validate('required|min:3|max:50|string')] 
    public $fullName;

    #[Validate('required|string|email|unique:users,email,')] 
    public $email;

    #[Validate('required|min:8|max:50')] 
    public $password;

    #[Validate('required|min:8|max:50|same:password')] 
    public $confirm_password;

    #[Validate('required|exists:roles,id')] 
    public $role_id;

    // #[Validate('exists:services,id')] 
    public $service_id;


    public function setUser(User $user){
        $this->user = $user;
        $this->fullName = $user->name;
        $this->email = $user->email;
        $this->role_id = $user->role_id;
        $this->service_id = $user->service_id;
    }


    public function store(){
        
        // validate form
        $this->validate();

        // check if user type is "admin" before allowing him to create a user
        if (Gate::denies('can-manage-users')) {
            abort(403, 'Unauthorized');
        }
        else{
            User::create([
                'name' => $this->fullName,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role_id' => $this->role_id,
                'service_id' => $this->service_id
            ]);
            // reset form fields
            $this->reset();
        } 
    }


    public function update()
    {
        // validate form
        $validated = $this->validate([
            'fullName' => 'required|min:3|max:50|string',
            'email' => 'required|string',
            'password' => 'nullable|min:8|max:50',
            'confirm_password' => 'nullable|min:8|max:50|same:password',
            'role_id' => 'required|exists:roles,id',
            // 'service_id' => 'exists:services,id'
        ]);

        // check if user type is "admin" before allowing him to create a user
        if (Gate::denies('can-manage-users')) {
            abort(403, 'Unauthorized');
        }

        // Assemble the data array for update
        $data = [
            'name' => $this->fullName,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'service_id' => $this->service_id
        ];

        // Conditionally add the password if it is specified
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        // Update the user with the assembled data
        $this->user->update($data);

        // reset form fields
        $this->reset();
    }


}
