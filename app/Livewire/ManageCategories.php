<?php

namespace App\Livewire;

use App\Models\DocumentCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ManageCategories extends Component
{
    use WithPagination;
    use Toast;

    public bool $categoryModal = false;
    public bool $editMode = false;
    public $search = '';
    public $category_name;
    public $category_id;

    protected $rules = [
        'category_name' => 'required|string|max:255',
    ];

    public function render()
    {
        $categories = DocumentCategory::where('category_name', 'LIKE', '%' . $this->search . '%')
                                    ->withCount('documents')
                                    ->paginate(10);

        $headers = [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'category_name', 'label' => 'Nom de la Catégorie'],
            ['key' => 'documents_count', 'label' => 'Nombre de documents'],
            // ['key' => '', 'label' => 'Actions'],
        ];

        return view('livewire.manage-categories', [
            'categories' => $categories,
            'headers' => $headers,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            DocumentCategory::find($this->category_id)->update(['category_name' => $this->category_name]);
            $this->success('Catégorie mise à jour avec succès.');
        } else {
            DocumentCategory::create(['category_name' => $this->category_name]);
            $this->success('Catégorie créée avec succès.');
        }

        $this->resetFields();
        $this->categoryModal = false;
    }

    public function showModal()
    {
        $this->resetFields();
        $this->categoryModal = true;
    }

    public function edit($id)
    {
        $category = DocumentCategory::findOrFail($id);
        $this->category_id = $id;
        $this->category_name = $category->category_name;
        $this->editMode = true;
        $this->categoryModal = true;
    }

    public function delete($id)
    {
        DocumentCategory::findOrFail($id)->delete();
        $this->error("Category supprimé.");
    }

    private function resetFields()
    {
        $this->category_name = '';
        $this->category_id = null;
        $this->editMode = false;
    }
}


