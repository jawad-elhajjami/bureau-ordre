
<div class="container">
    <div>
        <!-- Loading animation -->
        <div class="relative flex items-center justify-center">
            <x-mary-loading 
                class="text-primary loading-lg absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 h-screen" 
                wire:loading 
                wire:target="userModal" 
            />
        </div>

        <x-mary-header title="{{ __('messages.users_headers_title') }}" subtitle="{{ __('messages.manage_users_subtitle') }}">
            <x-slot:middle class="!justify-end">
                <x-mary-input icon="o-bolt" wire:model.live="search" placeholder="{{ __('messages.search_placeholder') }}" />
            </x-slot:middle>
            <x-slot:actions>
                <x-mary-button icon="o-plus" class="btn-primary" spinner @click="$wire.showModal()" />
            </x-slot:actions>
        </x-mary-header>
        <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg p-4">
            <!-- Users table -->
 
            {{-- You can use any `$wire.METHOD` on `@row-click` --}}
            @if(count($users) > 0)
            <x-mary-table 
                :headers="$headers" 
                :rows="$users" 
                @row-click="$event.detail.id === {{ auth()->user()->id }} ? window.location.href = '{{ route('profile.show') }}' : $wire.edit($event.detail.id)" 
                striped 
                with-pagination>
                {{-- Special `actions` slot --}}

                    @scope('cell_name', $user)
                        <div class="flex gap-1">
                            @if(auth()->user()->id == $user->id)
                                <p>{{ $user->name }}</p><x-mary-badge value="Vous" class="badge-warning" />
                                @else
                                <p>{{ $user->name }}</p>
                            @endif
                        </div>
                    @endscope

                    @scope('cell_role.name', $user)
                        <x-mary-badge :value="$user->role->name" class="{{ $user->role->name === 'admin' ? 'bg-red-400 text-white' : 'badge-primary'  }}" />
                    @endscope
                    @scope('cell_service', $user)
                        {{ $user->service ? $user->service->name : "N/A" }}
                    @endscope

                    @scope('actions', $user) 
                    <div class="flex gap-1">
                        @php
                            $confirmDeletionOfUserMessage = __('messages.confirm_deletion_of_user_message');
                        @endphp
                        @if($user->id !== auth()->user()->id && $user->non_deletable == 0)
                            <x-mary-button icon="o-trash" wire:click="delete({{ $user->id }})" wire:confirm="{{ $confirmDeletionOfUserMessage }}" spinner class="btn-sm btn-ghost text-red-600"  />
                        @endif
                    </div>
                    @endscope
            </x-mary-table>
            @else
                @livewire('partials/no-users-to-show')
            @endif
            
        
        </div>
    </div>

    <!-- Create user modal -->

    <x-mary-modal wire:model="userModal" title="{{ $editMode ? __('messages.edit_user_modal_title') : __('messages.add_user_modal_title') }}">
        <x-mary-form wire:submit="save">
            <div class="mb-4">
                <x-mary-input label="{{ __('messages.full_name_field_label') }}" icon="o-user"  wire:model="form.fullName" inline />
            </div>
            <div class="mb-4">
                <x-mary-input label="{{ __('messages.email_field_label') }}" type="email" icon="o-inbox"  wire:model="form.email" inline />
            </div>
            <div class="mb-4">
                <x-mary-input label="{{ __('messages.password_field_label') }}" type="password" icon="o-lock-closed"  wire:model="form.password" inline />
            </div>
            <div class="mb-4">
                <x-mary-input label="{{ __('messages.confirm_password_field_label') }}" icon="o-lock-closed" type="password"  wire:model="form.confirm_password" inline />
            </div>
            <div class="mb-4">
                
                @php
                    $roles = App\Models\Role::all();
                    $services = App\Models\Service::all();
                @endphp
                
                <x-mary-select
                    label="{{ __('messages.choose_role_field_label') }}"
                    placeholder="{{ __('messages.choose_role_field_label') }}"
                    :options="$roles"
                    wire:model="form.role_id"
                    inline
                />

            </div>
            <div class="mb-4">
                <x-mary-select
                    label="{{ __('messages.choose_service_field_label') }}"
                    :options="$services"
                    placeholder="{{ __('messages.choose_service_field_label') }}"
                    placeholder-value="0"
                    wire:model="form.service_id"
                    inline
                />
            </div>
        
            <x-slot:actions>
                <x-mary-button label="{{ __('messages.cancel_btn_text') }}" @click="$wire.userModal = false" />
                <x-mary-button label="{!! $editMode ? __('messages.edit_btn_text') : __('messages.create_btn_text') !!}" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    <x-mary-toast />  

</div>