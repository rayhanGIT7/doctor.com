<?php

namespace App\Controllers\Admin;

use App\Core\Validator;
use App\Models\Category;

class CategoryController extends AdminController
{
    private Category $categories;

    public function __construct()
    {
        parent::__construct();
        $this->categories = new Category();
    }

    public function index(): void
    {
        $this->view('admin/categories/index', [
            'title'      => 'Doctor Categories',
            'categories' => $this->categories->all(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/categories/form', ['title' => 'Add Category', 'category' => null]);
    }

    public function store(): void
    {
        $this->categories->create($this->validated());

        $this->success('Category added successfully.');
        $this->redirect('admin/categories');
    }

    public function edit(int $id): void
    {
        $this->view('admin/categories/form', ['title' => 'Edit Category', 'category' => $this->findOrFail($id)]);
    }

    public function update(int $id): void
    {
        $this->findOrFail($id);
        $this->categories->update($id, $this->validated($id));

        $this->success('Category updated successfully.');
        $this->redirect('admin/categories');
    }

    public function toggleStatus(int $id): void
    {
        $category = $this->findOrFail($id);
        $status   = $category['status'] === 'active' ? 'inactive' : 'active';
        $this->categories->setStatus($id, $status);

        $this->success($category['name'] . ' is now ' . $status . '.');
        $this->back();
    }

    private function findOrFail(int $id): array
    {
        $category = $this->categories->find($id);
        if (!$category) {
            abort(404);
        }

        return $category;
    }

    private function validated(int $id = 0): array
    {
        $validator = new Validator($_POST);
        $validator->required('name', 'status')
            ->max('name', 100)
            ->max('description', 255)
            ->in('status', ['active', 'inactive']);

        if ($this->categories->nameExists($this->input('name'), $id)) {
            $validator->addError('name', 'This category already exists.');
        }
        if ($validator->fails()) {
            $this->backWithErrors($validator->errors());
        }

        return [
            'name'        => $this->input('name'),
            'description' => $this->input('description') ?: null,
            'status'      => $this->input('status'),
        ];
    }
}
